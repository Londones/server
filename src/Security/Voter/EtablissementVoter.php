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
    public const ETAB_CREATE = 'ETAB_CREATE';

    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        $supportsAttribute = in_array($attribute, [self::ETAB_EDIT, self::ETAB_VIEW, self::ETAB_CREATE]);

        $supportsSubject = $subject instanceof Etablissement || $subject === Etablissement::class;

        return $supportsAttribute && $supportsSubject;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        
        if (!$user instanceof UserInterface) {
            return false;
        }
        
        switch ($attribute) {
            case self::ETAB_EDIT:
                return $this->canEdit($user, $subject);

            case self::ETAB_VIEW:
                return $this->canView($user, $subject);

            case self::ETAB_CREATE:
                return $this->canCreate($user);
        }

        return false;
    }

    private function canEdit(UserInterface $user, Etablissement $etablissement): bool
    {
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        return $user === $etablissement->getOwner();
    }

    private function canView(UserInterface $user, Etablissement $etablissement): bool
    {
        if ($this->security->isGranted('ROLE_USER')) {
            return true;
        }
        
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        if ($this->security->isGranted('ROLE_PRESTATAIRE')) {
            return true;
        }

        return $user === $etablissement->getOwner();
    }

    private function canCreate(UserInterface $user): bool
    {
        return $this->security->isGranted('ROLE_USER') || $this->security->isGranted('ROLE_PRESTATAIRE');
    }
}
