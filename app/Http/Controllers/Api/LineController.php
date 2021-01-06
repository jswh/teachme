<?php

namespace App\Http\Controllers\Api;

use App\Models\Student;
use App\Models\Teacher;
use App\Services\ScopeService;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Request as FacadesRequest;

class LineController extends ApiController
{
    public function token($code) {
        $http = new Client();
        $response = $http->post('https://api.line.me/oauth2/v2.1/token', [
            'proxy' => getenv('proxy'),
            'form_params' => [
                'grant_type'    => 'authorization_code',
                'code'          => $code,
                'redirect_uri'  => trim(FacadesRequest::server('HTTP_REFERER'), '/') .  '/withline',
                'client_id'     => '1655551351',
                'client_secret' => getenv('LINE_SECRET')
            ]
        ]);
        $body = (string) $response->getBody();

        return $body;
    }

    public function bind(Request $request) {
        $idToken = $request->get('id_token');
        $lineUserInfo = $this->parseLineInfo($idToken);
        $lineUserId = $lineUserInfo->sub;

        /** @var Teacher | Student */
        $user = \Auth::user();
        if ($user instanceof Teacher) {
            $bindTeacher = Teacher::where('line_user_id', $lineUserId)->first();
            if ($bindTeacher && $bindTeacher->id != $user->id) {
                throw new \Exception('can only bind one teacher');
            }
        }
        $user->line_user_id = $lineUserId;
        $user->save();

        return $this->success('ok', $user);
    }

    public function getBindings(Request $request) {
        $idToken = $request->get('id_token');
        $lineUserInfo = $this->parseLineInfo($idToken);
        $lineUserId = $lineUserInfo->sub;
        /** @var Teacher */
        $teacher = Teacher::where('line_user_id', $lineUserId)->first();
        $students = Student::where('line_user_id', $lineUserId)->get();
        $bindings = [];
        if ($teacher) {
            $bindings[] = [
                'type' => 'teacher',
                'name' => $teacher->name,
                'access_token' => $teacher->createToken('personal', [ScopeService::SCOPE_TEACHER])->accessToken,
                'user' => $teacher
            ];
        }
        foreach ($students as $student) {
            $bindings[] = [
                'type' => 'student',
                'name' => $student->name,
                'access_token' => $student->createToken('personal', [ScopeService::SCOPE_STUDENT])->accessToken,
                'user' => $student
            ];
        }

        return $this->success('ok', $bindings);
    }

    public function parseLineInfo($idToken) {
        JWT::$leeway = 60;
        return JWT::decode($idToken, getenv('LINE_SECRET'), ['HS256']);
    }
}
