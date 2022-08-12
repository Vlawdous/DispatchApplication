<?php

namespace Application\Controllers;

use Application\Core\Model;

trait ModelTrait
{
    private ?Model $model = null;

    private function setModel(string $class): Model
    {
        $this->model = Model::createModel($class);
        return $this->model;
    }
}
