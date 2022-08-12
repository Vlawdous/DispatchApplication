<?php

namespace Application\Controllers;

use Application\Core\Router;
use Application\Core\Controller;

class WorkerController extends Controller
{
    use SessionsTrait;
    use PostTrait;
    use WithoutActionTrait;
    use ModelTrait;
    use ViewTrait;

    public function action(): void
    {
        if ($this->isWorker()) {
            $this->setModel('Worker')->setSqlConnection(WORKER_MODE);
            if ($this->isPost()) {
                switch (true) {
                    case ($this->isPost('id_work', 'type_work')):
                        $this->model->setWorkReady($this->getPost('id_work', 'type_work'));
                        Router::redirect('/Worker');
                        break;
                    default:
                        Router::notFound();
                        break;
                }
            } else {
                echo $this->setView('Worker', 'WindowTemplate')->setDecorator('Worker', $this, $this->model)->render();
            }
        } else {
            Router::notFound();
        }
    }
}
