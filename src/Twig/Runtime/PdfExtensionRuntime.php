<?php

namespace App\Twig\Runtime;

use Twig\Extension\RuntimeExtensionInterface;

class PdfExtensionRuntime implements RuntimeExtensionInterface
{
    public function encodeImage(string $path): string
    {
        return base64_encode(file_get_contents($path));
    }
}
