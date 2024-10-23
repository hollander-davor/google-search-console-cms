# CMS GSC
This package downloads and displays data from Google Search Console into cms
Installation:
1. composer require davor/cms-gsc
2. php artisan vendor:publish --tag=config --provider="Hoks\CMSGSC\CMSGSCServiceProvider"
3. php artisan vendor:publish --tag=views --provider="Hoks\CMSGSC\CMSGSCServiceProvider"
4. run migrations
5. Put service_account.json to desired location
6. Set up config/gsc-cms.php
7. Call command GetGoogleSearchConsoleData according to you needs (for example, once a day at 03:00)
8. Make permissions to access package pages "gsc-cms" for admin and "gsc-user" for user
9. put require base_path('vendor/davor/cms-gsc/src/routes/web.php'); inside of routes/web.php
10. Access the page /google-search-console(route name: google_search_console.index)   /google-search-console-user(route name: google_search_console_user.index)
11. In layout include blade @include('google_search_console.scripts.check_new_queries')
12. Use package :D

