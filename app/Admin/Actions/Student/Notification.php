<?php

namespace App\Admin\Actions\Student;

use App\Models\Student;
use App\Notifications\SimpleNotification;
use Encore\Admin\Actions\RowAction;

class Notification extends RowAction
{
    public $name = 'Message';

    public function handle(Student $model, Request $request)
    {
        $type = $request->get('type');
        $message = $request->get('message');
        $notification = new SimpleNotification($message);
        if ($type == 2) {
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
        $this->checkbox('type', 'type')->options($type);
        $this->textarea('message', 'message')->rules('required');
    }

}
