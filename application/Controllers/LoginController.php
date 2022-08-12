<?php

namespace Application\Controllers;

use Application\Core\Router;
use Application\Core\Controller;

class LoginController extends Controller
{
    use SessionsTrait;
    use PostTrait;
    use WithoutActionTrait;
    use ModelTrait;
    use ViewTrait;

    public function action(): void
    {
        if ($this->isAuthorizided()) {
            Router::redirect('/');
        } elseif ($this->isPost()) {
            $this->setModel('Login')->setSqlConnection(LOGIN_MODE);
            switch (true) {
                case ($this->isPost('login', 'password')):
                    if ($this->isPost('email_code')) {
                        if ($this->model->getConfirmResult($this->getPost('login', 'password', 'email_code'))) {
                            $this->setSession('idUser', $this->model->getAccountID($this->getPost('login', 'password')));
                            $this->setSession('role', $this->model->getAccountRoles($_SESSION['idUser']));
                            echo json_encode(['confirmResult' => true]);
                        } else {
                            echo json_encode(['confirmResult' => false]);
                        }
                    } else {
                        echo $this->model->getLoginResult($this->getPost('login', 'password')); 
                    }
                    break;
                case ($this->isPost('forgotten_account_email')):
                    $this->model->restoreAccount($this->getPost('forgotten_account_email'));
                    Router::redirect('/Login');
                    break;
                default:
                    Router::notFound();
                    break;
            }
        } else {
            echo $this->setView('Login', 'WindowTemplate')->setDecorator('Login', $this)->render();
        }
    }
}
