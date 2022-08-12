<?php

namespace Application\Views;

use Application\Core\View;

class WorkerView extends View
{
    use DecoratorTrait;
    
    public function render(): string
    {
        return str_replace(
            ['<!-- <<titlePlace>> -->', '<!-- <<menuItemsPlace>> -->', '<!-- <<bodyPlace>> -->' , '<!-- <<footerItemsPlace>> -->'],
            ['Объявления', $this->decorator->getMenuItems(), $this->decorator->getBody(), $this->decorator->getFooterItems()],
            $this->template
        );
    }
}
