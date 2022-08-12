<?php

namespace Application\Controllers;

use Application\Core\Router;
use Application\Core\Controller;

class PayController extends Controller
{
    use SessionsTrait;
    use PostTrait;
    use WithoutActionTrait;
    use ModelTrait;
    use ViewTrait;

    public function action(): void
    {
        if ($this->isInhabitant()) {
            $this->setModel('Pay')->setSqlConnection(INHABITANT_MODE);
            if ($this->isPost()) {
                switch (true) {
                    case ($this->isPost('meter_data', 'id_meter')):
                        echo $this->model->setMeterData($this->getPost('meter_data', 'id_meter'), $_SESSION['idUser']);
                        break;
                    default:
                        Router::notFound();
                        break;
                }
            } else {
                echo $this->setView('Pay', 'WindowTemplate')->setDecorator('Pay', $this, $this->model)->render();
            }
        } else {
            Router::notFound();
        }
    }
}
