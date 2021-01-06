<?php
namespace App\Services;

use App\Models\School;
use App\Models\Student;
use App\Models\Teacher;
use Exception;
use Illuminate\Support\Facades\Request;

class SchoolService {
    /** @var School */
    protected $school;

    public static function selectSchoolOptions() {
        $schoolOptions = [];
        $schools = School::query()->select(['id', 'name'])->get();
        foreach($schools as $school) {
            $schoolOptions[$school->id] = $school->name;
        }

        return $schoolOptions;
    }

    public static function createTeacher($params) {
        $params['password'] = bcrypt($params['password']);
        return Teacher::create($params);
    }

    public static function createPrinciple($params) {
        $params['roles'] = ScopeService::SCOPE_PRINCIPAL;
        $teacher = self::createTeacher($params);
        return $teacher;
    }

    public static function apply(Teacher $principal, $schoolInfo) {
        //TODO check role
        $schoolInfo['creator_id'] = $principal->id;
        $schoolInfo['state'] = 0;
        $school = School::create($schoolInfo);


        return $school;
    }

    public function __construct(School $school)
    {
        $this->school = $school;
    }

    public function createInviteUrl() {
        return trim(Request::server('HTTP_REFERER'), '/') .'/school/' . $this->school->id . '/invite/' . $this->makeToken();
    }

    public function registerTeacher($profile, $token) {
        $this->checkToken($token);
        $profile['school_id'] = $this->school->id;
        $profile['roles'] = ScopeService::SCOPE_TEACHER;
        $teacher = self::createTeacher($profile);
        \Cache::delete($token);

        return $teacher;
    }

    public function addStudent($profile) {
        $profile['school_id'] = $this->school->id;
        $profile['password'] = bcrypt($profile['password']);
        $profile['roles'] = ScopeService::SCOPE_STUDENT;

        $student = Student::create($profile);

        return $student;
    }

    protected function makeToken() {
        $token = md5($this->school->id . time());
        $info = json_encode(['schoolId'=>$this->school->id]);
        \Cache::set($token, $info, 60 * 24);

        return $token;
    }

    protected function checkToken($token) {
        $info = \Cache::get($token);
        $info = @json_decode($info,  true);
        if (!$info) {
            throw new Exception('invalid token');
        }
        if (($info['schoolId'] ?? null) != $this->school->id) {
            throw new \Exception('school not match');
        }
    }
}
