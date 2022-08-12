<?php

namespace Application\Views;

use Application\Core\Model;
use Application\Core\View;
use Application\Core\Controller;
use Application\Core\Decorator;

trait DecoratorTrait
{
    private ?Decorator $decorator = null;

    public function setDecorator(string $class, ?Controller $controller = null, ?Model $model = null): View
    {
        $this->decorator = Decorator::createDecorator($controller, $model, $class);
        return $this;
    }
}
