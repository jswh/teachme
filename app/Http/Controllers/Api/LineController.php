<?php

namespace App\Http\Controllers\Api;

use App\Models\Student;
use App\Models\Teacher;
use App\Services\ScopeService;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Request as FacadesRequest;

/**
 * @OA\Schema(
 *      schema="LineAuthInfo",
 *      allOf={
 *          @OA\Schema(ref="#/components/schemas/AuthInfo"),
 *          @OA\Schema(
 *              @OA\Property(property="id_token", type="string")
 *          )
 *      }
 * )
 */
class LineController extends ApiController
{
    /**
     * @OA\Get(
     *     path="/line/token/{code}",
     *     tags={"line"},
     *     summary="get auth info of line by code",
     *     description="get auth info of line by code",
     *     @OA\Parameter(
     *         name="code",
     *         in="path",
     *         description="the code",
     *         required=true
     *     ),
     *     @OA\Response(response="200", description="success", @OA\JsonContent(ref= "#/components/schemas/LineAuthInfo"))
     * )
     */
    public function token($code)
    {
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

    /**
     * @OA\Put(
     *     path="/line/bindings",
     *     tags={"line"},
     *     summary="bind account to line account",
     *     description="bind account to line account",
     *     @OA\RequestBody(
     *          request="binding info",
     *          required=true,
     *          description="product_request",
     *          @OA\JsonContent(
     *              @OA\Property(property="id_token", type="string")
     *          )
     *     ),
     *     @OA\Response(response="200", description="success", @OA\JsonContent(
     *          allOf={
     *              @OA\Schema(ref="#/components/schemas/ApiResponse"),
     *              @OA\Schema(
     *                  @OA\Property(property="data", ref="#/components/schemas/UserInterface")
     *              )
     *          }
     *          )
     *      )
     * )
     */
    public function bind(Request $request)
    {
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

    /**
     * @OA\Post(
     *     path="/line/bindings",
     *     tags={"line"},
     *     summary="bind account to line account",
     *     description="bind account to line account",
     *     @OA\RequestBody(
     *          request="binding info",
     *          required=true,
     *          description="product_request",
     *          @OA\JsonContent(
     *              @OA\Property(property="id_token", type="string")
     *          )
     *     ),
     *     @OA\Response(response="200", description="success", @OA\JsonContent(ref= "#/components/schemas/LineAuthInfo"))
     * )
     */
    public function getBindings(Request $request)
    {
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

    public function parseLineInfo($idToken)
    {
        JWT::$leeway = 60;
        return JWT::decode($idToken, getenv('LINE_SECRET'), ['HS256']);
    }
}
