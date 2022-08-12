<?php

namespace Application\Decorators;

use Application\Core\Decorator;

class AdDecorator extends Decorator
{
    use HeaderTrait;
    use FooterTrait;

    public function getBody(): string
    {
        $result = '';
        $ads = $this->model->getAds($this->controller->getUserId());
        if ($ads === []) $result .= '<p>Нет объявлений для вашего дома</p>';
        foreach ($ads as $ad) {
            $adress = $ad['adress'];
            $description = $ad['description'];
            $dateStart = $ad['dateStart'];
            $dateEnd = $ad['dateEnd'];
            $result .= "
                <div class = 'ad'>
                    <h2>Адрес</h2>
                    <p>$adress</p>
                    <h2>Описание</h2>
                    <p>$description</p>
                    <h2>Время проведения</h2>
                    <p>$dateStart - $dateEnd</p>
                </div>";
        }
        return "<div class = 'ads scrolling_window'><h1>Объявления</h1>$result</div>";
    }
}
