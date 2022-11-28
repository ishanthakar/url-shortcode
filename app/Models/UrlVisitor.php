<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UrlVisitor extends Model
{
    public $table = "url_visitor";

    protected $fillable = ['url_shortcode_id ', 'ip', 'os', 'browser', 'device', 'meta'];
    public function shortCode()
    {
        return $this->hasOne('App\Models\UrlShortcode', 'id', 'url_shortcode_id');
    }
    
}