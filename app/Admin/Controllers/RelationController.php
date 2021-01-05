<?php

namespace App\Admin\Controllers;

use \App\Models\Relation;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class RelationController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Relation';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Relation());

        $grid->column('id', __('Id'));
        $grid->column('from', __('From'));
        $grid->column('to', __('To'));
        $grid->column('type', __('Type'));
        $grid->column('created_at', __('Created at'));
        $grid->column('updated_at', __('Updated at'));

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
        $show = new Show(Relation::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('from', __('From'));
        $show->field('to', __('To'));
        $show->field('type', __('Type'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Relation());

        $form->number('from', __('From'));
        $form->number('to', __('To'));
        $form->number('type', __('Type'))->default(1);

        return $form;
    }
}
