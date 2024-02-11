<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Psr\Log\LoggerInterface;

// Retrieve the user by their email.
class VerifyRoleController extends AbstractController
{
    #[Route(path: '/verify-role', name: 'verify_role', methods: ['POST'])]
    public function verifyUserRole(Request $request, EntityManagerInterface $entityManager)
    {
        $email = $request->getPayload()->get('email');

        // Retrieve the user by their email.
        $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $email]);

        if (!$user) {
            return new JsonResponse(['code' => 404, 'message' => 'User not found.'], 404);
        }

        // Check if the user has either "ROLE_ADMIN" or "ROLE_PRESTATAIRE".
        $roles = $user->getRoles();
        if (!in_array('ROLE_ADMIN', $roles) && !in_array('ROLE_PRESTATAIRE', $roles)) {
            return new JsonResponse(['code' => 403, 'message' => 'User does not have the required role.'], 403);
        }

        // Return the user details.
        return new JsonResponse([
            'id' => $user->getId(),
            'nom' => $user->getNom(),
            'prenom' => $user->getPrenom(),
            'roles' => $roles,
        ]);
    }
}
