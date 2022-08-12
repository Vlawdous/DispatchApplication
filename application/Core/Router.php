<?php

namespace Application\Core;

class Router
{
    public static function parse(string $url): Controller
    {
        $url = mb_strcut($url, 1);
        $parseInfo = explode('/', $url, 2);
        $parseInfo[0] = str_replace('/', '', $parseInfo[0]);
        if (isset($parseInfo[1])) {
            $parseInfo[1] = explode('/', $parseInfo[1]);
        }
        if (empty($parseInfo[0])) {
            $parseInfo[0] = 'Default';
        }
        $class = $parseInfo[0];
        if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/application/Controllers/{$class}Controller.php")) {
            return Controller::createController($parseInfo);
        } else {
            self::notFound();
        }
    }
    public static function notFound(): void
    {
        http_response_code(404);
        die();
    }
    public static function error(): void
    {
        http_response_code(500);
        die();
    }
    public static function redirect(string $request): void
    {
        header('Location: http://' . $_SERVER['HTTP_HOST'] . $request);
    }
}
