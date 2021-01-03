<?php

namespace App\Http\Controllers\Api;

class MessageController extends ApiController
{
    public function online() {
        $user = \Auth::user();
        \Cache::set('msg:' . $user->id, time(), 30);

    }
}
