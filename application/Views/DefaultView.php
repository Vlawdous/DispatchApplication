<?php

namespace Application\Views;

use Application\Core\View;

class DefaultView extends View
{
    use DecoratorTrait;

    public function render(): string
    {
        return str_replace(
            ['<!-- <<titlePlace>> -->', '<!-- <<profilePlace>> -->', '<!-- <<menuItemsPlace>> -->', '<!-- <<footerItemsPlace>> -->'],
            ['Главная страница', $this->decorator->getProfile(), $this->decorator->getMenuItems(), $this->decorator->getFooterItems()],
            $this->template
        );
    }
}
