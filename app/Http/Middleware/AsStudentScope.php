<?php

namespace App\Http\Middleware;

use App\Models\Student;
use App\Services\ScopeService;
use Closure;
use Illuminate\Support\Facades\Config;

class AsStudentScope
{
    public function handle($request, Closure $next)
    {
        $params = $request->all();
        $scope = $params['scope'] ?? '';
        if (strpos($scope, ScopeService::SCOPE_STUDENT) !== false) {
            Config::set('auth.guards.api.provider', 'students');
        }

        return $next($request);
    }
}
