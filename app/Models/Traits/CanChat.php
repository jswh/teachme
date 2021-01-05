<?php
namespace App\Models\Traits;

trait CanChat {
    // instance should overwright this function
    protected function getChatIdPrefix() {
        return '_';
    }

    public function getChatIdAttribute() {
        return $this->getChatIdPrefix() . $this->id;
    }
}
