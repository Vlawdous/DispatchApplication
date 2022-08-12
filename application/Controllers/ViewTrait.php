<?php

namespace Application\Controllers;

use Application\Core\View;
use Application\Core\Decorator;

trait ViewTrait
{
    private ?View $view = null;

    public function setView(string $class, ?string $templateName = null): View
    {
        $templateName = (is_null($templateName)) ? $class . 'Template' : $templateName;
        $this->view = View::createView($class, $templateName);
        return $this->view;
    }
}
