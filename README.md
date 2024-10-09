# CMS GSC
This package downloads and displays data from Google Search Console into cms
Installation:
1. composer require davor/cms-gsc
2. php artisan vendor:publish --tag=config --provider="Hoks\CMSGSC\CMSGSCServiceProvider"
3. php artisan vendor:publish --tag=views --provider="Hoks\CMSGSC\CMSGSCServiceProvider"
4. php artisan vendor:publish --tag=controllers --provider="Hoks\CMSGSC\CMSGSCServiceProvider"
5. Put service_account.json to desired location
6. Set up config/gsc-cms.php
7. Call command GetGoogleSearchConsoleData according to you needs (for example, once a day at 03:00)
8. Make permission to access package pages (gsc-cms)
9. put require base_path('vendor/davor/cms-gsc/src/routes/web.php'); inside of routes/web.php
10. Use package :D

