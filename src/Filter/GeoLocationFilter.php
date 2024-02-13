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
        if ($property !== 'distance' || !is_array($value) || !isset($value['lat'], $value['long'], $value['distance'])) {
            return;
        }

        $lat = (float) $value['lat'];
        $long = (float) $value['long'];
        $distance = (float) $value['distance']; // Distance in kilometers

        $alias = $queryBuilder->getRootAliases()[0];

        // Using Haversine formula for distance calculation
        // This SQL snippet calculates the distance in kilometers between two points
        $haversine = "(6371 * acos(cos(radians(:lat)) * cos(radians($alias.latitude)) * cos(radians($alias.longitude) - radians(:long)) + sin(radians(:lat)) * sin(radians($alias.latitude))))";

        $queryBuilder
            ->andWhere(sprintf('%s < :distance', $haversine))
            ->setParameter('lat', $lat)
            ->setParameter('long', $long)
            ->setParameter('distance', $distance);
    }

    public function getDescription(string $resourceClass): array
    {
        return [
            'lat' => [
                'property' => 'lat',
                'type' => 'float',
                'required' => false,
                'swagger' => [
                    'description' => 'Latitude for distance calculation.',
                    'name' => 'lat',
                    'type' => 'float',
                ],
            ],
            'long' => [
                'property' => 'long',
                'type' => 'float',
                'required' => false,
                'swagger' => [
                    'description' => 'Longitude for distance calculation.',
                    'name' => 'long',
                    'type' => 'float',
                ],
            ],
            'distance' => [
                'property' => 'distance',
                'type' => 'float',
                'required' => false,
                'swagger' => [
                    'description' => 'Distance in kilometers within which to search.',
                    'name' => 'distance',
                    'type' => 'float',
                ],
            ],
        ];
    }
    
}
