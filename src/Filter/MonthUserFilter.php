<?php

namespace App\Filter;

use ApiPlatform\Doctrine\Orm\Filter\AbstractFilter;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use Doctrine\ORM\QueryBuilder;
use ApiPlatform\Metadata\Operation;

class MonthUserFilter extends AbstractFilter
{
    protected function filterProperty(
        string $property,
        $value,
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        Operation $operation = null, array $context = []
    ): void
    {
        if ($property !== 'month') {
            return;
        }

        $alias = $queryBuilder->getRootAliases()[0];
        $queryBuilder->andWhere("MONTH($alias.createdAt) = :month")
                     ->setParameter('month', $value);
    }

    public function getDescription(string $resourceClass): array
    {
        return [
            'monthCreated' => [
                'property' => 'monthCreated',
                'type' => 'integer',
                'required' => false,
                'swagger' => ['description' => 'Filter by month created'],
            ],
        ];
    }
}