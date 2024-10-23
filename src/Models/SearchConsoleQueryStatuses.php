<?php

namespace Hoks\CMSGSC\Models;

use Illuminate\Database\Eloquent\Model;

class SearchConsoleQueryStatuses extends Model
{
    
    const SLAVE_STATUS_DELIVERED = 1;
    const SLAVE_STATUS_SEEN = 2;
    const SLAVE_STATUS_DONE = 3;
    const SLAVE_STATUS_DELAYED = 4;
    const SLAVE_STATUS_IN_PROGRESS = 5;

    protected $table = 'search_console_queries_statuses';
    protected $fillable = [
        'site_id',
        'query',
        'excluded',
        'fixed',
        'delegated',
        'master_id',
        'master_comment',
        'slave_id',
        'slave_comment',
        'slave_status',
        'created_at',
        'updated_at'
    ];

    public function queryConsole()
    {
        return $this->hasOne(SearchConsoleQuery::class, 'query_status_id');
    }

}
