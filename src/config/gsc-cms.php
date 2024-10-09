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
   'days_before_now' => 7
];
