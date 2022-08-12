<?php

error_reporting(0);

session_start();

date_default_timezone_set('Asia/Novosibirsk');
require_once($_SERVER['DOCUMENT_ROOT'] . '/application/Bootstrap.php');

session_write_close();
