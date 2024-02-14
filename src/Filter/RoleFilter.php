<?php

namespace App\Filter;

use ApiPlatform\Doctrine\Orm\Filter\AbstractFilter;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use Doctrine\ORM\QueryBuilder;
use ApiPlatform\Metadata\Operation;

class RoleFilter extends AbstractFilter
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
        if ($property !== 'roles') {
            return;
        }

        $alias = $queryBuilder->getRootAliases()[0];
        $queryBuilder->andWhere("JSON_GET_TEXT($alias.roles,0) = :role ")
                 ->setParameter('role', $value);
    }

    public function getDescription(string $resourceClass): array
    {
        return [
            'roles' => [
                'property' => 'roles',
                'type' => 'string',
                'required' => false,
                'swagger' => ['description' => 'Filter by roles'],
            ],
        ];
    }
}