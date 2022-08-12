<?php

namespace Application\Controllers;

use Application\Core\Router;
use Application\Core\Controller;

class DispatcherController extends Controller
{
    use SessionsTrait;
    use PostTrait;
    use ModelTrait;
    use ViewTrait;

    private ?string $idUpdate = null;

    public function action(): void
    {
        if ($this->isDispatcher()) {
            $this->setModel('Dispatcher')->setSqlConnection(DISPATCHER_MODE);
            if ($this->isPost('id_work')) {
                $this->idUpdate = $_POST['id_work'];
                unset($_POST['id_work']);
            }
            if ($this->isPost('filter') || $this->isPost('page')) {
                if ($this->isPost('filter')) {
                    $this->model->setFilters($this->getPost('filter'));
                }
                if ($this->isPost('page')) {
                    $this->model->setPage($_POST['page']);
                }
                if ((!isset($this->action[1]) || $this->action[1] !== 'Delete')) {
                    echo json_encode(['newTable' => $this->setView('Dispatcher')->setDecorator('Dispatcher', $this, $this->model)->renderTable($this->model->getTable(constant(mb_strtoupper($this->action[0]) . '_TABLE')))]);
                    return;
                }
            }

            if ($this->isPost()) {
                switch (true) {
                    case ($this->action[1] === 'Add'):
                        switch ($this->action[0]) {
                            case 'Emergency':
                                if ($this->isPost('adress', 'number_apartment', 'description', 'date_start', 'id_workers')) {
                                    echo json_encode($this->model->insertIntoTable(EMERGENCY_TABLE, $this->getPost('adress', 'number_apartment', 'description', 'date_start', 'id_workers')));
                                } else {
                                    Router::notFound();
                                }
                                break;
                            case 'Planned':
                                if ($this->isPost('adress', 'description', 'date_start', 'date_end', 'id_workers')) {
                                    echo json_encode($this->model->insertIntoTable(PLANNED_TABLE, $this->getPost('adress', 'description', 'date_start', 'date_end', 'id_workers')));
                                } else {
                                    Router::notFound();
                                }
                                break;
                            case 'Paid':
                                if ($this->isPost('full_adress', 'description', 'price', 'date_start', 'id_workers')) {
                                    echo json_encode($this->model->insertIntoTable(PAID_TABLE, $this->getPost('full_adress', 'description', 'price', 'date_start', 'id_workers')));
                                } else {
                                    Router::notFound();
                                }
                                break;
                            default:
                                Router::notFound();
                                break;
                        }
                        break;
                    case ($this->action[1] === 'Delete'):
                        if (!isset($this->idUpdate)) {
                            Router::notFound();
                        }
                        if (($this->action[0] === 'Emergency') || ($this->action[0] === 'Planned') || ($this->action[0] === 'Paid')) {
                            echo json_encode([...$this->model->deleteFromTable(constant(mb_strtoupper($this->action[0]) . '_TABLE'), $this->idUpdate),
                            'newTable' => $this->setView('Dispatcher')->setDecorator('Dispatcher', $this, $this->model)->renderTable($this->model->getTable(constant(mb_strtoupper($this->action[0]) . '_TABLE')))]);
                        } else {
                            Router::notFound();
                        }
                        break;
                    case ($this->action[1] === 'Update'):
                        if ((!isset($this->idUpdate)) && (!$this->isPost('name_item'))) {
                            Router::notFound();
                        }
                        switch ($this->action[0]) {
                            case 'Emergency':
                                if ($this->isPost('adress', 'number_apartment', 'description', 'date_start', 'id_workers')) {
                                    echo json_encode($this->model->updateWorkIntoTable(EMERGENCY_TABLE, $this->getPost('adress', 'number_apartment', 'description', 'date_start', 'id_workers'), $this->idUpdate));
                                } else {
                                    Router::notFound();
                                }
                                break;
                            case 'Planned':
                                if ($this->isPost('adress', 'description', 'date_start', 'date_end', 'id_workers')) {
                                    echo json_encode($this->model->updateWorkIntoTable(PLANNED_TABLE, $this->getPost('adress', 'description', 'date_start', 'date_end', 'id_workers'), $this->idUpdate));
                                } else {
                                    Router::notFound();
                                }
                                break;
                            case 'Paid':
                                if ($this->isPost('full_adress', 'description', 'price', 'date_start', 'id_workers')) {
                                    echo json_encode($this->model->updateWorkIntoTable(PAID_TABLE, $this->getPost('full_adress', 'description', 'price', 'date_start', 'id_workers'), $this->idUpdate));
                                } else {
                                    Router::notFound();
                                }
                            case 'Items':
                                if ($this->isPost('name_item', 'count', 'id_worker')) {
                                    echo json_encode($this->model->updateItemIntoTable($this->getPost('name_item', 'count', 'id_worker')));
                                } else {
                                    Router::notFound();
                                }
                                break;
                            default:
                                Router::notFound();
                                break;
                        }
                        break;
                    case ($this->action[0] === 'Messages') && ($this->isPost('reject_id')):
                        $this->model->rejectMessage($_POST['reject_id']);
                        Router::redirect('/Dispatcher/Messages');
                        break;
                    case ($this->action[0] === 'Messages') && ($this->isPost('accept_id')):
                        $this->model->acceptMessage($_POST['accept_id']);
                        Router::redirect('/Dispatcher/Messages');
                        break;
                    default:
                        Router::notFound();
                        return;
                }
            } else {
                echo $this->setView('Dispatcher')->setDecorator('Dispatcher', $this, $this->model)->render();
            }
        } else {
            Router::notFound();
        }
    }
    public function getAction(): ?array
    {
        return $this->action;
    }
    public function getIdUpdate(): ?string
    {
        return $this->idUpdate;
    }
}
