<?php

namespace Hoks\CMSGSC\Models;

use Illuminate\Database\Eloquent\Model;

class SearchConsoleQuery extends Model
{
    protected $table = 'search_console_queries';
    protected $fillable = [
        'site_id',
        'query',
        'clicks',
        'impressions',
        'ctr',
        'position',
        'excluded',
        'fixed',
        'critical',
        'created_at',
        'updated_at'
    ];
}
