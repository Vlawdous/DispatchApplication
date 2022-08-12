<?php

namespace Application\Models;

use Application\Core\Model;
use Application\Core\RandomGenerator;
use Exception;

class LoginModel extends Model
{
    use MailerTrait;
    use SqlTrait;
    use RedisTrait;

    private function loginExist(string $login, string $password, bool $withEmail = true): array
    {
        $resultQuery = $this->getAccount($login, $password);
        if ($account = $resultQuery->fetch()) {
            if ($withEmail) {
                $result = ['accountExist' => (count($account) > 0), 'email' => $account['email']];
            } else {
                $result = ['accountExist' => (count($account) > 0)];
            }
        } else {
            $result = ['accountExist' => false];
        }
        return $result;
    }
    private function getAccount(string $login = null, string $password = null, string $email = null): mixed
    {
        $needAnd = false;
        if ($login !== null) {
            $login = "login = '{$login}'";
            $needAnd = true;
        }
        if ($password !== null) {
            $password = ($needAnd) ? "AND password = '{$password}'" : "password = '{$password}'";
            $needAnd = true;
        }
        if ($email !== null) {
            $email = ($needAnd) ? "AND email = '{$email}'" : "email = '{$email}'";
        }
        return $this->sql->query("SELECT * FROM accounts WHERE {$login} {$password} {$email}");
    }
    private function getRoles(string|int $id_account): mixed
    {
        return $this->sql->query("SELECT * FROM accounts_roles WHERE id_account = $id_account");
    }
    private function updateAccountEnterData(string $email): array
    {
        do {
            $newLogin = RandomGenerator::generateRandonString(10, 20);
            $newPassword = RandomGenerator::generateRandonString(10, 20);
        } while ($this->loginExist($newLogin, $newPassword, false)["accountExist"]);
        $this->sql->exec("UPDATE accounts SET login = '{$newLogin}', password = '{$newPassword}' WHERE email='{$email}'");
        return ['login' => $newLogin, 'password' => $newPassword];
    }

    public function getLoginResult(array $accountInfo): string
    {
        $login = $accountInfo['login'];
        $password = $accountInfo['password'];
        $resultLogin = $this->loginExist($login, $password);
        if ($resultLogin['accountExist']) {
            $this->setRedisConnection();
            $email = $resultLogin['email'];
            do {
                $email_code = RandomGenerator::generateRandomCode(10);
            } while ($this->redis->exists("login:$email_code"));
            $this->sendEmailCode($email, $email_code);
            $this->redis->set("login:$email_code", json_encode(['login' => $login, 'password' => $password]), ['nx', 'ex' => 300]);
            $email_domain = preg_split('/@/', $email)[1];
            return json_encode(['loginResult' => true, 'email' => ((mb_strcut($email, 0, 3)) . '***@' . $email_domain)]);
        } else {
            return json_encode(['loginResult' => false]);
        }
    }
    private function sendEmailCode(string $emailReceiver, string $code): void
    {
        $this->sendTo(
            $emailReceiver,
            'ООО "Счастливый дом": проверочный код для входа.',
            "<h2>Проверочный код для авторизации на сайте:</h2>
            <p><b>{$code}</b></p>
            <p>Если вдруг в данный момент вы не пытайтесь войти в свою учётную запись, то обязательно обратитесь в один из наших пунктов по телефону или лично.</p>"
        );
    }
    public function getConfirmResult(array $confirmInfo): bool
    {
        $login = $confirmInfo['login'];
        $password = $confirmInfo['password'];
        $email_code = $confirmInfo['email_code'];
        $this->setRedisConnection();
        if ($this->redis->exists("login:$email_code")) {
            $accountInfo = json_decode($this->redis->get("login:$email_code"), true);
            if (($accountInfo['login'] == $login) && ($accountInfo['password'] == $password)) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
    public function restoreAccount(array $emailFromPost): void
    {
        $emailAccount = $emailFromPost['forgotten_account_email'];
        $account = $this->getAccount(email: $emailAccount)->fetch();
        if ($account === false) {
            return;
        }
        $newEnterData = $this->updateAccountEnterData($emailAccount);
        $newLogin = $newEnterData['login'];
        $newPassword = $newEnterData['password'];
        $this->sendTo(
            $emailAccount,
            'ООО "Счастливый дом": временные данные для входа.',
            "<h2>Для вас были сгенерерованы временные данные для входа в аккаунт и дальнейшую смену данных:</h2>
            <p>Логин: {$newLogin}</p>
            <p>Пароль: {$newPassword}</p>
            <p>Если вдруг в данный момент вы не пытайтесь войти в аккаунт, то обязательно обратитесь в один из наших пунктов по телефону или лично.</p>"
        );
    }
    public function getAccountID(array $accountInfo): int
    {
        $login = $accountInfo['login'];
        $password = $accountInfo['password'];
        $resultQuery = $this->getAccount($login, $password);
        if ($row = $resultQuery->fetch()) {
            return $row['id_account'];
        } else {
            throw new Exception('Несанкционированная выдача сессии.');
        }
    }
    public function getAccountRoles(string|int $id_account): array
    {
        $resultQuery = $this->getRoles($id_account);
        $result = [];
        $this->sql->errorInfo();
        while ($row = $resultQuery->fetch()) {
            $result[] = $row['role'];
        }
        return $result;
    }
}
