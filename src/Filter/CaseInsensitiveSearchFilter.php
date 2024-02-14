
<?php 
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\AbstractFilter;
use Doctrine\ORM\QueryBuilder;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;

class CaseInsensitiveSearchFilter extends AbstractFilter
{
    protected function filterProperty(string $property, $value, QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, string $operationName = null)
    {
        if ($value === null || !(is_array($value) || is_string($value))) {
            return;
        }

        $alias = $queryBuilder->getRootAliases()[0];
        $parameterName = $queryNameGenerator->generateParameterName($property);
        $queryBuilder
            ->andWhere(sprintf('LOWER(%s.%s) LIKE LOWER(:%s)', $alias, $property, $parameterName))
            ->setParameter($parameterName, '%' . addcslashes($value, '%_') . '%');
    }

    public function getDescription(string $resourceClass): array
    {
        return [
            'prestation.titre' => [
                'property' => 'prestation.titre',
                'type' => 'string',
                'required' => false,
                'swagger' => [
                    'description' => 'Filter by case-insensitive partial match on the prestation titre',
                    'name' => 'prestation.titre', 
                    'type' => 'string',
                ],
            ],
        ];
    }
}
