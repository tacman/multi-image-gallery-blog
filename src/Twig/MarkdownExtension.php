<?php

namespace App\Twig;

use Parsedown;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class MarkdownExtension extends AbstractExtension
{

    public function getFilters(): array
    {
        return [
            new TwigFilter('markdown', [$this, 'renderMarkdownToHtml']),
        ];
    }

    public function renderMarkdownToHtml($markdown): string
    {
        $parsedown = new Parsedown();

        return $parsedown->text($markdown);
    }

}
