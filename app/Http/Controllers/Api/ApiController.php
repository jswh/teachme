<?php

namespace App\Http\Controllers\Api;

use App\Channels\LineChannel;
use App\Models\Student;
use App\Notifications\SimpleNotification;
use App\Services\ChatService;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

/**
 * @OA\Info(title="Teachme API", version="0.1")
 *
 * @OA\Schema(
 *      schema="AuthInfo",
 *      @OA\Property(property="access_token", type="string"),
 *      @OA\Property(property="refresh_token", type="string"),
 *      @OA\Property(property="expire_in", type="integer"),
 *      @OA\Property(property="token_type", type="string"),
 * )
 * @OA\Schema(
 *      schema="ApiResponse",
 *      @OA\Property(property="code", type="integer"),
 *      @OA\Property(property="msg", type="string"),
 * )
 */
class ApiController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected function success($msg, $data = null)
    {
        $re = ['code' => 0, 'msg' => $msg];
        if ($data) {
            $re['data'] = $data;
        }

        return $re;
    }

    public function me()
    {
        $user = \Auth::user();
        ChatService::allowChat($user);
        return $this->success('ok', $user);
    }

    public function ping()
    {
        /** @var Student */
        $student = Student::first();
        $student->notify((new SimpleNotification('hello')));
        $student->notify((new SimpleNotification('hello'))->viaLine());
        try {
            \Cache::put('ping', 'pong', 1);
            var_dump(\Cache::pull('ping'));
        } catch (\Throwable $e) {
            var_dump($e);
        }
        return $this->success('ok');
    }
}
