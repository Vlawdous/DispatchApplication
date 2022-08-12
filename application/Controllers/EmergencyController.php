<?php

namespace Application\Controllers;

use Application\Core\Router;
use Application\Core\Controller;

class EmergencyController extends Controller
{
    use SessionsTrait;
    use PostTrait;
    use WithoutActionTrait;
    use ModelTrait;
    use ViewTrait;

    public function action(): void
    {
        if ($this->isInhabitant()) {
            $this->setModel('Emergency')->setSqlConnection(INHABITANT_MODE);
            if ($this->isPost()) {
                if ($this->isPost('emergency_call', 'apartment')) {
                    $this->model->setEmergencyCall($this->getPost('emergency_call', 'apartment'), $_SESSION['idUser']);
                    Router::redirect('/Emergency');
                } else {
                    Router::notFound();
                }
            } else {
                echo $this->setView('Emergency', 'WindowTemplate')->setDecorator('Emergency', $this, $this->model)->render();
            }
        } else {
            Router::notFound();
        }
    }
}
