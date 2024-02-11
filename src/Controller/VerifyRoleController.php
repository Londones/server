<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

class VerifyRoleController extends AbstractController
{
    #[Route(path: '/verify-role', name: 'verify_role', methods: ['POST'])]
    public function verifyUserRole(Request $request, EntityManagerInterface $entityManager)
    {
        $email = $request->getPayload()->get('email');

        // Get the user details.
        $user = $this->getUser();

        if ($user) {

            $roles = $user->getRoles();
            if (!in_array('ROLE_ADMIN', $roles) && !in_array('ROLE_PRESTATAIRE', $roles)) {
                return new JsonResponse(['code' => 403, 'message' => 'User does not have the required role.'], 403);
            }

            $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $email]);

            // Return the user details.
            return new JsonResponse([
                'id' => $user->getId(),
                'nom' => $user->getNom(),
                'prenom' => $user->getPrenom(),
                'roles' => $roles,
            ]);
        }

        // Return an error message.
        return new JsonResponse(['code' => 404, 'message' => 'User not found.'], 404);
    }
}
