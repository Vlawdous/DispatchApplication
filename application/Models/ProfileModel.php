<?php

namespace Application\Models;

use Application\Core\Model;
use Application\Core\RandomGenerator;

class ProfileModel extends Model
{
    use MailerTrait;
    use SqlTrait;
    use RedisTrait;

    private function accountExist(string|int $idUser, array $comparedOptions): bool
    {
        $condition = '';
        foreach ($comparedOptions as $option => $searchedValue) {
            $condition .= "AND $option = '$searchedValue'";
        }
        $resultQuery = $this->sql->query("SELECT EXISTS(SELECT * FROM accounts WHERE id_account = $idUser $condition)")->fetch();
        if ($resultQuery['exists']) {
            return true;
        } else {
            return false;
        }
    }
    private function changeColumns(string|int $idUser, array $comparedOptions): void
    {
        $condition = '';
        $needComma = false;
        foreach ($comparedOptions as $option => $searchedValue) {
            $condition .= ($needComma) ? ', ' : '';
            $condition .= "$option = '$searchedValue'";
        }
        $this->sql->exec("UPDATE accounts SET $condition WHERE id_account = $idUser");
    }

    public function changeLogin(array $logins, string|int $idUser): string
    {
        if ($logins['new_login'] === $logins['new_login_repeat']) {
            if ($this->accountExist($idUser, ['login' => $logins['old_login']])) {
                if ($this->accountExist($idUser, ['login' => $logins['new_login']])) {
                    return json_encode(['changeLoginResult' => false, 'message' => 'Данный логин уже существует.']);
                }
                $this->changeColumns($idUser, ['login' => $logins['new_login']]);
                return json_encode(['changeLoginResult' => true]);
            } else {
                return json_encode(['changeLoginResult' => false, 'message' => 'Неверный старый логин.']);
            }
        } else {
            return json_encode(['changeLoginResult' => false, 'message' => 'Неверное повторение нового логина.']);
        }
    }
    public function changePassword(array $passwords, string|int $idUser): string
    {
        if ($passwords['new_password'] === $passwords['new_password_repeat']) {
            if ($this->accountExist($idUser, ['password' => $passwords['old_password']])) {
                $this->changeColumns($idUser, ['password' => $passwords['new_password']]);
                return json_encode(['changePasswordResult' => true]);
            } else {
                return json_encode(['changePasswordResult' => false, 'message' => 'Неверный старый пароль.']);
            }
        } else {
            return json_encode(['changePasswordResult' => false, 'message' => 'Неверное повторение нового логина.']);
        }
    }
    public function changeEmail(array $emailFromPost): void
    {
        $email = $emailFromPost['new_email'];
        $this->setRedisConnection();
        do {
            $email_code = RandomGenerator::generateRandomCode(10);
        } while ($this->redis->exists("email:$email_code"));
        $this->redis->set("email:$email_code", $email, ['nx', 'ex' => 300]);
        $this->sendTo(
            $email,
            'ООО "Счастливый дом": проверочный код для смены почты.',
            "<h2>Проверочный код для смены почты:</h2>
            <p><b>{$email_code}</b></p>
            <p>Если вдруг вы не имеете отношения к нашей управляющей компании, то проигнорируйте это сообщение.</p>"
        );
    }
    public function confirmEmail(array $codeFromPost, string|int $idUser): string
    {
        $email_code = $codeFromPost['email_code'];
        $this->setRedisConnection();
        if ($this->redis->exists("email:$email_code")) {
            $new_email = $this->redis->get("email:$email_code");
            $this->changeColumns($idUser, ['email' => $new_email]);
            return json_encode(['confirmEmail' => true]);
        } else {
            return json_encode(['confirmEmail' => false]);
        }
    }
    public function addWish(array $wishFromPost, string|int $idUser): void
    {
        $currentDate = date('Y-m-d');
        $wish = $wishFromPost['wish'];
        $this->sql->exec("INSERT INTO messages_from_users VALUES (DEFAULT, $idUser, '$currentDate', '$wish')");
        $error = $this->sql->errorInfo();
    }
}
