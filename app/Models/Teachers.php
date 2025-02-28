<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class Teachers extends Authenticatable
{
    use HasFactory, HasApiTokens;
    protected $guard = 'teacher';
    protected $fillable = [
        'name',
        'profession',
        'email',
        'password',
        'image',
    ];
    public $timestamps = false;

    protected $appends = ['formatted_image'];

    public function getFormattedImageAttribute()
    {
        if (!$this->image) {
            return '/storage/default.png';
        }

        if (str_starts_with($this->image, 'http')) {
            return $this->image;
        }

        // Clean the path from any storage/public prefixes and normalize it
        $path = str_replace([
            '/storage/', 
            'storage/', 
            '/app/public/', 
            'app/public/',
            'uploads/uploads/'
        ], '', $this->image);

        // Ensure the path starts with uploads/ if it doesn't already
        if (!str_starts_with($path, 'uploads/')) {
            $path = 'uploads/' . $path;
        }

        return '/storage/' . $path;
    }
}
