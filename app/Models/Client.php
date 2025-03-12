<?php

namespace App\Models;

use Laravel\Passport\Client as PassportClient;

class Client extends PassportClient
{
    public function getBaseRedirectUrlAttribute()
    {
        if (!$this->redirect) {
            return null;
        }

        $scheme = parse_url($this->redirect, PHP_URL_SCHEME);
        $host = parse_url($this->redirect, PHP_URL_HOST);
        $port = parse_url($this->redirect, PHP_URL_PORT);

        $baseUrl = $scheme . '://' . $host;
        if ($port && $port != 80 && $port != 443) {
            $baseUrl .= ':' . $port;
        }

        return $baseUrl;
    }

    public function scopeSearch($query, $search)
    {
        if ($search) {
            $query->where('name', 'like', '%' . $search . '%');
        }

        return $query;
    }
}
