<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\Teacher;
use App\Services\SchoolService;
use Exception;
use Illuminate\Support\Facades\Validator;
use Throwable;

class RegisterController extends ApiController
{
    public function registerPrinciple(Request $request) {
        $params = $request->all();
        try {
            $re = $this->registerValdator($params);
        } catch (Throwable $e) {
            throw new Exception($e->getMessage());
        }
        $principle = SchoolService::createPrinciple($params);

        return $this->success('ok', $principle);
    }

    public function registerTeacher(School $school, Request $request) {
        $params = $request->all();
        $this->registerValdator($params);
        $teacher = ( new SchoolService($school))->registerTeacher($params, $request->input('token'));

        return $this->success('ok', $teacher);
    }

    protected function registerValdator($data) {
        return Validator::make($data, [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:6|confirmed',
        ])->validate();
    }
}
