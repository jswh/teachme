<?php

namespace App\Http\Controllers\Api;

use App\Models\Teacher;
use App\Services\RelationService;

class RelationController extends ApiController
{
    public function focus(Teacher $teacher) {
        $user = \Auth::user();
        $relation = (new RelationService($user))->focusTeacher($teacher);

        return $this->success('ok', $relation);
    }

    public function unfocus(Teacher $teacher) {

        $user = \Auth::user();
        $result = (new RelationService($user))->unfocusTeacher($teacher);

        return $this->success('ok', $result);
    }

}
