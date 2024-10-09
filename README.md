# CMS GSC
This package downloads and displays data from Google Search Console into cms
Installation:
1. composer require davor/cms-gsc
2. php artisan vendor:publish --tag=config --provider="Hoks\CMSGSC\CMSGSCServiceProvider"
2. php artisan vendor:publish --tag=views --provider="Hoks\CMSGSC\CMSGSCServiceProvider"
4. Set up config/gsc-cms.php
5. Call command GetGoogleSearchConsoleData according to you needs (for example, once a day at 03:00)
6. Use package :D

