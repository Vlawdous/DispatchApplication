<?php

namespace Application\Decorators;

trait FooterTrait
{
    public function getFooterItems(): string
    {
        $result = '';
        if ($this->controller->isWorker() || $this->controller->isDispatcher()) {
            $result .= $this->getInstruction();
        } else {
            $result .= $this->getAdress() . $this->getContacts();
        }
        return $result;
    }
    private function getAdress(): string
    {
        return '<div class="adress">
            <p>Наши адреса</p>
            <ul><li>г. Новосибирск, ул. Большевистская, д. 175/6</li>
            <li>г. Новосибирск, ул. Никитина, д. 72</li>
            <li>г. Новосибирск, ул. Ленина, д. 11</li></ul>
        </div>';
    }
    private function getContacts(): string
    {
        return '<div class="contacts">
            <p>Контакты</p>
            <ul><li>тел. 123-456-789</li>
            <li>HappyHouseNSK@yandex.ru</li>
            <li>vk.com/HappyHouseNSK</li></ul>
        </div>';
    }
    private function getInstruction(): string
    {
        return '<div class="instruction">
            <p>Инструкция пользования</p>
            <ul><li>Диспетчер: ссылка</li>
            <li>Инженер: ссылка</li></ul>
        </div>';
    }
}
