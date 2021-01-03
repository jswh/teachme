<?php
namespace App\Services;

use App\Models\Relation;
use App\Models\Student;
use App\Models\Teacher;
use Exception;

class RelationService {
    const TYPE_STUDENT_FOCUS_TEACHER = 1;

    public function __construct($fromUser) {
        $this->user = $fromUser;
    }

    public function focusTeacher(Teacher $teacher) {
        $this->checkTeacherRealtionAvailiable($teacher);
        $relation = Relation::create([
            'from' => $this->user->id,
            'to' => $teacher->id,
            'type' => self::TYPE_STUDENT_FOCUS_TEACHER
        ]);

        return $relation;
    }

    public function unfocusTeacher(Teacher $teacher) {
        $this->checkTeacherRealtionAvailiable($teacher);
        return Relation::where('from', $this->user->id)
            ->where('to', $teacher->id)
            ->where('type', self::TYPE_STUDENT_FOCUS_TEACHER)
            ->delete();

    }

    public static function hasRelation($fromId, $toId, $type = 1) {
        return Relation::where('from', $fromId)->where('to', $toId)->where('type', $type)->first();
    }

    protected function checkTeacherRealtionAvailiable(Teacher $teacher) {
        if (!$this->user instanceof Student || $this->user->school_id !== $teacher->school_id) {
            throw new Exception('relation action not available');
        }
    }
}
