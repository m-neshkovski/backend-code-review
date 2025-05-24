<?php
declare(strict_types=1);

namespace App\Controller;

use App\Enum\MessageStatus;
use App\Message\SendMessage;
use App\Repository\MessageRepository;
use Controller\MessageControllerTest;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * @see MessageControllerTest
 * TODO: review both methods and also the `openapi.yaml` specification
 *       Add Comments for your Code-Review, so that the developer can understand why changes are needed.
 */
class MessageController extends AbstractController
{
    /**
     * TODO: cover this method with tests, and refactor the code (including other files that need to be refactored)
     */
    #[Route('/messages')]
    public function list(Request $request, MessageRepository $messagesRepository, NormalizerInterface $normalize): Response
    {
        // The repository should be responsible only for CRUD operations
        $status = (string) $request->query->get('status');

        /**
         * I know that the valid status query parameter can be null
         * and any value defined in enum MessageStatus.
         * So if this is not true, I don't want to send the query to the database
         */
        $messages = [];

        if(MessageStatus::isValidForFilterByStatus($status)) {
            // Names are changed for clarity
            $messages = $messagesRepository->filterByStatus($status);

            $messages = $normalize->normalize($messages, 'array', [
                'groups' => ['message_list']
            ]);
        }
        
        return new Response(json_encode([
            'messages' => $messages,
        ], JSON_THROW_ON_ERROR), headers: ['Content-Type' => 'application/json']);
    }

    #[Route('/messages/send', methods: ['GET'])]
    public function send(Request $request, MessageBusInterface $bus): Response
    {
        $text = (string) $request->query->get('text');
        
        if (!$text) {
            return new Response('Text is required', 400);
        }

        $bus->dispatch(new SendMessage($text));
        
        return new Response('Successfully sent', 204);
    }
}