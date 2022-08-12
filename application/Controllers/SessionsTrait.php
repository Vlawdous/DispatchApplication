<?php

namespace Application\Controllers;

trait SessionsTrait
{
    public function isAuthorizided(): bool
    {
        return (isset($_SESSION['idUser']) && isset($_SESSION['role']));
    }
    public function isWorker(): bool
    {
        return ($this->isAuthorizided() && in_array('worker', $_SESSION['role']));
    }
    public function isInhabitant(): bool
    {
        return ($this->isAuthorizided() && in_array('inhabitant', $_SESSION['role']));
    }
    public function isDispatcher(): bool
    {
        return ($this->isAuthorizided() && in_array('dispatcher', $_SESSION['role']));
    }
    public function getUserId(): string|int
    {
        return $_SESSION['idUser'];
    }
    private function setSession(string $nameSession, mixed $value): void
    {
        $_SESSION[$nameSession] = $value;
    }
}
