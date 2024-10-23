<?php

namespace Hoks\CMSGSC\Models;

use Illuminate\Database\Eloquent\Model;

class SearchConsoleQuery extends Model
{

    protected $table = 'search_console_queries';
    protected $fillable = [
        'site_id',
        'query_status_id',
        'query',
        'clicks',
        'impressions',
        'ctr',
        'position',
        // 'excluded',
        // 'fixed',
        'critical',
        'low_hanging_fruit',
        'created_at',
        'updated_at'
    ];

    public function queryStatus()
    {
        return $this->belongsTo(SearchConsoleQueryStatuses::class, 'query_status_id');
    }
}
