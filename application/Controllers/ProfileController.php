<?php

namespace Application\Controllers;

use Application\Core\Router;
use Application\Core\Controller;

class ProfileController extends Controller
{
    use SessionsTrait;
    use PostTrait;
    use WithoutActionTrait;
    use ModelTrait;
    use ViewTrait;

    public function action(): void
    {
        if ($this->isAuthorizided()) {
            if ($this->isPost()) {
                $this->setModel('Profile')->setSqlConnection(LOGIN_MODE);
                switch (true) {
                    case ($this->isPost('old_login', 'new_login', 'new_login_repeat')):
                        echo $this->model->changeLogin($this->getPost('old_login', 'new_login', 'new_login_repeat'), $_SESSION['idUser']);
                        break;
                    case ($this->isPost('old_password', 'new_password', 'new_password_repeat')):
                        echo $this->model->changePassword($this->getPost('old_password', 'new_password', 'new_password_repeat'), $_SESSION['idUser']);
                        break;
                    case ($this->isPost('new_email')):
                        $this->model->changeEmail($this->getPost('new_email'));
                        break;
                    case ($this->isPost('email_code')):
                        echo $this->model->confirmEmail($this->getPost('email_code'), $_SESSION['idUser']);
                        break;
                    case ($this->isPost('wish')):
                        $this->model->addWish($this->getPost('wish'), $_SESSION['idUser']);
                        break;
                    case ($this->isPost('clear')):
                        unset($_SESSION['idUser']);
                        unset($_SESSION['idUser']);
                        Router::redirect('/');
                        break;
                    default:
                        Router::notFound();
                        break;
                }
            } else {
                echo $this->setView('Profile', 'WindowTemplate')->setDecorator('Profile', $this)->render();
            }
        } else {
            Router::notFound();
        }
    }
}
