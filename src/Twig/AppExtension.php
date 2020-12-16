<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('icon', [$this, 'faIcon'], ['is_safe' => ['html'] ]),
        ];
    }

    public function faIcon(string $name): string
    {
        return "<i class=\"fa fa-${name}\"></i>";
    }
}
