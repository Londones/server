<?php

namespace App\State;

use ApiPlatform\Metadata\CollectionOperationInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;

class TranslationStateProvider implements ProviderInterface
{
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $language = $uriVariables['language'] ?? 'fr';

        // Retrieve the state from a JSON file
        $state = json_decode(file_get_contents("../translations/{$language}.json"), true);

        // Return the state for a GET operation
        if ($operation instanceof CollectionOperationInterface) {
            return null;
        }

        return $state;
    }
}
