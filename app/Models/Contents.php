<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Encryptable;

class Contents extends Model
{
    use HasFactory;
    use Encryptable;
    
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
        'video_source_type'
    ];

    protected $appends = ['encrypted_id'];

    public $timestamps = false;

    public function getEncryptedIdAttribute()
    {
        return $this->encryptId($this->id);
    }

    public function getThumbAttribute($value)
    {
        if (!$value) {
            return null;
        }
        return str_replace('/storage/app/public/', '/storage/', $value);
    }

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
