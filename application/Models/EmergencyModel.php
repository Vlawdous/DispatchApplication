<?php

namespace Application\Models;

use Application\Core\Model;

class EmergencyModel extends Model
{
    use SqlTrait;

    public function setEmergencyCall(array $emergencyInfo, string|int $idUser): void
    {
        $call = $emergencyInfo['emergency_call'];
        $apartment = explode(':', $emergencyInfo['apartment']);
        $adress = $apartment[0];
        $numberApartment = $apartment[1];
        $dateStart = date("Y-m-d H:i:s");
        $this->sql->exec("INSERT INTO emergency_works VALUES (DEFAULT, '$adress', $numberApartment, '$call', '$dateStart', NULL)");
    }
    public function getApartments(string|int $idUser): array
    {
        $resultQuery = $this->sql->query("SELECT adress, number_apartment FROM apartments WHERE personal_account = (SELECT personal_account FROM personal_accounts WHERE id_account = {$idUser})");
        $result = [];
        $index = 0;
        while ($row = $resultQuery->fetch()) {
            $result[$index]['adress'] = $row['adress'];
            $result[$index]['numberApartment'] = $row['number_apartment'];
            $index++;
        }
        return $result;
    }
    public function getAllCalls(string|int $idUser): array
    {
        $result = [];
        $index = 0;
        $apartments = $this->getApartments($idUser);
        foreach ($apartments as $apartment) {
            $adress = $apartment['adress'];
            $numberApartment = $apartment['numberApartment'];
            $resultQuery = $this->sql->query("SELECT id_emergency, description, date_start FROM emergency_works WHERE adress = '{$adress}' AND number_apartment = '{$numberApartment}' AND date_end IS NULL ORDER BY date_start")->fetchAll();
            foreach ($resultQuery as $call) {
                $idEmergency = $call['id_emergency'];
                $result[$index]['isProccessed'] =
                ($this->sql->query("SELECT EXISTS(SELECT * FROM emergency_workers WHERE id_emergency = $idEmergency)")->fetch()['exists'])
                ? true : false;
                $result[$index]['adress'] = $adress;
                $result[$index]['numberApartment'] = $numberApartment;
                $result[$index]['description'] = $call['description'];
                $result[$index]['dateStart'] = $call['date_start'];
                $index++;
            }
        }
        return $result;
    }
}
