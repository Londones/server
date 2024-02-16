<?php

namespace App\Security\Voter;
use App\Entity\Etablissement;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Security;

class EtablissementVoter extends Voter
{
    public const ETAB_EDIT = 'ETAB_EDIT';
    public const ETAB_VIEW = 'ETAB_VIEW';

    private $security = null;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        // return in_array($attribute, [self::ETAB_EDIT, self::ETAB_VIEW])
        //     && $subject instanceof \App\Entity\Etablissement;

        $supportsAttribute = in_array($attribute, ['ETAB_EDIT', 'ETAB_VIEW']);
        $supportsSubject = $subject instanceof Etablissement;

        return $supportsAttribute && $supportsSubject;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case 'ETAB_EDIT':
                if ($this->security->isGranted('ROLE_ADMIN')) {
                    return true;
                }
                break;

            case 'ETAB_VIEW':
                if ($this->security->isGranted('ROLE_ADMIN') || $this->security->isGranted('ROLE_PRESTATAIRE')) {
                    return true;
                }
                break;
        }

        return false;
    }
}
