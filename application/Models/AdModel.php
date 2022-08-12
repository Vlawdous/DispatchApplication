<?php

namespace Application\Models;

use Application\Core\Model;

class AdModel extends Model
{
    use SqlTrait;

    public function getAds(string|int $idUser): array
    {
        $result = [];
        $index = 0;
        $resultQuery['adresses'] = $this->sql->query("SELECT adress FROM apartments WHERE personal_account = 
        (SELECT personal_account FROM personal_accounts WHERE id_account = 
        $idUser)");
        if ($resultQuery['adresses'] === false) {
            return [];
        }
        $resultQuery['adresses'] = $resultQuery['adresses']->fetchAll(\PDO::FETCH_ASSOC);
        foreach ($resultQuery['adresses'] as $adress) {
            $resultQuery['ads'] = $this->sql->query("SELECT description, date_start, date_end FROM planned_works WHERE date_end >= current_timestamp AND adress = '{$adress['adress']}' ORDER BY date_start");
            if ($resultQuery['ads'] === false) {
                $error = $this->sql->errorInfo();
                continue;
            }
            $resultQuery['ads'] = $resultQuery['ads']->fetchAll();
            foreach ($resultQuery['ads'] as $ad) {
                $result[$index]['adress'] = $adress['adress'];
                $result[$index]['description'] = $ad['description'];
                $result[$index]['dateStart'] = $ad['date_start'];
                $result[$index]['dateEnd'] = $ad['date_end'];
                $index++;
            }
        }
        return $result;
    }
}
