# CMS GSC
This package downloads and displays data from Google Search Console into cms
Installation:
1. composer require davor/cms-gsc
2. php artisan vendor:publish --tag=config --provider="Hoks\CMSGSC\CMSGSCServiceProvider"
3. php artisan vendor:publish --tag=views --provider="Hoks\CMSGSC\CMSGSCServiceProvider"
4. Put service_account.json to desired location
5. Set up config/gsc-cms.php
6. Call command GetGoogleSearchConsoleData according to you needs (for example, once a day at 03:00)
7. Use package :D

