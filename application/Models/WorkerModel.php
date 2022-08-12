<?php

namespace Application\Models;

use Application\Core\Model;

class WorkerModel extends Model
{
    use SqlTrait;

    private function getAdsByTableName(string $typeWork, string|int $idWorker): array
    {
        $result = [];
        $index = 0;
        $id = 'id_' . $typeWork;
        $idWorks = $this->sql->query("SELECT $id FROM {$typeWork}_workers WHERE id_worker = {$idWorker}");
        if ($idWorks === false) {
            return [];
        }
        $idWorks = $idWorks->fetchAll();
        $selectingGeo = '';
        $havePrice = false;
        switch ($typeWork) {
            case 'planned':
                $selectingGeo = 'adress';
                break;
            case 'emergency':
                $selectingGeo = 'adress, number_apartment';
                break;
            case 'paid':
                $selectingGeo = 'full_adress';
                $havePrice = true;
                break;
        }
        foreach ($idWorks as $work) {
            $idWork = $work[$id];
            $query = "SELECT {$id}, description, date_start, date_end, {$selectingGeo}" . (($havePrice) ? ', price' : '') . " FROM {$typeWork}_works WHERE $id = {$idWork} AND (date_end IS NULL OR date_end > current_timestamp)";
            $workInfo = $this->sql->query($query);
            if ($workInfo === false) {
                return [];
            } else {
                $workInfo = $workInfo->fetchAll();
            }
            foreach ($workInfo as $ad) {
                $result[$index]['id'] = $ad[$id];
                if (isset($ad['price'])) {
                    $result[$index]['price'] = $ad['price'];
                }
                $result[$index]['typeWork'] = $typeWork;
                $result[$index]['description'] = $ad['description'];
                $result[$index]['dateStart'] = $ad['date_start'];
                $result[$index]['dateEnd'] = $ad['date_end'];
                switch ($typeWork) {
                    case 'planned':
                        $result[$index]['geo'] = $ad['adress'];
                        break;
                    case 'emergency':
                        $result[$index]['geo'] = $ad['adress'] . ', кв. ' . $ad['number_apartment'];
                        break;
                    case 'paid':
                        $result[$index]['geo'] = $ad['full_adress'];
                        break;
                }
                $index++;
            }
        }
        return $result;
    }

    public function getAds(string|int $idUser): array
    {
        $result = [];
        $idWorker = $this->sql->query("SELECT id_worker FROM workers WHERE id_account = {$idUser}");
        if ($idWorker === false) {
            return [];
        } else {
            $idWorker = $idWorker->fetch()[0];
        }
        $result = [ ...$this->getAdsByTableName('emergency', $idWorker), ...$this->getAdsByTableName('planned', $idWorker), ...$this->getAdsByTableName('paid', $idWorker)];
        return $result;
    }
    public function setWorkReady(array $workInfoFromPost): void
    {
        $idWork = $workInfoFromPost['id_work'];
        $typeWork = $workInfoFromPost['type_work'];
        $dateEnd = date("Y-m-d H:i:s");
        $this->sql->exec("UPDATE {$typeWork}_works SET date_end = '{$dateEnd}' WHERE id_{$typeWork} = {$idWork}");
    }
}
