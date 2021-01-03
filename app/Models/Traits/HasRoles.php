<?php
namespace App\Models\Traits;

use App\Models\Role;
use App\Models\TeacherRole;

trait HasTeacherRoles {
    public function hasRole(Role $role) {
        return $this->roleQuery($role)->exists();
    }

    public function addRole(Role $role) {
        return TeacherRole::create(['teacher_id' => $this->id, 'role_id' => $role->id]);
    }

    public function removeRole(Role $role) {
        return $this->roleQuery($role)->delete();
    }

    protected function roleQuery(Role $role) {
        return TeacherRole::where('teacher_id', $this->id)->where('role_id', $role->id);
    }
}
