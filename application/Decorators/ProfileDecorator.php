<?php

namespace Application\Decorators;

use Application\Core\Decorator;

class ProfileDecorator extends Decorator
{
    use HeaderTrait;
    use FooterTrait;

    public function getBody(): string
    {
        return '
            <div class = "profile scrolling_window">
            <form action="/Profile" method="post" id="clearSession">
                <input type="submit" name="clear" value="Выйти из аккаунта">
            </form>
            <h2>Изменить данные аккаунта</h2>
            <h4>Логин</h4>
            <form action="/Profile" method="post" id="change_login">
                <span>Старый логин</span><input type="text" name="old_login" required minlength="5" maxlength="256" autocomplete="off">
                <span>Новый логин</span><input type="text" name="new_login" required minlength="5" maxlength="256" autocomplete="off">
                <span>Повторите новый логин</span><input type="text" name="new_login_repeat" required minlength="5" maxlength="256" autocomplete="off">
                <span class="error_message"></span>
                <input type="submit" value="Изменить">
            </form>
            <h4>Пароль</h4>
            <form action="/Profile" method="post" id="change_password">
                <span>Старый пароль</span><input type="password" name="old_password" required minlength="5" maxlength="256" autocomplete="off">
                <span>Новый пароль</span><input type="text" name="new_password" required minlength="5" maxlength="256" autocomplete="off">
                <span>Повторите новый пароль</span><input type="text" name="new_password_repeat" required minlength="5" maxlength="256" autocomplete="off">
                <span class="error_message"></span>
                <input type="submit" value="Изменить">
            </form>
            <h4>Почта</h4>
            <form action="/Profile" method="post" id="change_email">
                <span>Новая почта</span><input type="text" name="new_email" required>   
                <span class="error_message"></span>
                <input type="submit" value="Изменить">
            </form>
            <h2>Отправить пожелания</h2>
            <p class="error_message">Пожалуйста, оставьте в данном пожелании информацию о себе, а также о том, как с вами можно связаться. Не забудьте указать дом и подъезд с квартирой, если это нужно. В противном случае есть вероятность того, что заявка будет проигнорирована</p>
            <form action="/Profile" method="post" id="wish">
                <textarea name="wish" required maxlength="10000"></textarea>
                <span class="error_message"></span>
                <input type="submit" value="Отправить">
            </form>
        </div>' . str_replace(
            '<!-- <<modalPlace>> -->',
            '<div class="modal_window">
                <form id="email_confirm" action="/Profile" class="email_confirm" method="post">
                    <div class="modal_header">
                        <h2>Проверочный код</h2>
                        <p>На введённую почту для подтверждения был выслан код. Пожалуйста, введите его.</p>
                    </div>
                    <div class="modal_input">
                        <input type="text" name="email_code" placeholder="Проверочный код" require><div class = "error_message error_confirm"></div>
                        <input type="submit" value="Отправить">
                    </div>
                </form>
            </div>',
            file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/templates/ModalWindowTemplate.html')
        );
    }
}
