<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class Developers extends Authenticatable
{
    use HasFactory, HasApiTokens;

    protected $table = 'developers';
    protected $guard = 'developer';

    protected $fillable = [
        'name',
        'email',
        'password',
        'image',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function getImageAttribute($value)
    {
        if (!$value) {
            return '/storage/default-avatar.png';
        }

        if (str_starts_with($value, 'http')) {
            return $value;
        }

        $path = str_replace([
            '/storage/app/public/',
            'storage/app/public/',
            '/storage/',
            'storage/',
            'uploads/uploads/',
            'uploads/'
        ], '', $value);

        $path = trim($path, '/');

        if (empty($path)) {
            return '/storage/default-avatar.png';
        }

        return '/storage/' . $path;
    }
} 