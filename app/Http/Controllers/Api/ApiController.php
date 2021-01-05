<?php

namespace App\Http\Controllers\Api;

use App\Services\ChatService;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ApiController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected function success($msg, $data = null) {
        $re = ['code' => 0, 'msg' => $msg];
        if ($data) {
            $re['data'] = $data;
        }

        return $re;
    }

    public function me() {
        $user = \Auth::user();
        ChatService::allowChat($user);
        return $this->success('ok', $user);
    }

    public function ping() {
        try {
            \Cache::put('ping', 'pong', 1);
            var_dump(\Cache::pull('ping'));
        } catch(\Throwable $e) {
            var_dump($e);
        }
        return $this->success('ok');
    }
}
