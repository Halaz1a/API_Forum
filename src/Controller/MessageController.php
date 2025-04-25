<?php

namespace App\Controller;

use App\Entity\Message;
use App\Entity\Forum;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Serializer\SerializerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator as DoctrinePaginator;


class MessageController extends AbstractController
{
    public function getRootMessagesByForum(
        Request $request,
        EntityManagerInterface $entityManager,
        SerializerInterface $serializer,
        string $forumId
    ): JsonResponse {
        $forum = $entityManager->getRepository(Forum::class)->find((int) $forumId);
    
        if (!$forum) {
            throw new NotFoundHttpException('Forum not found');
        }
    
        $page = max((int) $request->query->get('page', 1), 1);
        $itemsPerPage = (int) $request->query->get('itemsPerPage', 10);
        $firstResult = ($page - 1) * $itemsPerPage;
    
        $qb = $entityManager->getRepository(Message::class)
            ->createQueryBuilder('m')
            ->join('m.forum', 'f')
            ->where('m.parent IS NULL')
            ->andWhere('f.id = :forumId')
            ->setParameter('forumId', $forumId)
            ->setFirstResult($firstResult)
            ->setMaxResults($itemsPerPage);
    
        $paginator = new DoctrinePaginator($qb);
        $totalItems = count($paginator);
    
        $messages = iterator_to_array($paginator);
        $jsonMessages = json_decode($serializer->serialize($messages, 'json', [
            'groups' => ['message:list'],
        ]), true);
    
        $uri = $request->getSchemeAndHttpHost() . $request->getPathInfo();
    
        return new JsonResponse([
            '@context' => '/forum/api/contexts/Message',
            '@id' => $uri,
            '@type' => 'hydra:Collection',
            'hydra:member' => $jsonMessages,
            'hydra:totalItems' => $totalItems,
            'hydra:view' => [
                '@id' => $uri . '?page=' . $page,
                '@type' => 'hydra:PartialCollectionView',
                'hydra:first' => $uri . '?page=1',
                'hydra:last' => $uri . '?page=' . ceil($totalItems / $itemsPerPage),
                'hydra:next' => $page * $itemsPerPage < $totalItems ? $uri . '?page=' . ($page + 1) : null,
                'hydra:previous' => $page > 1 ? $uri . '?page=' . ($page - 1) : null,
            ],
        ]);
    }

    public function getResponsesToMessage(Request $request, EntityManagerInterface $entityManager, string $messageId)
    {
        $messageIdInt = (int) $messageId;

        $message = $entityManager->getRepository(Message::class)->find($messageIdInt);

        if (!$message) {
            return new JsonResponse(['error' => 'Message not found'], 400);
        }

        $repository = $entityManager->getRepository(Message::class);
        $messages = $repository->findBy([
            'parent' => $message
        ]);

        return $this->json($messages);
    }
}

?>