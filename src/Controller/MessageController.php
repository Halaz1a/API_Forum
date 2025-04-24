<?php

namespace App\Controller;

use App\Entity\Message;
use App\Entity\Forum;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class MessageController extends AbstractController
{
    public function getRootMessagesByForum(Request $request, EntityManagerInterface $entityManager, string $forumId)
    {

        $forumIdInt = (int) $forumId;
        
        $forum = $entityManager->getRepository(Forum::class)->find($forumIdInt);
        
        if (!$forum) {
            return new JsonResponse(['error' => 'Forum not found'], 404);
        }
        
        $repository = $entityManager->getRepository(Message::class);
        $messages = $repository->findBy([
            'parent' => null,
            'forum' => $forum 
        ]);
        
        return $this->json($messages);
    }
}

?>