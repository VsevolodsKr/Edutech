<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use App\Traits\Encryptable;

class Teachers extends Authenticatable
{
    use HasFactory, HasApiTokens, Encryptable;
    protected $guard = 'teacher';
    protected $fillable = [
        'name',
        'profession',
        'email',
        'password',
        'image',
    ];
    public $timestamps = false;

    protected $appends = ['formatted_image', 'encrypted_id'];

    public function getFormattedImageAttribute()
    {
        if (!$this->image) {
            return '/storage/default-avatar.png';
        }

        if (str_starts_with($this->image, 'http')) {
            return $this->image;
        }

        // If the path already starts with /storage/, return it as is
        if (str_starts_with($this->image, '/storage/')) {
            return $this->image;
        }

        // Clean the path from any storage/public prefixes and normalize it
        $path = str_replace([
            'storage/app/public/',
            'storage/',
            '/app/public/',
            'app/public/',
            'uploads/uploads/',
            'uploads/'
        ], '', $this->image);

        // If the path is empty after cleaning, return default image
        if (empty($path)) {
            return '/storage/default-avatar.png';
        }

        // If the path is in uploads directory, keep it
        if (str_starts_with($path, 'uploads/')) {
            return '/storage/' . $path;
        }

        return '/storage/' . $path;
    }

    public function playlists()
    {
        return $this->hasMany('App\Models\Playlists', 'teacher_id', 'id');
    }

    public function contents()
    {
        return $this->hasMany('App\Models\Contents', 'teacher_id', 'id');
    }

    public function comments()
    {
        return $this->hasManyThrough(
            'App\Models\Comments',
            'App\Models\Contents',
            'teacher_id',
            'content_id',
            'id',
            'id'
        );
    }

    public function likes()
    {
        return $this->hasManyThrough(
            'App\Models\Likes',
            'App\Models\Contents',
            'teacher_id',
            'content_id',
            'id',
            'id'
        );
    }
}
