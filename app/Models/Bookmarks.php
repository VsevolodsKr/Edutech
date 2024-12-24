<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bookmarks extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'playlist_id',
    ];

    public $timestamps = false;

    public function user() {
        return $this->hasOne('App\Models\Users', 'id', 'user_id');
    }

    public function playlist() {
        return $this->hasOne('App\Models\Playlists', 'id', 'playlist_id');
    }
}
