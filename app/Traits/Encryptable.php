<?php

namespace App\Traits;

use Illuminate\Support\Facades\Crypt;

trait Encryptable
{
    public function encryptId($id)
    {
        return Crypt::encryptString($id);
    }

    public function decryptId($encryptedId)
    {
        try {
            return Crypt::decryptString($encryptedId);
        } catch (\Exception $e) {
            return null;
        }
    }

    public function getEncryptedIdAttribute()
    {
        return $this->encryptId($this->id);
    }
}