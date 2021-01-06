<?php

namespace App\Admin\Controllers;

use App\Models\School;
use \App\Models\Teacher;
use App\Services\SchoolService;
use App\Services\ScopeService;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class TeacherController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Teacher';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Teacher());

        $grid->column('id', __('Id'));
        $grid->column('school.name', __('School'));
        $grid->column('name', __('Name'));
        $grid->column('email', __('Email'));
        $grid->column('roles', __('Roles'));
        $grid->column('line_user_id', __('Line id'));
        $grid->column('created_at', __('Created at'));
        $grid->column('updated_at', __('Updated at'))->hide();
        $grid->column('deleted_at', __('Deleted at'))->hide();

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(Teacher::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('school_id', __('School id'));
        $show->field('name', __('Name'));
        $show->field('email', __('Email'));
        $show->field('roles', __('Roles'));
        $show->field('line_user_id', __('Line id'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));
        $show->field('deleted_at', __('Deleted at'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Teacher());
        if ($form->isCreating()) {
            $form->select('school_id', __('School'))->options(SchoolService::selectSchoolOptions());
        } else {
            $form->display('school_id', __('School'))->with(function ($value) {
                $school = School::find($value);
                return $school ? $school->name : '-';
            });
        }
        $form->saving(function (Form $form) {
            $teacher = Teacher::find($form->id);
            if (!$teacher || $form->password !== $teacher->password) {
                $form->password = bcrypt($form->password);
            }
        });
        $form->text('name', __('Name'));
        $form->email('email', __('Email'));
        $form->select('roles', __('Roles'))->options(ScopeService::selectOptions());
        $form->text('line_user_id', __('Line id'));
        $form->password('password', __('Password'));

        return $form;
    }
}
