<?php

namespace Application\Decorators;

use Application\Core\Decorator;
use DateTime;

class WorkerDecorator extends Decorator
{
    use HeaderTrait;
    use FooterTrait;

    public function getBody(): string
    {
        $result = '';
        $ads = $this->model->getAds($this->controller->getUserId());
        if ($ads == []) {
            $result .= '<p>Нет работ для вас</p>';
        }
        uasort(
            $ads,
            function ($a, $b) {
                $aDate = DateTime::createFromFormat('Y-m-d H:i:s', $a['dateStart']);
                $bTime = DateTime::createFromFormat('Y-m-d H:i:s', $b['dateStart']);
                if ($aDate == $bTime) {
                    return 0;
                } else {
                    return (($aDate < $bTime) ? -1 : 1);
                }
            }
        );
        foreach ($ads as $ad) {
            $idWork = $ad['id'];
            $typeWork = $ad['typeWork'];
            $description = $ad['description'];
            $dateStart = $ad['dateStart'];
            $dateEnd = $ad['dateEnd'];
            $geo = $ad['geo'];
            $price = (isset($ad['price'])) ? "<h2>Цена</h2><p>{$ad['price']}</p>" : '';
            $result .= "
                <div class = 'ad " . (($ad['typeWork'] === 'emergency_ad') ? 'emergency' : '') . "'>
                    <h2>Описание</h2>
                    <p>$description</p>
                    <h2>Место</h2>
                    <p>$geo</p>
                    <h2>Время проведения</h2>
                    <p>$dateStart - $dateEnd</p>
                    $price
                    <form method = 'post' action='/Worker'>
                        <input type = 'hidden' name='id_work' value=$idWork>
                        <input type = 'hidden' name='type_work' value=$typeWork>
                        <input type = 'submit' value='Завершить работу'>
                    </form>
                </div>";
        }
        return "<div class = 'ads scrolling_window'><h1>Работы</h1>$result</div>";
    }
}
