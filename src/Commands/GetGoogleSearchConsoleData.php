<?php

namespace Hoks\CMSGSC\Commands;

use Hoks\CMSGSC\Models\SearchConsoleQueryStatuses;
use Hoks\CMSGSC\Models\SearchConsoleQuery;
use Illuminate\Console\Command;
use Google\Client;
use Google\Service\SearchConsole\SearchAnalyticsQueryRequest;
use Google\Service\SearchConsole;
use Illuminate\Support\Facades\DB;

class GetGoogleSearchConsoleData extends Command
{
    protected $queriesWithStatuses;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'get:gsc-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get Google Search Console Data for specific project';

    /**
     * set queries with statuses
     */
    protected function setQueriesWithStatuses(){
        $this->queriesWithStatuses = SearchConsoleQueryStatuses::get();
      
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        //first we truncate tables search_console_queries and search_console_query_pages
        DB::table('search_console_queries')->truncate();
        DB::table('search_console_query_pages')->truncate();

        //set queries with status
        $this->setQueriesWithStatuses();
    
        //instantiate google client
        $client = new Client();
        //set the path to Oauth credentials
        // $client->setAuthConfig(public_path('oauth.json'));
        $client->useApplicationDefaultCredentials();
        //set the scope
        $client->setScopes(SearchConsole::WEBMASTERS_READONLY);
        $searchConsole = new SearchConsole($client);
        //Check if access token expired
        if ($client->isAccessTokenExpired()) {
            // The access token will be automatically fetched
            $client->fetchAccessTokenWithAssertion();
        }
        //----use this part of code to test connection and see available sites
            // $sites = $searchConsole->sites->listSites();
            // dd($sites);
        ///-------------------------------------------------------------------

        //form the request
        $request = new SearchAnalyticsQueryRequest();

        $dateFrom = now()->subDays(config('gsc-cms.days_before_now'))->format('Y-m-d');
        $dateTo = now()->format('Y-m-d');

        $request->setStartDate($dateFrom);
        $request->setEndDate($dateTo);
        $request->setDimensions(['query']);
       

        $websites = config('gsc-cms.websites_domains');
        foreach($websites as $website){
            $response = $searchConsole->searchanalytics->query($website['domain'], $request);
            if($response){
                //delete previous queries
                //to be done!!!
    
                //enter new queries
                foreach($response as $query){
                    $newQuery = new SearchConsoleQuery();
                    $data = [
                        'site_id' => $website['site_id'],
                        'query' => $query->keys[0],
                        'clicks' => $query->clicks,
                        'impressions' => $query->impressions,
                        'ctr' => $query->ctr,
                        'position' => $query->position,
                        'excluded' => $this->checkForQueryStatus($query->keys[0],'excluded'),
                        'fixed' => $this->checkForQueryStatus($query->keys[0],'fixed'),
                        'critical' => $this->checkIfCritical($query),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                    $newQuery->fill($data);
                    $newQuery->save();
                }
            }
        }
        
        
    }

     /**
     * checks for query status flags (if query should have flag excluded or fixed etc.)
     */
    protected function checkForQueryStatus($query,$status){
        $queriesWithStatuses = $this->queriesWithStatuses;
        $finalStatus = 0;
        foreach($queriesWithStatuses as $queryWithStatus){
            if($queryWithStatus->query == $query){
                if($status == 'excluded'){
                    if($queryWithStatus->excluded == 1){
                        $finalStatus = 1;
                    }
                }elseif($status == 'fixed'){
                    if($queryWithStatus->fixed == 1){
                        $finalStatus = 1;
                    }
                }
            }
        }
        return $finalStatus;
    }

    /**
     * determine if query is critical
     * high number of impressions and low ctr
     */
    protected function checkIfCritical($query){
        $ctrThreshold = config('gsc-cms.low_ctr_value');
        $impressionsThreshold = config('gsc-cms.high_impressions_value');

        $status = 0;
        if($query->ctr <= $ctrThreshold){
            if($query->impressions >= $impressionsThreshold){
                $status = 1;
            }
        }

        return $status;

    }
}
