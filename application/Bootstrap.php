<?php

namespace Application;

require_once('./application/Autoload.php');
spl_autoload_register(__NAMESPACE__ . '\\Autoload::load');
use Application\Core;
use Application\Controller;
use Application\Models;
use Application\Decorators;
use Application\View;
use Application\Core\Router;
try {
    $controller = Router::parse($_SERVER['REQUEST_URI']);
    $controller->action();
} catch (\Throwable $e) {
    $fp = fopen($_SERVER['DOCUMENT_ROOT'] . '/logs/logServerErrors.txt', "a+");
    fwrite(
        $fp,
        'Message: ' . $e->getMessage() . "\n" .
        'Code: ' . $e->getCode() . "\n" .
        'File: ' . $e->getFile() . "\n" .
        'Line: ' . $e->getLine() . "\n" .
        'Trace: ' . $e->getTraceAsString() . "\n\n"
    );
    fclose($fp);
    Router::error();
}
