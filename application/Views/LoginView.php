<?php

namespace Application\Views;

use Application\Core\View;

class LoginView extends View
{
    use DecoratorTrait;

    public function render(): string
    {
        return str_replace(
            ['<!-- <<titlePlace>> -->', '<!-- <<headPlace>> -->', '<!-- <<menuItemsPlace>> -->', '<!-- <<bodyPlace>> -->' , '<!-- <<footerItemsPlace>> -->'],
            ['Авторизация', '<script src="/js/auth.js"></script>', $this->decorator->getMenuItems(), $this->decorator->getBody(), $this->decorator->getFooterItems()],
            $this->template
        );
    }
}
