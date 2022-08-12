<?php

namespace Application\Decorators;

use Application\Core\Decorator;

class EmergencyDecorator extends Decorator
{
    use HeaderTrait;
    use FooterTrait;

    public function getBody(): string
    {
        $apartments = $this->model->getApartments($this->controller->getUserId());
        $selectApartment = '';
        $calls = '';
        foreach ($apartments as $apartment) {
            $adress = $apartment['adress'];
            $numberApartment = $apartment['numberApartment'];
            $selectApartment .= "<option value='$adress:$numberApartment'>$adress, кв. $numberApartment</option>";
        }
        $allCalls = $this->model->getAllCalls($this->controller->getUserId());
        if (!empty($allCalls)) {
            foreach ($allCalls as $call) {
                $feedBack = ($call['isProccessed']) ? 'заявка получена и обработана диспетчером, в ближайшее время наши инженеры придут к вам.' : 'заявка пока что не подтверждена диспетчером.';
                $adress = $call['adress'];
                $numberApartment = $call['numberApartment'];
                $description = mb_strcut($call['description'], 0, 100);
                $dateStart = $call['dateStart'];
                $calls .=
                "<div class = 'call'>
                    <div class = 'place'>Адрес: $adress, кв. $numberApartment</div>
                    <hr>
                    <div class = 'description'>Описание: $description</div>
                    <hr>
                    <div class = 'date'>Заявка подана: $dateStart</div>
                    <hr>
                    <div class = 'feedback'>Степень готовности заявки: $feedBack</div>
                </div>";
            }
        }
        return "
            <div class = 'emergency scrolling_window'>
                {$calls}
                <h2>Аварийная ситуация</h2>
                <form method = 'POST' action=' /Emergency'>
                    <p>Выберите квартиру.</p>
                    <select name='apartment'>
                        {$selectApartment}
                    </select>
                    <p>Пожалуйста, опишите в подробности ситуацию, которая произошла в вашей квартире. Каждая деталь может помочь исправить вашу проблему быстрее.</p>
                    <textarea name = 'emergency_call' maxlength='10000'></textarea>
                    <input type = 'submit' value = 'Отправить'>
                </form>
            </div>";
    }
}
