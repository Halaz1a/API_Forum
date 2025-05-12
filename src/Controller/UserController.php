<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;


class UserController extends AbstractController
{
    #[Route('/api/users/email/{email}', name: 'get_user_by_email', methods: ['GET'])]
    public function getUserByEmail(string $email, EntityManagerInterface $em): JsonResponse
    {
        $user = $em->getRepository(User::class)->findOneBy(['email' => $email]);

        if (!$user) {
            return new JsonResponse(['error' => 'User not found'], 404);
        }

        return $this->json($user, 200, [], ['groups' => ['user:item']]);
    }
}
