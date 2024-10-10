<?php

namespace Hoks\CMSGSC\Controllers;

use Hoks\CMSGSC\Models\SearchConsoleQuery;
use Hoks\CMSGSC\Models\SearchConsoleQueryStatuses;
use Illuminate\Http\Request;
use App\Http\Resources\JsonResource;
use Hoks\CMSGSC\Models\SearchConsoleQueryPage;
use Google\Service\SearchConsole;
use Google\Service\SearchConsole\SearchAnalyticsQueryRequest;
use Google\Client;
use App\Http\Controllers\Controller;

class GoogleSearchConsoleController extends Controller
{
    /**
     * displays index page
     */
    public function index($activeWebsite = false){
        return view('google_search_console.index',[
            'activeWebsite' => $activeWebsite
        ]);
    }

    /**
     * datatable
     */
    public function datatable(){
        $activeWebsite = request()->activeWebsite;
        if(isset($activeWebsite)){
            $queries = SearchConsoleQuery::where('site_id',$activeWebsite);
            // $existingQueries = SearchConsoleQueryStatuses::where('site_id',$activeWebsite)->get();
            // $excludedQueries = [];
            // foreach($existingQueries as $existingQuery){
            //     if($existingQuery->exclude == 1){
            //         $excludedQueries[] = $existingQuery->query;
            //     }
            // }
            $datatable = datatables($queries)->addColumn('actions',function($row){
                return view('google_search_console.partials.actions',['entity' => $row]);
            })->editColumn('ctr',function($row){
                return $row->ctr." (".($row->ctr*100)."%)";
            })->editColumn('position',function($row){
                return round($row->position,2);
            });

            $datatable->rawColumns(['actions']);

            // $datatable->filter(function()use($queries,$excludedQueries){
            //     $queries->whereNotIn('query',$excludedQueries);
            // });

            return $datatable->make();
        }
    }

    /**
     * exclude query (give some query status)
     */
    public function exclude(SearchConsoleQuery $query){
        if(isset($query)){
            $newQueryWithStatus = new SearchConsoleQueryStatuses();
            $data = [
                'site_id' => $query->site_id,
                'query' => $query->query,
                'exclude' => 1
            ];
            
            $newQueryWithStatus->fill($data);
            $newQueryWithStatus->save();

            $query->update([
                'status' => SearchConsoleQuery::STATUS_EXCLUDED
            ]);
          
            if (request()->wantsJson()) {
                return JsonResource::make()->withSuccess(__('Query has been successfully excluded!'));
            }
            return redirect()->route('google_search_console.index')->withSystemSuccess(__('Query has been successfully excluded!'));

        }
    }

    /**
     * display all pages for one query
     */
    public function pages(SearchConsoleQuery $query){
        //get all existing pages for query
        $queryPages = SearchConsoleQueryPage::where('query_id',$query->id)->get();
        if(isset($queryPages) && !empty($queryPages) && count($queryPages) > 0){
            //if there are already pages for query, we will return query
            
        }else{
            //if there are no pages for query, we get them and then return query

            $queryTitle = $query->query;
            //create client for gsc api
            $client = $this->createClient();
            //form request
            $request = new SearchAnalyticsQueryRequest();
            $searchConsole = new SearchConsole($client);

            //form dates for request
            $dateFrom = now()->subDays(config('gsc-cms.days_before_now'))->format('Y-m-d');
            $dateTo = now()->format('Y-m-d');
            $request->setStartDate($dateFrom);
            $request->setEndDate($dateTo);
            //we want pages for specific query
            $request->setDimensions(['page']);
                $request->setDimensionFilterGroups([
                    [
                        'filters' => [
                            [
                                'dimension' => 'query',
                                'operator' => 'equals',
                                'expression' => $queryTitle,
                            ]
                        ]
                    ]
                ]);
            //find domain for gsc api request
            $websites = config('gsc-cms.websites_domains');
            $domain = false;
            foreach($websites as $website){
                if($website['site_id'] == $query->site_id){
                    $domain = $website['domain'];
                }            
            }

            if($domain){
                $response = $searchConsole->searchanalytics->query($domain, $request);
                if($response){
                    foreach($response as $page){
                        $newPage = new SearchConsoleQueryPage();
                        $data = [
                            'query_id' => $query->id,
                            'page' => $page->keys[0],
                            'clicks' => $page->clicks,
                            'impressions' => $page->impressions,
                            'ctr' => $page->ctr,
                            'position' => $page->position,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                        $newPage->fill($data);
                        $newPage->save();
                    }
                }
            }
        }

        return view('google_search_console.pages',['query' => $query]);


    }

    protected function createClient(){
        //instantiate google client
        $client = new Client();
        //set the path to Oauth credentials
        // $client->setAuthConfig(public_path('oauth.json'));
        $client->useApplicationDefaultCredentials();
        //set the scope
        $client->setScopes(SearchConsole::WEBMASTERS_READONLY);
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

        return $client;
    }

    /**
     * datatable for pages for specific query
     */
    public function pagesDatatable(){
        $queryId = request()->query_id;
        if(isset($queryId) && !empty($queryId)){
            $queryExists = SearchConsoleQuery::where('id',$queryId)->first();
            if(isset($queryExists) && !empty($queryExists)){
                $pages = SearchConsoleQueryPage::where('query_id',$queryId);
        
                $datatable = datatables($pages)->editColumn('ctr',function($row){
                    return $row->ctr." (".($row->ctr*100)."%)";
                })->editColumn('position',function($row){
                    return round($row->position,2);
                });
        
                return $datatable->make();
            }
          
        }
        
    }

    /**
     * mark query as fixed (give some query status EXCLUDED)
     */
    public function markAsFixed(SearchConsoleQuery $query){
        if(isset($query)){
            $newQueryWithStatus = new SearchConsoleQueryStatuses();
            $data = [
                'site_id' => $query->site_id,
                'query' => $query->query,
                'fixed' => 1
            ];
            
            $newQueryWithStatus->fill($data);
            $newQueryWithStatus->save();

            $query->update([
                'status' => SearchConsoleQuery::STATUS_FIXED
            ]);
          
            if (request()->wantsJson()) {
                return JsonResource::make()->withSuccess(__('Query has been successfully marked as fixed!'));
            }
            return redirect()->route('google_search_console.index')->withSystemSuccess(__('Query has been successfully marked as fixed!'));

        }

    }

     
}
