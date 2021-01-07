<?php

namespace App\Http\Controllers\Api;

use App\Models\Relation;
use App\Models\School;
use App\Models\Student;
use App\Models\Teacher;
use App\Services\RelationService;
use App\Services\SchoolService;
use Exception;
use Illuminate\Http\Request;
/**
 * @OA\Schema(
 *      schema="School",
 *      @OA\Property(property="access_token", type="string"),
 *      @OA\Property(property="refresh_token", type="string"),
 *      @OA\Property(property="expire_in", type="integer"),
 *      @OA\Property(property="token_type", type="string"),
 * )
 */
class SchoolController extends ApiController
{
    /**
     * @OA\Post(
     *     path="/schools",
     *     tags={"school"},
     *     summary="apply school",
     *     description="apply school",
     *     @OA\RequestBody(
     *          request="school info",
     *          required=true,
     *          description="school info",
     *          @OA\JsonContent(
     *              @OA\Property(property="name", type="string", description="the name of school"),
     *              @OA\Property(property="description", type="string", description="the description of school")
     *          )
     *     ),
     *     @OA\Response(response="200", description="success", @OA\JsonContent(
     *          allOf={
     *              @OA\Schema(ref="#/components/schemas/ApiResponse"),
     *              @OA\Schema(
     *                  @OA\Property(property="data", ref="#/components/schemas/School")
     *              )
     *          }
     *          )
     *      )
     * )
     */
    public function apply(Request $request)
    {
        $user = \Auth::user();
        $params = $request->all();
        \Validator::make($params, [
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:255'
        ])->validate();
        $school = SchoolService::apply($user, $params);
        return $this->success('ok', $school);
    }

    /**
     * @OA\Get(
     *     path="schools/{school_id}/invite_url",
     *     tags={"school"},
     *     summary="create an invite url",
     *     description="create an invite url",
     *     @OA\Parameter(
     *         name="school_id",
     *         in="path",
     *         description="the school id",
     *         required=true
     *     ),
     *     @OA\Response(response="200", description="success", @OA\JsonContent(
     *          allOf={
     *              @OA\Schema(ref="#/components/schemas/ApiResponse"),
     *              @OA\Property(property="data", type="string", description="the invite url")
     *          }
     *          )
     *      )
     * )
     */
    public function makeInviteUrl(School $school)
    {
        $this->checkSchoolPrincipal($school);
        $service = new SchoolService($school);

        return $this->success('ok', $service->createInviteUrl());
    }

    /**
     * @OA\Post(
     *     path="schools/{school_id}/students",
     *     tags={"school"},
     *     summary="create a student account of this school",
     *     description="create a student account of this school",
     *     @OA\RequestBody(
     *          request="student info",
     *          required=true,
     *          description="student info",
     *          @OA\JsonContent(
     *              @OA\Property(property="name", type="string", description="the name of student account"),
     *              @OA\Property(property="username", type="string", description="the username of student account"),
     *              @OA\Property(property="password", type="string", description="the password of student account"),
     *          )
     *     ),
     *     @OA\Response(response="200", description="success", @OA\JsonContent(
     *          allOf={
     *              @OA\Schema(ref="#/components/schemas/ApiResponse"),
     *              @OA\Schema(
     *                  @OA\Property(property="data", ref="#/components/schemas/UserSchema")
     *              )
     *          }
     *          )
     *      )
     * )
     */
    public function createStudent(School $school, Request $request)
    {
        $this->checkSchoolPrincipal($school);
        $params = $request->all();
        \Validator::make($params, [
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255',
            'password' => 'required|string|max:255',
        ])->validate();
        $student = (new SchoolService($school))->addStudent($params);

        return $this->success('ok', $student);
    }

    /**
     * @OA\Get(
     *     path="schools/{school_id}/students",
     *     tags={"school"},
     *     summary="get students of this school",
     *     description="get students of this school, support laravel paginater",
     *     @OA\Parameter(
     *         name="focused",
     *         in="query",
     *         description="if focused== 'true', the api will return students focused current teacher only",
     *         required=false
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
    public function getStudents(School $school, Request $request)
    {
        $this->checkSchoolUser($school);
        if ($request->input('focused', 'false') == 'true') {
            $teacher = \Auth::user();
            $relations = Relation::where('to', $teacher->id)->simplePaginate()->toArray();
            $studentIds = [];
            foreach ($relations['data'] as $relation) {
                $studentIds[] = $relation['from'];
            }
            $relations['data'] = Student::whereIn('id', $studentIds)->get();
            $students = $relations;
        } else {
            $students = Student::where('school_id', $school->id)->simplePaginate();
        }

        return $this->success('ok', $students);
    }

    /**
     * @OA\Get(
     *     path="schools/{school_id}/teachers",
     *     tags={"school"},
     *     summary="get teachers of this school",
     *     description="get students of this school, support laravel paginater",
     *     @OA\Response(response="200", description="success", @OA\JsonContent(
     *          allOf={
     *              @OA\Schema(ref="#/components/schemas/ApiResponse"),
     *              @OA\Schema(
     *                  @OA\Property(property="data", @OA\Items(@OA\Schema(
     *                  allOf={
     *                      @OA\Schema(ref="#/components/schemas/UserSchema"),
     *                      @OA\Property(property="is_focused", type="boolean"),
     *                  }
     *                  )))
     *              )
     *          }
     *          )
     *      )
     * )
     */
    public function getTeachers(School $school)
    {
        $user = \Auth::user();
        $this->checkSchoolUser($school);
        $teachers = Teacher::where('school_id', $school->id)->simplePaginate();
        if ($user instanceof Student) {
            $teachers = $teachers->toArray();
            array_walk($teachers['data'], function (&$teacher) use ($user) {
                //this could be cached
                $teacher['is_focused'] = boolval(RelationService::hasRelation($user->id, $teacher['id']));
            });
        }

        return $this->success('ok', $teachers);
    }

    /**
     * @OA\Get(
     *     path="schools",
     *     tags={"school"},
     *     summary="get schools applied by current user",
     *     description="get schools applied by current user",
     *     @OA\Response(response="200", description="success", @OA\JsonContent(
     *          allOf={
     *              @OA\Schema(ref="#/components/schemas/ApiResponse"),
     *              @OA\Schema(
     *                  @OA\Property(property="data", ref="#/components/schemas/School")
     *              )
     *          }
     *          )
     *      )
     * )
     */
    public function getSchools()
    {
        $user = \Auth::user();
        if ($user->school_id) {
            throw new Exception('should be principal');
        }
        $schools = School::where('creator_id', $user->id)->get();

        return $this->success('ok', $schools);
    }

    protected function checkSchoolPrincipal(School $school)
    {
        $principal = \Auth::user();
        if ($school->creator_id != $principal->id) {
            throw new Exception('should be principal');
        }
    }

    protected function checkSchoolTeacher(School $school)
    {
        $user = \Auth::user();
        if (!$user instanceof Teacher) {
            throw new Exception('should be school teacher');
        }
        $this->checkSchoolUser($school);
    }

    protected function checkSchoolUser(School $school)
    {
        $user = \Auth::user();
        if ($user->school_id != $school->id && $user->id != $school->creator_id) {
            throw new Exception('should be school ' . class_basename($user));
        }
    }
}
