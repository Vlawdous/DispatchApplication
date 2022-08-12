<?php

namespace Application\Decorators;

use Application\Core\Decorator;

class PayDecorator extends Decorator
{
    use HeaderTrait;
    use FooterTrait;

    public function getBody(): string
    {
        $debt = $this->model->getDebt($this->controller->getUserId());
        return "
            <div class = 'pay scrolling_window'>
                <h2>Внести показания счётчиков</h2>
                <form method = 'POST' action=' /Pay' id = 'meters'>
                    <p>Номер счётчика</p>
                    <input type = 'text' required name = 'id_meter' minlength = '20' maxlength = '20'>
                    <span  class = 'error_message'></span>
                    <p>Показания счётчика</p>
                    <input type = 'text' required name = 'meter_data' minlength = '8'  maxlength = '8'>
                    <input type = 'submit' value = 'Отправить'>
                </form>
                <h2>Оплатить счета</h2>
                <p>Долг</p>
                <p>$debt</p>
                <button>Оплатить</button>
            </div>";
    }
}
