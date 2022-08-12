<?php

namespace Application\Models;

require_once($_SERVER['DOCUMENT_ROOT'] . '/application/Mailer/PHPMailer.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/application/Mailer/Exception.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/application/Mailer/SMTP.php');

use PHPMailer\PHPMailer\PHPMailer;

trait MailerTrait
{
    private function sendTo(string $emailReceiver, string $title, string $body): void
    {
        //Настройки отправителя
        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->CharSet = "UTF-8";
        $mail->SMTPAuth   = true;
        $emailInfo = parse_ini_file($_SERVER['DOCUMENT_ROOT'] . '/EmailInfo.ini', true);
        $mail->Host       = 'smtp.yandex.ru'; // SMTP сервера вашей почты
        $mail->Username   = $emailInfo['email']['login']; // Логин на почте
        $mail->Password   = $emailInfo['email']['password']; // Пароль на почте
        $mail->SMTPSecure = 'ssl';
        $mail->Port       = 465;
        $mail->setFrom('HappyHouseNSK@yandex.ru', 'ООО "Счастливый дом"'); // Адрес самой почты и имя отправителя

        //Настройки получателя
        $mail->addAddress($emailReceiver);

        // Отправка сообщения
        $mail->isHTML(true);
        $mail->Subject = $title;
        $mail->Body = $body;

        if (!$mail->send()) {
            echo $mail->ErrorInfo;
            throw new \Exception('Ошибка отправки письма.');
        }
    }
}
