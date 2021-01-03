<?php

namespace App\Http\Controllers\Api;

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
        return $this->success('ok', \Auth::user());
    }

    public function ping() {
        try {
            \Cache::set('ping', 'pong');
            var_dump(\Cache::get('ping'));
        }
        return $this->success();
    }
}
