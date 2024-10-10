<?php

return [
    //absolute path to google credentials json (service_accont.json)
   'google_application_credentials' => env("GOOGLE_APPLICATION_CREDENTIALS",""),
   //websites for which we will take data, each website is array with domain and site_id
   'websites_domains' => [
        [
            'domain' => 'sc-domain:24sedam.rs',
            'site_id' => 1
        ]
        ],
   //how many days should data be old (default is 7 days, it means that we will get data for last 7 days)
   'days_before_now' => 7,
   //queries with ctr values bellow this value will be in consideration for critical queries(combined with number of impressions)
    'low_ctr_value' => 0.1,
   //queries with impression values above this value will be in consideration for critical queries(combined with ctr)
    'high_impressions_value' => 500,
    //background color for critical queries (in datatable)
    'critical_query_color' => '#ffcccb',
    //text to be displayed on "tag" for critical query (in datatable)
    'critical_query_text' => "Critical query"
];
