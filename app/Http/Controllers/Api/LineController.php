<?php

namespace App\Http\Controllers\Api;

use GuzzleHttp\Client;

class LineController extends ApiController
{
    public function token($code) {
        $http = new Client();
        $response = $http->post('https://api.line.me/oauth2/v2.1/token', [
            'proxy' => 'http://172.24.48.1:10809',
            'form_params' => [
                'grant_type'    => 'authorization_code',
                'code'          => $code,
                'redirect_uri'  => 'http://localhost:8080/#/withline',
                'client_id'     => '1655544448',
                'client_secret' => '1823d723381a08904ec3c19b864cc499'
            ]
        ]);
        $body = (string) $response->getBody();

        return json_decode($body);
    }

    public function login() {
    }

    public function bind() {
    }
}
