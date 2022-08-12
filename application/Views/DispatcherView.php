<?php

namespace Application\Views;

use Application\Core\View;

class DispatcherView extends View
{
    use DecoratorTrait;

    public function render(): string
    {
        return str_replace(
            ['<!-- <<titlePlace>> -->', '<!-- <<headPlace>> -->', '<!-- <<menuItemsPlace>> -->', '<!-- <<bodyPlace>> -->' , '<!-- <<footerItemsPlace>> -->'],
            ['Диспетчерское приложение', '<script src="/js/dispatch.js"></script>', $this->decorator->getMenuItems(), $this->decorator->getBody(), $this->decorator->getFooterItems()],
            $this->template
        );
    }
    public function renderTable(array $tableInfo): string
    {
        return $this->decorator->makeTable($tableInfo, false);
    }
}
