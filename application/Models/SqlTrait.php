<?php

namespace Application\Models;

use Application\Core\Model;
use PDO;

define('LOGIN_MODE', 1);
define('DISPATCHER_MODE', 2);
define('WORKER_MODE', 3);
define('INHABITANT_MODE', 4);
define('ADMIN_MODE', 5);

trait SqlTrait
{
    private ?PDO $sql = null;

    public function setSqlConnection(int $connectMode): Model
    {
        $server = '';
        $username = '';
        $password = '';
        $psqlInfo = parse_ini_file($_SERVER['DOCUMENT_ROOT'] . '/PostgreSQLInfo.ini', true);
        if (!empty($psqlInfo)) {
            $server = $psqlInfo['server']['adress'];
            switch ($connectMode) {
                case (LOGIN_MODE):
                    $username = $psqlInfo['users']['login'];
                    $password = $psqlInfo['passwords']['login'];
                    break;
                case (DISPATCHER_MODE):
                    $username = $psqlInfo['users']['dispatcher'];
                    $password = $psqlInfo['passwords']['dispatcher'];
                    break;
                case (WORKER_MODE):
                    $username = $psqlInfo['users']['worker'];
                    $password = $psqlInfo['passwords']['worker'];
                    break;
                case (INHABITANT_MODE):
                    $username = $psqlInfo['users']['inhabitant'];
                    $password = $psqlInfo['passwords']['inhabitant'];
                    break;
                case (ADMIN_MODE):
                    $username = $psqlInfo['users']['admin'];
                    $password = $psqlInfo['passwords']['admin'];
                    break;
            }
        }
        $this->sql = new PDO("pgsql:host={$server};dbname=happyhouse", $username, $password, [PDO::ATTR_ERRMODE => PDO::ERRMODE_SILENT]);
        return $this;
    }
}
