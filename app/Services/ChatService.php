<?php
namespace App\Services;

use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Support\Facades\Redis;

class ChatService {
    /**
     * @var App\Models\Teacher | App\Models\Student $user
     */
    public static function allowChat($user) {
        Redis::setex($user->chat_id, 600, 'ok');
    }

}
