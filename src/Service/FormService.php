<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;

readonly class FormService
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    /**
     * @throws ORMException
     */
    public function getEntityReferences(string $namespace, array $data, bool $choiceList = false): array
    {
        $references = [];
        foreach ($data as $value) {
            if ($choiceList) {
                $reference = $this->entityManager->getReference($namespace, $value);
                $references[(string)$reference] = $reference->getId();
            } else {
                $references[] = $this->entityManager->getReference($namespace, $value);
            }
        }

        if ($choiceList) {
            ksort($references);
        }

        return $references;
    }

    /**
     * @throws ORMException
     */
    public function getEntityReference(string $namespace, string $data): mixed
    {
        return $this->entityManager->getReference($namespace, $data);
    }

    /**
     * @throws ORMException
     */
    public function getEnumReferences(string $namespace, array $data): array
    {
        $references = [];
        foreach ($data as $value) {
            $enum = (new EnumService)->createEnumProxy($namespace);
            $references[] = $enum->tryFrom($value);
        }

        return $references;
    }

    /**
     * @throws ORMException
     */
    public function getEnumReference(string $namespace, string $data): mixed
    {
        $enum = (new EnumService)->createEnumProxy($namespace);
        return $enum->tryFrom($data);
    }
}
