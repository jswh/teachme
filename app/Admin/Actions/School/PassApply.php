<?php

namespace App\Admin\Actions\School;

use App\Models\School;
use Encore\Admin\Actions\RowAction;
use Illuminate\Database\Eloquent\Model;

class PassApply extends RowAction
{
    public $name = 'PassApply';

    public function handle(School $model)
    {
        $model->state = School::STATE_NORMAL;
        $model->save();

        return $this->response()->success('Success message.')->refresh();
    }

}
