<?php

namespace App\Enum\Trait;

use Symfony\Contracts\Translation\TranslatorInterface;

trait TranslatableTrait
{
    public function trans(TranslatorInterface $translator, ?string $locale = null): string
    {
        $label = ucfirst(str_replace('_', '-', $this->name));
        $value = ucfirst(str_replace('_', ' ', $this->value));
        return $translator->trans('label.' . $label, ['default' => $value], locale: $locale);
    }
}