<?php

namespace Application\Controllers;

use Application\Core\Router;
use Application\Core\Controller;

class AdController extends Controller
{
    use SessionsTrait;
    use ModelTrait;
    use WithoutActionTrait;
    use ViewTrait;

    public function action(): void
    {
        if ($this->isInhabitant()) {
            $this->setModel('Ad')->setSqlConnection(INHABITANT_MODE);
            echo $this->setView('Ad', 'WindowTemplate')->setDecorator('Ad', $this, $this->model)->render();
        } else {
            Router::notFound();
        }
    }
}
