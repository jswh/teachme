<?php

namespace App\Http\Controllers\Api;

use App\Models\Student;
use App\Notifications\SimpleNotification;
use App\Services\ChatService;
use Illuminate\Http\Request;

class MessageController extends ApiController
{
    public function online() {
        $user = \Auth::user();
        ChatService::allowChat($user);

        return $this->success('authed');
    }

    public function studentNotification(Request $request) {
        $params = $request->all(['to', 'message']);
        $student = Student::findOrFail($params['to']);
        $student->notify((new SimpleNotification($params['message'])));

        return $this->success('success');
    }
}
