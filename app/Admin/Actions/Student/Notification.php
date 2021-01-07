<?php

namespace App\Admin\Actions\Student;

use App\Models\Student;
use App\Notifications\SimpleNotification;
use Encore\Admin\Actions\RowAction;
use Illuminate\Http\Request;

class Notification extends RowAction
{
    public $name = 'Message';

    public function handle(Student $model, Request $request)
    {
        $type = $request->get('type');
        $message = $request->get('message');
        $notification = new SimpleNotification($message);
        if ($type == 2) {
            var_dump($model);
            if (!$model->line_user_id) {
                return $this->response()->error('student has no line_user_id');
            }
            $notification->viaLine();
        }
        $model->notify($notification);

        return $this->response()->success('Success message.')->refresh();
    }

    public function form() {
        $type = [
            1 => 'notification',
            2 => 'line message'
        ];
        $this->radio('type', 'type')->options($type)->default(1);
        $this->textarea('message', 'message')->rules('required');
    }

}
