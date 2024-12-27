<?php

namespace App\Twig\Extension;

use App\Twig\Runtime\PdfExtensionRuntime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class PdfExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('encodeImage', [PdfExtensionRuntime::class, 'encodeImage']),
        ];
    }
}
