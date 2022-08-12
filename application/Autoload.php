<?php

namespace Application;

class Autoload
{
    public static function load(string $name): void
    {
        [$folder, $document] = array_slice(explode('\\', $name), 1, 2);
        require_once($_SERVER['DOCUMENT_ROOT'] . "/application/{$folder}/{$document}.php");
    }
}
