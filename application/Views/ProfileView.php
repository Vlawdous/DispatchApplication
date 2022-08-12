<?php

namespace Application\Views;

use Application\Core\View;

class ProfileView extends View
{
    use DecoratorTrait;
    
    public function render(): string
    {
        return str_replace(
            ['<!-- <<titlePlace>> -->', '<!-- <<headPlace>> -->','<!-- <<menuItemsPlace>> -->', '<!-- <<bodyPlace>> -->' , '<!-- <<footerItemsPlace>> -->'],
            ['Мой профиль', '<script src="/js/profile.js"></script>', $this->decorator->getMenuItems(), $this->decorator->getBody(), $this->decorator->getFooterItems()],
            $this->template
        );
    }
}
