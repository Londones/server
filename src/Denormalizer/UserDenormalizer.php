<?php

declare(strict_types=1);

namespace App\Denormalizer;

use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;


class UserDenormalizer implements DenormalizerInterface, DenormalizerAwareInterface
{
    use DenormalizerAwareTrait;

    public function __construct(
        protected Security $security,
        protected UserPasswordHasherInterface $hasher,
    )
    {
    }

    public function supportsDenormalization(mixed $data, string $type, string $format = null, array $context = []): bool
    {
        return $type === User::class && !isset($context[__CLASS__]);
    }

    public function denormalize(mixed $data, string $type, string $format = null, array $context = [])
    {
        $context[__CLASS__] = true;

        $user = $this->denormalizer->denormalize($data, $type, $format, $context);
        assert($user instanceof User);

        //hash mot de passe
        $plainPassword = $user->getPlainPassword();

        if (empty($plainPassword)) {
            return $user;
        }

        $hashedPassword = $this->hasher->hashPassword($user, $plainPassword);
        $user->setPassword($hashedPassword);

        //Ajouter le role ['ROLE_USER'] par défaut
        $user->setRoles(['ROLE_USER']);


        $user->eraseCredentials();
        return $user;
    }
}