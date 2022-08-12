<?php

namespace Application\Controllers;

use Application\Core\Controller;

class DefaultController extends Controller
{
    use SessionsTrait;
    use WithoutActionTrait;
    use ViewTrait;

    public function action(): void
    {
        echo $this->setView('Default')->setDecorator('Default', $this)->render();
    }
}
