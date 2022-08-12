<?php

namespace Application\Decorators;

use Application\Core\Decorator;

class LoginDecorator extends Decorator
{
    use HeaderTrait;
    use FooterTrait;

    public function getBody(): string
    {
        return str_replace(
            '<!-- <<modalPlace>> -->',
            '<div class="modal_window" style = "display: none">
                <form id="email_confirm" action="/Login" class="email_confirm" method="post">
                    <div class="modal_header">
                        <h2>Проверочный код</h2>
                        <p>На вашу почту (<span class = "email_info"></span>) для подтверждения был выслан код. Пожалуйста, введите его.</p>
                    </div>
                    <div class="modal_input">
                        <input type="text" name="email_code" placeholder="Проверочный код" require><div class = "error_message error_confirm"></div>
                        <input type="hidden" name="login">
                        <input type="hidden" name="password">
                        <input type="submit" value="Отправить">
                    </div>
                </form>
            </div>' .
            '<div class="modal_window" style = "display: none">
                <form id="forget_password" action="/Login" class="forget_password" method="post">
                    <div class="modal_header">
                        <h2>Получить данные</h2>
                        <p>Введите почту, на которую зарегестрирован аккаунт. На неё будет отправлено письмо с дальнейшими инструкциями.</p>
                    </div>
                    <div class="modal_input">
                        <input type="text" name="forgotten_account_email" placeholder="Ваша почта" require>
                        <input type="submit" value="Отправить">
                    </div>
                </form>
            </div>',
            str_replace(
                ['<!-- <<authPlace>> -->', '<!-- <<modalWindow>> -->'],
                ['Авторизация', file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/templates/ModalWindowTemplate.html') ],
                file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/templates/AuthTemplate.html')
            )
        );
    }
}
