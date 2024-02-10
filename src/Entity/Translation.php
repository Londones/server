<?php

namespace App\Entity;

use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\ApiResource;
use App\State\TranslationStateProvider;

#[Get(
    uriTemplate: '/translations/{language}',
    provider: TranslationStateProvider::class
)]
class Translation
{
}
