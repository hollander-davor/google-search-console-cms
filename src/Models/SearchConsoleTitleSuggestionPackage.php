<?php

namespace Hoks\CMSGSC\Models;

use Illuminate\Database\Eloquent\Model;

class SearchConsoleTitleSuggestionPackage extends Model
{

    protected $table = 'search_console_title_suggestions';

    protected $fillable = ['query', 'content'];

    protected $casts = [
        'content' => 'array',
    ];

}