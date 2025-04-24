namespace App\Controller;

use App\Entity\Message;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class MessageController extends AbstractController
{
    public function getRootMessagesByForum(Request $request, EntityManagerInterface $entityManager, int $forumId)
    {
        $repository = $entityManager->getRepository(Message::class);
        $messages = $repository->findBy([
            'parent' => null,
            'id_forum' => $forumId
        ]);
        
        return $messages;
    }
}