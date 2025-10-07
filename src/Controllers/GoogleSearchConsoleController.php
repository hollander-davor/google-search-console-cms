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
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class GoogleSearchConsoleController extends Controller
{
    /**
     * displays index page
     */
    public function index($activeWebsite = false)
    {
        $maxValue = 10000;
        if ($activeWebsite) {
            $maxImpressionQuery = SearchConsoleQuery::where('site_id', $activeWebsite)->orderBy('impressions', 'desc')->first();
            if (isset($maxImpressionQuery)) {
                $maxValue = $maxImpressionQuery->impressions;
            } else {
                $maxValue = 10000;
            }
        }
        $daysOld = config('gsc-cms.days_before_now');

        return view('google_search_console.index', [
            'activeWebsite' => $activeWebsite,
            'maxValue' => $maxValue,
            'daysOld' => $daysOld
        ]);
    }

    /**
     * datatable
     */
    public function datatable()
    {
        $activeWebsite = request()->activeWebsite;
        if (isset($activeWebsite)) {
            $queries = SearchConsoleQuery::with('queryStatus')->where('site_id', $activeWebsite);

            $datatable = datatables($queries)->addColumn('actions', function ($row) {
                return view('google_search_console.partials.actions', ['entity' => $row->queryStatus, 'critical' => $row->critical, 'lhf' => $row->low_hanging_fruit, 'query' => $row]);
            })->editColumn('ctr', function ($row) {
                return $row->ctr . " (" . ($row->ctr * 100) . "%)";
            })->editColumn('position', function ($row) {
                return round($row->position, 2);
            })->addColumn('delegated_to', function ($row) {
                if(isset($row->queryStatus->delegated) && $row->queryStatus->delegated == 1) {
                    $userToDelegate = User::where('id', $row->queryStatus->slave_id)->first();
                    return $userToDelegate->first_name . ' ' . $userToDelegate->last_name;
                }else {
                    return '';
                }
            })->editColumn('answer', function ($row) {
                return $row->queryStatus->slave_comment ?? '';
            })
            ->editColumn('query_statuses', function ($row) {
                if(!empty($row->queryStatus->slave_status)) {
                    switch ($row->queryStatus->slave_status) {
                        case SearchConsoleQueryStatuses::SLAVE_STATUS_DELIVERED:
                            return config('gsc-cms.delivered_status');
                            break;
                        case SearchConsoleQueryStatuses::SLAVE_STATUS_SEEN:
                            return config('gsc-cms.seen_status');
                            break;
                        case SearchConsoleQueryStatuses::SLAVE_STATUS_DONE:
                            return config('gsc-cms.done_status');
                            break;
                        case SearchConsoleQueryStatuses::SLAVE_STATUS_DELAYED:
                            return config('gsc-cms.delayed_status');
                            break;
                        case SearchConsoleQueryStatuses::SLAVE_STATUS_IN_PROGRESS:
                            return config('gsc-cms.in_progress_status');
                            break;
                        default:
                            return '';
                            break;
                    }
                }
            });

            $datatable->rawColumns(['actions']);

            $datatable->filter(function () use ($queries) {
                //search filter
                if (request()->has('search') && isset(request()->get('search')['value'])) {
                    $searchString = request()->get('search')['value'];
                    $queries->where('query', 'LIKE', '%' . $searchString . '%');
                }

                //filter by critical status
                //if value is 'all', we will show both true and false
                if (isset(request()->criticalStatus)) {
                    $criticalStatus = request()->criticalStatus;
                    if ($criticalStatus == 'true') {
                        //show only critical
                        $queries->where('critical', 1);
                    } elseif ($criticalStatus == 'false') {
                        //show those that are not critical
                        $queries->where('critical', 0);
                    }
                }

                $slider1_min = request()->input('slider1_min');
                $slider1_max = request()->input('slider1_max');

                $slider2_min = request()->input('slider2_min');
                $slider2_max = request()->input('slider2_max');

                $slider3_min = request()->input('slider3_min');
                $slider3_max = request()->input('slider3_max');

                $slider4_min = request()->input('slider4_min');
                $slider4_max = request()->input('slider4_max');

                if (!is_null($slider1_min) && !is_null($slider1_max)) {
                    $queries->whereBetween('clicks', [$slider1_min,  $slider1_max]);
                }

                if (!is_null($slider2_min) && !is_null($slider2_max)) {
                    $queries->whereBetween('impressions', [$slider2_min,  $slider2_max]);
                }

                if (!is_null($slider3_min) && !is_null($slider3_max)) {
                    $queries->whereBetween('ctr', [$slider3_min,  $slider3_max]);
                }

                if (!is_null($slider4_min) && !is_null($slider4_max)) {
                    $queries->whereBetween('position', [$slider4_min,  $slider4_max]);
                }

                //filter by days old
                //if value is 'all', we will all
                if (isset(request()->daysOld)) {
                    $daysOld = request()->daysOld;
                    if($daysOld == 1){
                        $queries->where('days_old', 1);
                    }elseif($daysOld == 3){
                        $queries->where('days_old', 3);
                    }
                    elseif($daysOld == 7){
                        $queries->where('days_old', 7);
                    }
                }

                //filter by lhf status
                //if value is 'all', we will show both true and false
                if (isset(request()->lhfStatus)) {
                    $lhfStatus = request()->lhfStatus;
                    if ($lhfStatus == 'true') {
                        //show only critical
                        $queries->where('low_hanging_fruit', 1);
                    } elseif ($lhfStatus == 'false') {
                        //show those that are not low hanging fruit
                        $queries->where('low_hanging_fruit', 0);
                    }
                }

                //filter by excluded status
                //if value is 'all', we will show both excluded and not-excluded
                if (isset(request()->excludedStatus)) {
                    $excludedStatus = request()->excludedStatus;
                    if ($excludedStatus == 'excluded') {
                        //show only excluded
                        $queries->whereHas('queryStatus', function ($query) {
                            $query->where('excluded', 1);
                        });
                    } elseif ($excludedStatus == 'not-excluded') {
                        //show those that are not excluded
                        $excludedQueries = SearchConsoleQuery::whereHas('queryStatus', function ($query) {
                            $query->where('excluded', 1);
                        })->pluck('id');
                        $queries->whereNotIn('id', $excludedQueries);
                    }
                }

                //filter by fixed status
                //if value is 'all', we will show both fixed and not-fixed
                if (isset(request()->fixedStatus)) {
                    $fixedStatus = request()->fixedStatus;
                    if ($fixedStatus == 'fixed') {
                        //show only fixed
                        $queries->whereHas('queryStatus', function ($query) {
                            $query->where('fixed', 1);
                        });
                    } elseif ($fixedStatus == 'not-fixed') {
                        //show those that are not fixed
                        $fixedQueries = SearchConsoleQuery::whereHas('queryStatus', function ($query) {
                            $query->where('fixed', 1);
                        })->pluck('id');
                        $queries->whereNotIn('id', $fixedQueries);
                    }
                }

                if (isset(request()->delegatedStatus)) {
                    $delegatedStatus = request()->delegatedStatus;
                    if ($delegatedStatus == 'delegated') {
                        //show only delegated
                        $queries->whereHas('queryStatus', function ($query) {
                            $query->where('delegated', 1);
                        });
                    } elseif ($delegatedStatus == 'not-delegated') {
                        //show those that are not delegated
                        $delegatedQueries = SearchConsoleQuery::whereHas('queryStatus', function ($query) {
                            $query->where('delegated', 1);
                        })->pluck('id');
                        $queries->whereNotIn('id', $delegatedQueries);
                    }
                }
            });

            return $datatable->make();
        }
    }

    /**
     * exclude/include query 
     */
    public function toggleExclude(SearchConsoleQuery $query)
    {
        if (isset($query)) {
            $existingQueriesWithStatus = SearchConsoleQueryStatuses::where('query', $query->query)->get();
            $newStatus = 0;

            foreach($existingQueriesWithStatus as $existingQueryWithStatus){
                if ($existingQueryWithStatus->excluded == 1) {
                    $newStatus = 0;
                } elseif ($existingQueryWithStatus->excluded == 0) {
                    $newStatus = 1;
                }
                //if query exists in queries with statuses we update it
                if (isset($existingQueryWithStatus) && !empty($existingQueryWithStatus)) {
                    if ($existingQueryWithStatus->excluded == 1 && $existingQueryWithStatus->fixed == 0 && $existingQueryWithStatus->delegated == 0) {
                        $existingQueryWithStatus->delete();
                        $query->update([
                            'query_status_id' => 0
                        ]);
                    } else {
                        $existingQueryWithStatus->update(['excluded' => $newStatus]);
                    }
                } else {
                    $newQueryWithStatus = new SearchConsoleQueryStatuses();
                    $data = [
                        'site_id' => $newQueryWithStatus->site_id,
                        'query' => $newQueryWithStatus->query,
                        'excluded' => $newStatus
                    ];
                    $newQueryWithStatus->fill($data);
                    $newQueryWithStatus->save();
                }
    
                // $query->update([
                //     'excluded' => $newStatus
                // ]);
    
            }

            
            if ($newStatus == 0) {
                $messageIdentifier = "included";
            } elseif ($newStatus == 1) {
                $messageIdentifier = "excluded";
            }

            if (request()->wantsJson()) {
                return JsonResource::make()->withSuccess(__('Query has been successfully ' . $messageIdentifier . '!'));
            }
            return redirect()->route('google_search_console.index')->withSystemSuccess(__('Query has been successfully ' . $messageIdentifier . '!'));
        }
    }

    /**
     * mark query as fixed/unfixed 
     */
    public function toggleFixed(SearchConsoleQuery $query)
    {
        if (isset($query)) {
            $existingQueriesWithStatus = SearchConsoleQueryStatuses::where('query', $query->query)->get();
            $newStatus = 0;
            foreach($existingQueriesWithStatus as $existingQueryWithStatus){
                if ($existingQueryWithStatus->fixed == 1) {
                    $newStatus = 0;
                } elseif ($existingQueryWithStatus->fixed == 0) {
                    $newStatus = 1;
                }
                //if query exists in queries with statuses we update it
                if (isset($existingQueryWithStatus) && !empty($existingQueryWithStatus)) {
    
                    if ($existingQueryWithStatus->fixed == 1 && $existingQueryWithStatus->excluded == 0 && $existingQueryWithStatus->delegated == 0) {
                        $existingQueryWithStatus->delete();
                        $query->update([
                            'query_status_id' => 0
                        ]);
                    } else {
                        $existingQueryWithStatus->update(['fixed' => $newStatus]);
                        $existingQueryWithStatus->update(['delegated' => 0]);
                    }
                } else {
                    $newQueryWithStatus = new SearchConsoleQueryStatuses();
                    $data = [
                        'site_id' => $newQueryWithStatus->site_id,
                        'query' => $newQueryWithStatus->query,
                        'fixed' => $newStatus
                    ];
                    $newQueryWithStatus->fill($data);
                    $newQueryWithStatus->save();
                }
    
                // $query->update([
                //     'fixed' => $newStatus
                // ]);
            }
            

            if ($newStatus == 0) {
                $messageIdentifier = "fixed";
            } elseif ($newStatus == 1) {
                $messageIdentifier = "unfixed";
            }


            if (request()->wantsJson()) {
                return JsonResource::make()->withSuccess(__('Query has been successfully marked as ' . $messageIdentifier . '!'));
            }
            return redirect()->route('google_search_console.index')->withSystemSuccess(__('Query has been successfully marked as ' . $messageIdentifier . '!'));
        }
    }

    /**
     * mark query as delegated/undelegated 
     */
    public function toggleDelegated(SearchConsoleQuery $query)
    {
        if (isset($query)) {
            $existingQueriesWithStatus = SearchConsoleQueryStatuses::where('query', $query->query)->get();
            $newStatus = 0;
            foreach($existingQueriesWithStatus as $existingQueryWithStatus){
                if ($existingQueryWithStatus->delegated == 1) {
                    $newStatus = 0;
                } elseif ($existingQueryWithStatus->delegated == 0) {
                    $newStatus = 1;
                }
                //if query exists in queries with statuses we update it
                if (isset($existingQueryWithStatus) && !empty($existingQueryWithStatus)) {
    
                    if ($existingQueryWithStatus->delegated == 1 && $existingQueryWithStatus->excluded == 0 && $existingQueryWithStatus->fixed == 0) {
                        $existingQueryWithStatus->delete();
                        $query->update([
                            'query_status_id' => 0
                        ]);
                    } else {
                        $existingQueryWithStatus->update(['delegated' => $newStatus]);
                    }
                } else {
                    $newQueryWithStatus = new SearchConsoleQueryStatuses();
                    $data = [
                        'site_id' => $newQueryWithStatus->site_id,
                        'query' => $newQueryWithStatus->query,
                        'delegated' => $newStatus
                    ];
                    $newQueryWithStatus->fill($data);
                    $newQueryWithStatus->save();
                }
    
                // $query->update([
                //     'delegated' => $newStatus
                // ]);

            }
            

            if ($newStatus == 0) {
                $messageIdentifier = "delegated";
            } elseif ($newStatus == 1) {
                $messageIdentifier = "dismiss";
            }


            if (request()->wantsJson()) {
                return JsonResource::make()->withSuccess(__('Query has been successfully marked as ' . $messageIdentifier . '!'));
            }
            return redirect()->route('google_search_console.index')->withSystemSuccess(__('Query has been successfully marked as ' . $messageIdentifier . '!'));
        }
    }

    /**
     * display all pages for one query
     */
    public function pages(SearchConsoleQuery $query)
    {
        //get all existing pages for query
        $queryPages = SearchConsoleQueryPage::where('query_id', $query->id)->get();
        if (isset($queryPages) && !empty($queryPages) && count($queryPages) > 0) {
            //if there are already pages for query, we will return query

        } else {
            //if there are no pages for query, we get them and then return query

            $queryTitle = $query->query;
            //create client for gsc api
            $client = $this->createClient();
            //form request
            $request = new SearchAnalyticsQueryRequest();
            $searchConsole = new SearchConsole($client);
            $daysBeforeNow = $query->days_old;
            //form dates for request
            $dateFrom = now()->subDays($daysBeforeNow)->format('Y-m-d');
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
            foreach ($websites as $website) {
                if ($website['site_id'] == $query->site_id) {
                    $domain = $website['domain'];
                }
            }

            if ($domain) {
                $response = $searchConsole->searchanalytics->query($domain, $request);
                if ($response) {
                    foreach ($response as $page) {
                        $newPage = new SearchConsoleQueryPage();
                        $data = [
                            'query_id' => $query->id,
                            'page' => $page->keys[0],
                            'clicks' => $page->clicks,
                            'impressions' => $page->impressions,
                            'ctr' => $page->ctr,
                            'position' => $page->position,
                            'days_old', $daysBeforeNow,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                        $newPage->fill($data);
                        $newPage->save();
                    }
                }
            }
        }

        return view('google_search_console.pages', ['query' => $query]);
    }

    protected function createClient()
    {
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
    public function pagesDatatable()
    {
        $queryId = request()->query_id;
        if (isset($queryId) && !empty($queryId)) {
            $queryExists = SearchConsoleQuery::where('id', $queryId)->first();
            if (isset($queryExists) && !empty($queryExists)) {
                $pages = SearchConsoleQueryPage::where('query_id', $queryId);

                $datatable = datatables($pages)->editColumn('ctr', function ($row) {
                    return $row->ctr . " (" . ($row->ctr * 100) . "%)";
                })->editColumn('position', function ($row) {
                    return round($row->position, 2);
                });

                return $datatable->make();
            }
        }
    }

    //new status button
    public function newStatus($status, SearchConsoleQuery $query)
    {
        if (isset($query)) {
            $queryStatus = new SearchConsoleQueryStatuses();
            switch ($status) {
                case 'excluded':
                    $data = [
                        'site_id' => $query->site_id,
                        'query' => $query->query,
                        'excluded' => 1,
                        'fixed' => 0,
                        'delegated' => 0,
                        'slave_status' => 0,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                    $queryStatus->fill($data);
                    $queryStatus->save();
                    $query->update([
                        'query_status_id' => $queryStatus->id
                    ]);

                    $messageIdentifier = "excluded";

                    if (request()->wantsJson()) {
                        return JsonResource::make()->withSuccess(__('Query has been successfully marked as ' . $messageIdentifier . '!'));
                    }
                    return redirect()->route('google_search_console.index')->withSystemSuccess(__('Query has been successfully marked as ' . $messageIdentifier . '!'));
                    break;

                case 'fixed':
                    $data = [
                        'site_id' => $query->site_id,
                        'query' => $query->query,
                        'excluded' => 0,
                        'fixed' => 1,
                        'delegated' => 0,
                        'slave_status' => 0,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                    $queryStatus->fill($data);
                    $queryStatus->save();
                    $query->update([
                        'query_status_id' => $queryStatus->id
                    ]);
                    $messageIdentifier = "fixed";

                    if (request()->wantsJson()) {
                        return JsonResource::make()->withSuccess(__('Query has been successfully marked as ' . $messageIdentifier . '!'));
                    }
                    return redirect()->route('google_search_console.index')->withSystemSuccess(__('Query has been successfully marked as ' . $messageIdentifier . '!'));
                    break;
                case 'delegated':
                    $data = [
                        'site_id' => $query->site_id,
                        'query' => $query->query,
                        'excluded' => 0,
                        'fixed' => 0,
                        'delegated' => 1,
                        'slave_status' => 1,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                    $queryStatus->fill($data);
                    $queryStatus->save();
                    $query->update([
                        'query_status_id' => $queryStatus->id
                    ]);
                    $messageIdentifier = "delegated";

                    if (request()->wantsJson()) {
                        return JsonResource::make()->withSuccess(__('Query has been successfully marked as ' . $messageIdentifier . '!'));
                    }
                    return redirect()->route('google_search_console.index')->withSystemSuccess(__('Query has been successfully marked as ' . $messageIdentifier . '!'));
                    break;
            }
        }
    }

    //get all active users for delegate modal
    public function getUsers()
    {
        $master_id = Auth::user()->id;
        $users = User::where('id', '!=', $master_id)->where('active', 1)->get();
        return response()->json(['users' => $users]);
    }

    //store delegated comment from master
    public function storeDelegate(Request $request)
    {

        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
            'comment' => 'nullable|string',
            'query_id' => 'required|exists:search_console_queries,id',
        ]);

        $slave = User::where('id', $data['user_id'])->first();
        if (isset($slave)) {
            $user = $slave->first_name . ' ' . $slave->last_name;
        } else {
            $user = '';
        }

        $master_id = Auth::user()->id;
        $searchConsole = SearchConsoleQuery::where('id', $data['query_id'])->first();
        $query = $searchConsole->query;
        $siteId = $searchConsole->site_id;
        $excludedStatus = $searchConsole->queryStatus->excluded ?? 0;
        $fixedStatus = $searchConsole->queryStatus->fixed ?? 0;

        $queryStatusExists = SearchConsoleQueryStatuses::where('query', $query)->first();
        // dd($data, $queryStatusExists);
        if(!isset($queryStatusExists) && empty($queryStatusExists)) {
            $queryStatus = SearchConsoleQueryStatuses::insert([
                'query' => $query,
                'site_id' => $siteId,
                'master_id' => $master_id,
                'slave_id' => $data['user_id'],
                'master_comment' => $data['comment'],
                'excluded' => $excludedStatus,
                'fixed' => $fixedStatus,
                'delegated' => 1,
                'slave_status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            if ($queryStatus) {
                $queryStatusId = SearchConsoleQueryStatuses::where('query', $query)->first();
                $searchConsole->update([
                    'query_status_id' => $queryStatusId->id
                ]);
            }
        }else {
            $queryStatusExists->update([
                'query' => $query,
                'site_id' => $siteId,
                'master_id' => $master_id,
                'slave_id' => $data['user_id'],
                'master_comment' => $data['comment'],
                'excluded' => $excludedStatus,
                'fixed' => $fixedStatus,
                'delegated' => 1,
                'slave_status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        
        if (request()->wantsJson()) {
            return JsonResource::make()->withSuccess(__('Query has been successfully delegated to user ' . $user . '!'));
        }
        return redirect()->route('google_search_console.index')->withSystemSuccess(__('Query has been successfully delegated to user ' . $user . '!'));
    }
}
