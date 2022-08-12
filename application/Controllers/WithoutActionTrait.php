<?php

namespace Application\Controllers;

use Application\Core\Router;

trait WithoutActionTrait
{
    public function __construct(?array $action)
    {
        if ($action !== null) {
            Router::notFound();
        }
    }
}
