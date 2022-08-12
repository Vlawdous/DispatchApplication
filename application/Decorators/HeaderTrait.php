<?php

namespace Application\Decorators;

trait HeaderTrait
{
    public function getProfile(): string
    {
        $href = ($this->controller->isAuthorizided()) ? '/Profile' : '/Login';
        return "<div><div class='logo_box'><a href='{$href}'><img src='./images/logo.svg' class='logo'></a></div></div>";
    }
    public function getMenuItems(): string
    {
        $result = '';
        if ($this->controller->isInhabitant()) {
            $result .= $this->getInhabitantMenu();
        }
        if ($this->controller->isDispatcher()) {
            $result .= $this->getDispatherMenu();
        }
        if ($this->controller->isWorker()) {
            $result .= $this->getWorkerMenu();
        }
        return $result;
    }
    private function getDispatherMenu(): string
    {
        return '<a href="/Dispatcher">Меню диспетчера</a>';
    }
    private function getInhabitantMenu(): string
    {
        return '<a href="/Ad">Объявления</a><a href="/Emergency">Аварийная ситуация</a><a href="/Pay">Оплата</a>';
    }
    private function getWorkerMenu(): string
    {
        return '<a href="/Worker">Список заявок и работ</a>';
    }
}
