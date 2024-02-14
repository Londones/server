<?php

namespace App\Filter;

use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Doctrine\Orm\Filter\AbstractFilter;
use ApiPlatform\Metadata\Operation;
use Doctrine\ORM\QueryBuilder;

class GeoLocationFilter extends AbstractFilter
{
    protected function filterProperty(string $property, $value, QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, Operation $operation = null, array $context = []): void
    {
        if ($property !== 'distance') {
            return;
        }

        // Ensure the value contains the latitude, longitude, and radius, e.g., "48.8566,2.3522,10"
        [$lat, $long, $radius] = explode(',', $value);

        $rootAlias = $queryBuilder->getRootAliases()[0];

        // Use your custom GEO function in the query
        // The GEO function is expected to return the distance, so you can compare it with the radius
        $queryBuilder->andWhere(sprintf('GEO(%s.latitude, %s.longitude, :lat, :long) < :radius', $rootAlias, $rootAlias))
            ->setParameter('lat', $lat)
            ->setParameter('long', $long)
            ->setParameter('radius', $radius);
    }


    public function getDescription(string $resourceClass): array
    {
        return [
            // Describes the 'distance' filter parameter, which now expects a combined string of lat,long,radius
            'distance' => [
                'property' => 'distance',
                'type' => 'string', // Type is now string to indicate the expected format of "lat,long,radius"
                'required' => false,
                'swagger' => [
                    'description' => 'Combined latitude, longitude, and radius (in kilometers) for distance calculation, separated by commas. For example: "48.8566,2.3522,10" where the radius "10" is the distance in kilometers.',
                    'name' => 'distance',
                    'type' => 'string',
                ],
            ],
        ];
    }
}
