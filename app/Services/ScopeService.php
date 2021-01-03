<?php
namespace App\Services;

use Laravel\Passport\Passport;

class ScopeService {
    const SCOPE_PRINCIPAL = 'as_principal';
    const SCOPE_TEACHER = 'as_teacher';
    const SCOPE_STUDENT = 'as_student';

    public static function registerPassportScope() {
        Passport::tokensCan([
            self::SCOPE_PRINCIPAL => 'the creator of school',
            self::SCOPE_TEACHER => 'teacher of school',
            self::SCOPE_STUDENT => 'student of school',
        ]);
    }

    public static function getAuthProvider($scope) {
        if ($scope === self::SCOPE_STUDENT) {
            return \Auth::createUserProvider('students');
        } else {
            return \Auth::createUserProvider('teachers');
        }
    }

    public static function middlewareTeacher() {
        return self::middleware([self::SCOPE_PRINCIPAL, self::SCOPE_TEACHER]);
    }

    public static function middlewareStudent() {
        return self::middleware(self::SCOPE_STUDENT);
    }

    public static function middlewarePrincipal() {
        return self::middleware(self::SCOPE_PRINCIPAL);
    }

    public static function middleware($scopes) {
        if (! is_array($scopes)) {
            $scopes = [$scopes];
        }
        return 'scope:' . implode(',', $scopes);
    }
}
