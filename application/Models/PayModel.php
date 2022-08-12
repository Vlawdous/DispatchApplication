<?php

namespace Application\Models;

use Application\Core\Model;

class PayModel extends Model
{
    use SqlTrait;

    private function isTrueMeterNumber($idMeter, string|int $idUser): bool
    {
        $resultQuery =  $this->sql->query(
            "SELECT id_account FROM accounts WHERE id_account = 
                (SELECT id_account FROM personal_accounts WHERE personal_account = 
                    (SELECT personal_account FROM apartments WHERE adress = 
                        (SELECT adress FROM meters WHERE number_meter = '{$idMeter}')
                    AND number_apartment = 
                        (SELECT number_apartment FROM meters WHERE number_meter = '{$idMeter}')
                )
            )"
        );
        if ($resultQuery === false) {
            return false;
        } else {
            return ($resultQuery->fetch()[0] == $idUser);
        }
    }
    public function getDebt(string|int $idUser): string|int
    {
        return $this->sql->query("SELECT * FROM personal_accounts WHERE id_account = {$idUser}")->fetch()['debt'];
    }
    public function setMeterData(array $meterDataFromPost, string|int $idUser): string
    {
        $meterData = $meterDataFromPost['meter_data'];
        $idMeter = $meterDataFromPost['id_meter'];
        if ($this->isTrueMeterNumber($idMeter, $idUser)) {
            $date = date('Y-m-d');
            $query = "INSERT INTO meters_readings VALUES ('{$idMeter}', {$meterData}, '{$date}')";
            $this->sql->exec("INSERT INTO meters_readings VALUES ('{$idMeter}', '{$meterData}', '{$date}')");
            $error = $this->sql->errorInfo();
            return json_encode(['meterResult' => true]);
        } else {
            return json_encode(['meterResult' => false, 'message' => 'Указаный номер счётчика не верен']);
        }
    }
}
