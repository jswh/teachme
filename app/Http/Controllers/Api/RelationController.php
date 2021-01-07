<?php

namespace App\Http\Controllers\Api;

use App\Models\Teacher;
use App\Services\RelationService;
/**
 * @OA\Schema(
 *      schema="Relation",
 *      @OA\Property(property="id", type="integer"),
 *      @OA\Property(property="from", type="integer"),
 *      @OA\Property(property="to", type="integer"),
 *      @OA\Property(property="type", type="integer")
 * )
 */
class RelationController extends ApiController
{
    /**
     * @OA\Post(
     *     path="/relation/{teacher}/focus",
     *     tags={"relation"},
     *     summary="create as focus relation of a teacher",
     *     description="create as focus of a teacher",
     *     @OA\Response(response="200", description="success", @OA\JsonContent(
     *          allOf={
     *              @OA\Schema(ref="#/components/schemas/ApiResponse"),
     *              @OA\Schema(
     *                  @OA\Property(property="data", @OA\Items(ref="#/components/schemas/Relation"))
     *              )
     *          }
     *          )
     *      )
     * )
     */
    public function focus(Teacher $teacher) {
        $user = \Auth::user();
        $relation = (new RelationService($user))->focusTeacher($teacher);

        return $this->success('ok', $relation);
    }

    /**
     * @OA\Delete(
     *     path="/relation/{teacher}/focus",
     *     tags={"relation"},
     *     summary="remove as focus relation of a teacher",
     *     description="create as focus of a teacher",
     *     @OA\Response(response="200", description="success")
     * )
     */
    public function unfocus(Teacher $teacher) {

        $user = \Auth::user();
        $result = (new RelationService($user))->unfocusTeacher($teacher);

        return $this->success('ok', $result);
    }

}
