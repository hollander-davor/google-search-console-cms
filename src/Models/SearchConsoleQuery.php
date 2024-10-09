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
        'status',
        'critical',
        'created_at',
        'updated_at'
    ];

    /**
    * statuses
    */
    const STATUS_NEW = 0;
    const STATUS_EXCLUDED = 1;
    const STATUS_FIXED = 2;

}
