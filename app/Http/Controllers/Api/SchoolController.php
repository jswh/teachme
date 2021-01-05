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

class SchoolController extends ApiController
{
    public function apply(Request $request) {
        $user = \Auth::user();
        $params = $request->all();
        \Validator::make($params, [
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:255'
        ])->validate();
        $school = SchoolService::apply($user, $params);
        return $this->success('ok', $school);
    }

    public function makeInviteUrl(School $school) {
        $this->checkSchoolPrincipal($school);
        $service = new SchoolService($school);

        return $this->success('ok', $service->createInviteUrl());
    }

    public function createStudent(School $school, Request $request) {
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

    public function getStudents(School $school, Request $request) {
        $this->checkSchoolUser($school);
        if ($request->input('focused', 'false') == 'true') {
            $teacher = \Auth::user();
            $relations = Relation::where('to_id', $teacher->id)->simplePaginate()->toArray();
            $studentIds = [];
            foreach($relations['data'] as $relation) {
                $studentIds[] = $relation['from_id'];
            }
            $relations['data'] = Student::whereIn('id', $studentIds)->get();
            $students = $relations;
        } else {
            $students = Student::where('school_id', $school->id)->simplePaginate();
        }

        return $this->success('ok', $students);
    }

    public function getFocusStudents(School $school) {
        $this->checkSchoolTeacher($school);
        /** @var Teacher */
        $teacher = \Auth::user();
        # TODO fetch students
        $relations = Relation::where('to', $teacher->id)->simplePaginate()->data;

        return $this->success('ok', $students);


    }

    public function getTeachers(School $school) {
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

    public function getSchools() {
        $user = \Auth::user();
        if ($user->school_id) {
            throw new Exception('should be principal');
        }
        $schools = School::where('creator_id', $user->id)->get();

        return $this->success('ok', $schools);
    }

    protected function checkSchoolPrincipal(School $school) {
        $principal = \Auth::user();
        if ($school->creator_id != $principal->id) {
            throw new Exception('should be principal');
        }
    }

    protected function checkSchoolTeacher(School $school) {
        $user = \Auth::user();
        if (!$user instanceof Teacher) {
            throw new Exception('should be school teacher');
        }
        $this->checkSchoolUser($school);
    }

    protected function checkSchoolUser(School $school) {
        $user = \Auth::user();
        if ($user->school_id != $school->id && $user->id != $school->creator_id) {
            throw new Exception('should be school ' . class_basename($user));
        }
    }

}