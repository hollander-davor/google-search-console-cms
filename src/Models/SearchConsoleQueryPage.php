<?php

namespace Hoks\CMSGSC\Models;

use Illuminate\Database\Eloquent\Model;

class SearchConsoleQueryPage extends Model
{
    protected $table = 'search_console_query_pages';
    protected $fillable = [
        'query_id',
        'page',
        'clicks',
        'impressions',
        'ctr',
        'position'
    ];
}