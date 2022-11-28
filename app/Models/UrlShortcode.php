<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UrlShortcode extends Model
{
    public $table = "url_shortcode";

    protected $fillable = ['url', 'hash', 'is_used'];
    public function visitors()
    {
        return $this->hasMany('App\Models\UrlVisitor', 'url_shortcode_id', 'id');
    }
}