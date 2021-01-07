<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\School;
use App\Services\SchoolService;
use Exception;
use Illuminate\Support\Facades\Validator;
use Throwable;

/**
 *
 * @OA\Schema(
 *      schema="RegisterInfo",
 *      @OA\Property(property="name", type="string"),
 *      @OA\Property(property="email", type="string"),
 *      @OA\Property(property="password", type="integer"),
 *      @OA\Property(property="password_confirmation", type="string"),
 * )
 */
class RegisterController extends ApiController
{
    /**
     * @OA\Post(
     *     path="/register/principal",
     *     tags={"school"},
     *     summary="register a principal",
     *     description="register a principal",
     *     @OA\RequestBody(
     *          request="register info",
     *          required=true,
     *          description="register info",
     *          @OA\JsonContent( ref="#/components/schemas/RegisterInfo"         )
     *     ),
     *     @OA\Response(response="200", description="success", @OA\JsonContent(
     *          allOf={
     *              @OA\Schema(ref="#/components/schemas/ApiResponse"),
     *              @OA\Schema(
     *                  @OA\Property(property="data", @OA\Items(ref="#/components/schemas/UserSchema"))
     *              )
     *          }
     *          )
     *      )
     * )
     */
    public function registerPrinciple(Request $request)
    {
        $params = $request->all();
        try {
            $this->registerValdator($params);
        } catch (Throwable $e) {
            throw new Exception($e->getMessage());
        }
        $principle = SchoolService::createPrinciple($params);

        return $this->success('ok', $principle);
    }

    /**
     * @OA\Post(
     *     path="/school/{school_id}/teachers",
     *     tags={"school"},
     *     summary="register a teacher",
     *     description="register a teacher",
     *     @OA\RequestBody(
     *          request="register info",
     *          required=true,
     *          description="register info",
     *          @OA\JsonContent(
     *              allOf= {
     *                  @OA\Schema(ref="#/components/schemas/RegisterInfo"),
     *                  @OA\Schema(
     *                      @OA\Property(property="token", type="string", description="the token from the invite url")
     *                  )
     *              }
     *          )
     *     ),
     *     @OA\Response(response="200", description="success", @OA\JsonContent(
     *          allOf={
     *              @OA\Schema(ref="#/components/schemas/ApiResponse"),
     *              @OA\Schema(
     *                  @OA\Property(property="data", @OA\Items(ref="#/components/schemas/UserSchema"))
     *              )
     *          }
     *          )
     *      )
     * )
     */
    public function registerTeacher(School $school, Request $request)
    {
        $params = $request->all();
        $this->registerValdator($params);
        $teacher = (new SchoolService($school))
            ->registerTeacher($params, $request->input('token'));

        return $this->success('ok', $teacher);
    }

    protected function registerValdator($data)
    {
        return Validator::make($data, [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:6|confirmed',
        ])->validate();
    }
}
