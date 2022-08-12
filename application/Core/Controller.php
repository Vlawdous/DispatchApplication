<?php

namespace Application\Core;

abstract class Controller
{
    protected ?array $action;

    protected function __construct(?array $action = null)
    {
        $this->action = $action;
    }

    public static function createController(array $parseInfo): Controller
    {
        $class = $parseInfo[0];
        $action = isset($parseInfo[1]) ? $parseInfo[1] : null;
        $classController = "Application\Controllers\\{$class}Controller";
        return new $classController($action);
    }

    abstract public function action(): void;
}
