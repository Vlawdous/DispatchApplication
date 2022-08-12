<?php

namespace Application\Views;

use Application\Core\View;

class PayView extends View
{
    use DecoratorTrait;
    
    public function render(): string
    {
        return str_replace(
            ['<!-- <<titlePlace>> -->', '<!-- <<headPlace>> -->', '<!-- <<menuItemsPlace>> -->', '<!-- <<bodyPlace>> -->' , '<!-- <<footerItemsPlace>> -->'],
            ['Оплата', '<script src="/js/pay.js"></script>', $this->decorator->getMenuItems(), $this->decorator->getBody(), $this->decorator->getFooterItems()],
            $this->template
        );
    }
}
