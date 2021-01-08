<?php

namespace App\Admin\Controllers;

use App\Admin\Actions\School\PassApply;
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
    protected $stateOptions = [
        School::STATE_PENDING => 'pending',
        School::STATE_NORMAL => 'normal',
    ];
    protected function grid()
    {
        $grid = new Grid(new School());
        $options = $this->stateOptions;
        $grid->column('id', __('Id'));
        $grid->column('name', __('Name'));
        $grid->column('description', __('Description'));
        $grid->column('creator.name', __('Creator'));
        $grid->column('state', __('State'))->display(function ($state) use($options) {
            return $options[$state] ?? $state;
        });
        $grid->column('created_at', __('Created at'));
        $grid->column('updated_at', __('Updated at'))->hide();
        $grid->column('deleted_at', __('Deleted at'))->hide();

        $grid->actions(function ($actions) {
            $actions->add(new PassApply);
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
        $show = new Show(School::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('name', __('Name'));
        $show->field('description', __('Description'));
        $show->field('creator_id', __('Creator id'));
        $show->field('state', __('State'));
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
        $form->select('state', __('State'))->options($this->stateOptions);
        $form->select('creator_id', __('Creator'))->options($creators);

        return $form;
    }
}
