<?php

namespace App\Http\Controllers\Api;

use App\Services\ChatService;

class MessageController extends ApiController
{
    public function online() {
        $user = \Auth::user();
        ChatService::allowChat($user);

        return $this->success('authed');
    }

}
