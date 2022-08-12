<?php

namespace Application\Core;

abstract class Decorator
{
    protected ?Controller $controller;
    protected ?Model $model;

    protected function __construct(?Controller $controller, ?Model $model)
    {
        $this->model = $model;
        $this->controller = $controller;
    }

    public static function createDecorator(?Controller $controller, ?Model $model, string $class): Decorator
    {
        $className = "Application\Decorators\\{$class}Decorator";
        return new $className($controller, $model);
    }
}
