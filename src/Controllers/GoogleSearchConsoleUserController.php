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
use App\Models\Website\Website;
use Illuminate\Support\Facades\Auth;

class GoogleSearchConsoleUserController extends Controller
{
    /**
     * displays index page
     */
    public function index($activeWebsite = false)
    {
        return view('google_search_console_user.index', [
            'activeWebsite' => $activeWebsite
        ]);
    }

    /**
     * datatable
     */
    public function datatable()
    {
        $activeWebsite = request()->activeWebsite;
        $slave = Auth::user()->id;
        if (isset($activeWebsite)) {
            $queries = SearchConsoleQuery::where('site_id', $activeWebsite)->whereHas('queryStatus', function ($query) use ($slave) {
                $query->where('slave_id', $slave);
            });

            $slaveId = Auth::user()->id;
            $slaveStatuses = SearchConsoleQueryStatuses::where('site_id', $activeWebsite)->where('slave_id', $slaveId)->where('slave_status', 1)->get();
            if (isset($slaveStatuses) && !empty($slaveStatuses)) {
                foreach ($slaveStatuses as $status) {
                    $status->update([
                        'slave_status' => 2
                    ]);
                }
            }

            $datatable = datatables($queries)->addColumn('actions', function ($row) {
                return view('google_search_console_user.partials.actions', ['entity' => $row->queryStatus, 'critical' => $row->critical, 'query' => $row]);
            })->editColumn('ctr', function ($row) {
                return $row->ctr . " (" . ($row->ctr * 100) . "%)";
            })->editColumn('position', function ($row) {
                return round($row->position, 2);
            })->addColumn('delegated_by', function ($row) {
                $admin = User::where('id', $row->queryStatus->master_id)->first();
                return $admin->first_name . ' ' . $admin->last_name;
            })->editColumn('comment', function ($row) {
                return $row->queryStatus->master_comment ?? '';
            });

            $datatable->rawColumns(['actions']);

            $datatable->filter(function () use ($queries) {
                //search filter
                if (request()->has('search') && isset(request()->get('search')['value'])) {
                    $searchString = request()->get('search')['value'];
                    $queries->where('query', 'LIKE', '%' . $searchString . '%');
                }

                if (isset(request()->status)) {
                    $status = request()->status;
                    if ($status == 'done') {
                        //show only delegated
                        $queries->whereHas('queryStatus', function ($query) {
                            $query->where('slave_status', 3);
                        });
                    } elseif ($status == 'delayed') {
                        //show those that are not delegated
                        $queries->whereHas('queryStatus', function ($query) {
                            $query->where('slave_status', 4);
                        });
                    } elseif ($status == 'in_progress') {
                        //show those that are not delegated
                        $queries->whereHas('queryStatus', function ($query) {
                            $query->where('slave_status', 5);
                        });
                    }
                }
            });

            return $datatable->make();
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
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                        $newPage->fill($data);
                        $newPage->save();
                    }
                }
            }
        }

        return view('google_search_console_user.pages', ['query' => $query]);
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

    //store comment from slave
    public function storeComment(Request $request)
    {

        $data = $request->validate([
            'status_id' => 'required|integer',
            'comment' => 'nullable|string',
            'query_id' => 'required|exists:search_console_queries,id',
        ]);

        $searchConsole = SearchConsoleQuery::where('id', $data['query_id'])->first();
        $query = $searchConsole->query;

        $queryStatus = SearchConsoleQueryStatuses::where('query', $query)->first();
       
        $queryStatus->slave_status = $data['status_id'];
        $queryStatus->slave_comment = $data['comment'];
        $queryStatus->save();

        if (request()->wantsJson()) {
            return JsonResource::make()->withSuccess(__('You have been successfully send a feedback!'));
        }
        return redirect()->route('google_search_console_user.index')->withSystemSuccess(__('You have been successfully send a feedback!'));
    }

    public function ajaxNewQueries()
    {
        $userId = auth()->id();

        $data = [
            [
                'site_id' => 1,
                'name' => "Story",
                'count' => SearchConsoleQueryStatuses::where('slave_id', $userId)->where('site_id', 1)
                    ->where('slave_status', SearchConsoleQueryStatuses::SLAVE_STATUS_DELIVERED)
                    ->count()
            ],
            [
                'site_id' => 2,
                'name' => "Lepota i zdravlje",
                'count' => SearchConsoleQueryStatuses::where('slave_id', $userId)->where('site_id', 2)
                    ->where('slave_status', SearchConsoleQueryStatuses::SLAVE_STATUS_DELIVERED)
                    ->count()
            ],
            [
                'site_id' => 3,
                'name' => "Hellomagazin",
                'count' => SearchConsoleQueryStatuses::where('slave_id', $userId)->where('site_id', 3)
                    ->where('slave_status', SearchConsoleQueryStatuses::SLAVE_STATUS_DELIVERED)
                    ->count()
            ],
            [
                'site_id' => 4,
                'name' => "Gloria",
                'count' => SearchConsoleQueryStatuses::where('slave_id', $userId)->where('site_id', 4)
                    ->where('slave_status', SearchConsoleQueryStatuses::SLAVE_STATUS_DELIVERED)
                    ->count()
            ],
        ];

        foreach($data as $key => $value) {
            if($value['count'] == 0) {
                unset($data[$key]);
            }
        }
        
        return response()->json(['data' => $data]);
    }
}
