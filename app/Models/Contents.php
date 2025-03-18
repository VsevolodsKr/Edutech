<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contents extends Model
{
    use HasFactory;
    
    protected $table = 'Contents';
    
    protected $fillable = [
        'teacher_id',
        'playlist_id',
        'title',
        'description',
        'video',
        'thumb',
        'date',
        'status',
    ];
    public $timestamps = false;

    public function user() {
        return $this->hasOne('App\Models\Users', 'id', 'user_id');
    }

    public function teacher() {
        return $this->hasOne('App\Models\Teachers', 'id', 'teacher_id');
    }

    public function playlist() {
        return $this->belongsTo('App\Models\Playlists', 'playlist_id', 'id');
    }

    public function likes()
    {
        return $this->hasMany(Likes::class, 'content_id');
    }

    public function comments()
    {
        return $this->hasMany(Comments::class, 'content_id');
    }
}
