<?php

namespace App\Admin\Controllers;

use App\Admin\Actions\Student\Notification;
use App\Models\School;
use \App\Models\Student;
use App\Services\SchoolService;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class StudentController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Student';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Student());

        $grid->column('id', __('Id'));
        $grid->column('school.name', __('School'));
        $grid->column('name', __('Name'));
        $grid->column('username', __('Username'));
        $grid->column('line_user_id', __('Line id'));
        $grid->column('created_at', __('Created at'));
        $grid->column('updated_at', __('Updated at'))->hide();
        $grid->column('deleted_at', __('Deleted at'))->hide();
        $grid->actions(function ($actions) {
            $actions->add(new Notification);
        });

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
        $show = new Show(Student::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('school_id', __('School id'));
        $show->field('name', __('Name'));
        $show->field('username', __('Username'));
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
        $form = new Form(new Student());

        if ($form->isCreating()) {
            $form->select('school_id', __('School'))->options(SchoolService::selectSchoolOptions());
        } else {
            $form->display('school_id', __('School'))->with(function ($value) {
                $school = School::find($value);
                return $school ? $school->name : '-';
            });
        }
        $form->text('name', __('Name'));
        $form->text('username', __('Username'));
        $form->text('line_user_id', __('Line id'));

        return $form;
    }
}
