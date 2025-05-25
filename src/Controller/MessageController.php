<?php
declare(strict_types=1);

namespace App\Controller;

use App\Enum\MessageStatus;
use App\Message\SendMessage;
use App\Repository\MessageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class MessageController extends AbstractController
{
    // This is based on the openapi.yaml specification that
    // '/messages' path method is defined as GET.
    #[Route('/messages', methods: ['GET'])]
    public function list(Request $request, MessageRepository $messagesRepository, NormalizerInterface $normalize): Response
    {
        // The repository should be responsible only for CRUD operations
        $status = (string) $request->query->get('status');

        /**
         * This is based on the openapi.yaml specification for parameter status.
         * I know that the valid status query parameter can be null
         * and any value defined in enum MessageStatus.
         * So if this is not true, I don't want to send the query to the database
         * because this operation is expensive both on computing resources and
         * AWS RDS, for example, charges for data transfer
         * so avoid whenever possible!!!!
         */
        if(! MessageStatus::isValidForFilterByStatus($status)) {
            return new JsonResponse([
                'messages' => [],
            ]);
        }

        // Names are changed for clarity
        $messages = $messagesRepository->filterByStatus($status);

        $messages = $normalize->normalize($messages, 'array', [
            'groups' => ['message_list'],
        ]);
        
        return new JsonResponse([
            'messages' => $messages,
        ]);
    }

    #[Route('/messages/send', methods: ['GET'])]
    public function send(Request $request, MessageBusInterface $bus): Response
    {
        $text = (string) $request->query->get('text');
        
        if (empty($text)) {
            // This is based on the openapi.yaml specification that
            // query parameter 'text' is required.
            return new Response('Text is required', 400);
        }

        if (strlen(trim($text)) > 255) {
            // This is based on the openapi.yaml specification that
            // query parameter 'text' is of type 'string'.
            return new Response('Text is longer than 255 characters', 400);
        }

        $bus->dispatch(new SendMessage($text));
        
        return new Response('Successfully sent', 204);
    }
}