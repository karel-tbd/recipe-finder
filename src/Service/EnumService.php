<?php

namespace App\Service;

use BadMethodCallException;
use InvalidArgumentException;
use Symfony\Contracts\Translation\TranslatableInterface;

readonly class EnumService
{
    public function __construct()
    {
    }

    public function createEnumProxy(string $fullClassName): object
    {
        return new readonly class($fullClassName) {
            public function __construct(private string $enum)
            {
                if (!enum_exists($this->enum)) {
                    throw new InvalidArgumentException("$this->enum is not an Enum type and cannot be used in this function");
                }
            }

            public function __call(string $name, array $arguments)
            {
                $fullClassName = sprintf('%s::%s', $this->enum, $name);

                if (defined($fullClassName)) {
                    return constant($fullClassName);
                }

                if (method_exists($this->enum, $name)) {
                    return $this->enum::$name(...$arguments);
                }

                throw new BadMethodCallException("Neither \"$fullClassName\" nor \"$fullClassName::$name()\" exist in this runtime.");
            }
        };
    }

    public function getEnumValues(string $fullClassName, array $exclude = [], bool $translated = true): array
    {
        $enum = $this->createEnumProxy($fullClassName);
        $translate = $translated && is_subclass_of($fullClassName, TranslatableInterface::class);

        $values = [];
        foreach ($enum->cases() as $case) {
            if (in_array($case, $exclude)) continue;

            $values[] = $translate ? $case->trans($this->translator) : $case->value;
        }

        return $values;
    }
}
