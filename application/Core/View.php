<?php

namespace Application\Core;

abstract class View
{
    protected string $template;

    protected function __construct(string $templateName)
    {
        $this->template = str_replace(
            ['<!-- <<startPagePlace>> -->', '<!-- <<headerPlace>> -->', '<!-- <<footerPlace>> -->'],
            [file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/templates/HeadTemplate.html"), file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/templates/HeaderTemplate.html"), file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/templates/FooterTemplate.html")],
            file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/templates/{$templateName}.html")
        );
    }

    public static function createView(string $class, string $templateName): View
    {
        $className = "Application\Views\\{$class}View";
        return new $className($templateName);
    }
    abstract public function render(): string;
}
