<?php

return [
    //absolute path to google credentials json (service_accont.json)
    'google_application_credentials' => env("GOOGLE_APPLICATION_CREDENTIALS", ""),
    //websites for which we will take data, each website is array with domain and site_id
    'websites_domains' => [
        [
            'domain' => '',
            'site_id' => 1
        ]
    ],
    //how many days should data be old (default is 7 days, it means that we will get data for last 7 days)
    'days_before_now' => 7,
    //queries with ctr values bellow this value will be in consideration for critical queries(combined with number of impressions)
    'low_ctr_value' => 0.1,
    //queries with impression values above this value will be in consideration for critical queries(combined with ctr)
    'high_impressions_value' => 500,
    //low hanging fruit low value
    'low_lhf_value' => 9,
    //low hanging fruit high value
    'high_lhf_value' => 15,
    //background color for critical queries (in datatable)
    'critical_query_color' => '#c1e1ec',
    //background color for low hanging fruit queries (in datatable)
    'lhf_query_color' => '#c1e1ec',
    //text to be displayed on "tag" for critical query (in datatable)
    'critical_query_text' => "Great potential",
    //text to be displayed on "tag" for low hanging fruit query (in datatable)
    'lhf_query_text' => "Low hanging fruit",
    //background color for excluded queries (in datatable)
    'excluded_query_color' => "#ffb09c",
    //text to be displayed on "tag" for excluded query (in datatable)
    'excluded_query_text' => "Excluded query",
    //background color for fixed queries (in datatable)
    'fixed_query_color' => '#d8e6ad',
    //background color for delegated queries (in datatable)
    'delegated_query_color' => '#9dadf5',
    //text to be displayed on "tag" for fixed query (in datatable)
    'fixed_query_text' => "Fixed query",
    //text to be displayed on "tag" for delegated query (in datatable)
    'delegated_query_text' => "Delegated query",
    //text to be displayed on "tag" for done query (in datatable)
    'done_query_text' => "Done",
    //text to be displayed on "tag" for delayed query (in datatable)
    'delayed_query_text' => "Delayed",
    //text to be displayed on "tag" for In progress query (in datatable)
    'in_progress_query_text' => "In progress",
    //ajax interval in seconds
    "ajax_interval" => 10,
    //statuses for slave queries
    'delivered_status' => "Delivered",
    'seen_status' => "Seen",
    'done_status' => "Done",
    'delayed_status' => "Delayed",
    'in_progress_status' => "In progress",
];
