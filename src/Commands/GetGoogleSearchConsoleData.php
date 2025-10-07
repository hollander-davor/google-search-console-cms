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
    protected function setQueriesWithStatuses($siteId){
        $this->queriesWithStatuses = SearchConsoleQueryStatuses::where('site_id',$siteId)->get();

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

        $daysBeforeNow = config('gsc-cms.days_before_now');
        

        foreach($daysBeforeNow as $daysBeforeNowElement){
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

            $dateFrom = now()->subDays($daysBeforeNowElement)->format('Y-m-d');
            $dateTo = now()->format('Y-m-d');

            $request->setStartDate($dateFrom);
            $request->setEndDate($dateTo);
            $request->setDimensions(['query']);


            $websites = config('gsc-cms.websites_domains');
            foreach($websites as $website){
                //set queries with status
                $this->setQueriesWithStatuses($website['site_id']);
                $response = $searchConsole->searchanalytics->query($website['domain'], $request);
                if($response){
                    //delete previous queries
                    //to be done!!!

                    $pattern = '/\b(delo(\.si)?|(ona\s?plus|onaplus(\.si)?)|slovenske?\s*novice(\.si)?|delo(in)?dom(\.si)?|odprta?k?kuhinja(\.si)?|delosi|slovenskene\s*novice|ona\+|oprtakuhinja|odrta\s*kuhinja|(naslovnica|naslovna)|(portal|časopis|revija)|(pdf|epaper)|(prijava|registracija|kontakt)|(oglasi?|oglasnik)|spored|(vreme|horoskop)(\s*danes)?|sudoku|križanka|napovednik|delo\s*(novice|naslovnica|pdf\s*izdaja)|slovenske\s*novice\s*(danes|naslovnica|epaper)|ona\s*plus\s*revija|odprta\s*kuhinja\s*recepti|(današnje|najnovejše|vse|jutranje|popoldanske|dnevne)\s*novice|breaking\s*news|news\s*slovenia|splet(n[iy])?\s*(portal|novice)|online\s*časopis|mali\s*oglasi\s*delo|oglasi\s*slovenske\s*novice|tv\s*spored(\s*(delo|slovenske\s*novice))?)\b/iu';

                    //enter new queries
                    foreach($response as $query){
                        $newQuery = new SearchConsoleQuery();
                        $data = [
                            'site_id' => $website['site_id'],
                            'query_status_id' => $this->checkForQueryStatus($query->keys[0]),
                            'query' => $query->keys[0],
                            'clicks' => $query->clicks,
                            'impressions' => $query->impressions,
                            'ctr' => $query->ctr,
                            'position' => $query->position,
                            'days_old' => $daysBeforeNowElement,
                            // 'excluded' => $this->checkForQueryStatus($query->keys[0],'excluded'),
                            // 'fixed' => $this->checkForQueryStatus($query->keys[0],'fixed'),
                            'critical' => $this->checkIfCritical($query),
                            'low_hanging_fruit' => $this->checkIfLHF($query),
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];


                        $newQuery->fill($data);
                        $newQuery->save();

                        if (preg_match($pattern, $query->keys[0])) {

                            $queryStatus = new SearchConsoleQueryStatuses();
                            $data['excluded'] = 1;
                            $queryStatus->fill($data);
                            $queryStatus->save();

                            $newQuery->update([
                                'query_status_id' => $queryStatus->id
                            ]);
                        }

                    }

                }
            }

        }
        
        


    }

     /**
     * checks for query status flags (if query should have flag excluded or fixed etc.)
     */
    protected function checkForQueryStatus($query){
        $queriesWithStatuses = $this->queriesWithStatuses;
        // $finalStatus = 0;
        foreach($queriesWithStatuses as $queryWithStatus){
            if($queryWithStatus->query == $query){
                // if($status == 'excluded'){
                //     if($queryWithStatus->excluded == 1){
                //         $finalStatus = 1;
                //     }
                // }elseif($status == 'fixed'){
                //     if($queryWithStatus->fixed == 1){
                //         $finalStatus = 1;
                //     }
                // }
                return $queryWithStatus->id;
            }
        }
        return 0;
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

    /**
     * determine if query is low hanging fruit
     *
     */
    protected function checkIfLHF($query){
        $lowThreshold = config('gsc-cms.low_lhf_value');
        $highThreshold = config('gsc-cms.high_lhf_value');

        $status = 0;
        if($query->position >= $lowThreshold){
            if($query->position <= $highThreshold){
                $status = 1;
            }
        }

        return $status;

    }
}