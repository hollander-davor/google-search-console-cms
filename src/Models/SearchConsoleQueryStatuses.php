<?php

namespace Hoks\CMSGSC\Models;

use Illuminate\Database\Eloquent\Model;

class SearchConsoleQueryStatuses extends Model
{

    protected $table = 'search_console_queries_statuses';
    protected $fillable = [
        'site_id',
        'query',
        'exclude',
        'fixed',
        'created_at',
        'updated_at'
    ];
}
