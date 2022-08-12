<?php

namespace Application\Core;

abstract class Model
{
    protected function __construct()
    {
    }

    public static function createModel(string $class): Model
    {
        $className = "Application\Models\\{$class}Model";
        return new $className();
    }
}
