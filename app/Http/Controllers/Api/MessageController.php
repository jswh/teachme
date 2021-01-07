<?php

namespace App\Http\Controllers\Api;

use App\Models\Student;
use App\Notifications\SimpleNotification;
use App\Services\ChatService;
use Illuminate\Http\Request;

class MessageController extends ApiController
{

    /**
     * @OA\Post(
     *     path="/notification",
     *     tags={"chat"},
     *     summary="send notification to a student",
     *     description="send notification to a student",
     *     @OA\RequestBody(
     *          request="register info",
     *          required=true,
     *          description="register info",
     *          @OA\JsonContent( @OA\Schema(
     *              @OA\Property(property="to", type="integer", description="the student id"),
     *              @OA\Property(property="message", type="string"))
     *          )
     *     ),
     *     @OA\Response(response="200", description="success",
     *          @OA\JsonContent(ref="#/components/schemas/ApiResponse")
     *     )
     * )
     */
    public function studentNotification(Request $request) {
        $params = $request->all(['to', 'message']);
        $student = Student::findOrFail($params['to']);
        $student->notify((new SimpleNotification($params['message'])));

        return $this->success('success');
    }
}
