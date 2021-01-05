<?php

namespace App\Admin\Controllers;

use \App\Models\School;
use App\Models\Teacher;
use App\Services\ScopeService;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class SchoolController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'School';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new School());

        $grid->column('id', __('Id'));
        $grid->column('name', __('Name'));
        $grid->column('description', __('Description'));
        $grid->column('creator.name', __('Creator'));
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
        $show = new Show(School::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('name', __('Name'));
        $show->field('description', __('Description'));
        $show->field('creator_id', __('Creator id'));
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
        $form = new Form(new School());
        $creators = [];
        $teachers = Teacher::where('roles', ScopeService::SCOPE_PRINCIPAL)->select(['id', 'name'])->get();
        foreach($teachers as $teacher) {
            $creators[$teacher->id] = $teacher->name;
        }
        $form->text('name', __('Name'));
        $form->text('description', __('Description'));
        $form->select('creator_id', __('Creator'))->options($creators);

        return $form;
    }
}
