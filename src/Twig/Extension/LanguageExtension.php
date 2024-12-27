<?php

namespace App\Twig\Extension;

use App\Service\LanguageService;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class LanguageExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('supportedLanguages', [$this, 'getSupportedLanguages']),
        ];
    }

    public function getSupportedLanguages(): array
    {
        return LanguageService::getSupportedLanguages();
    }
}
