<?php

declare(strict_types=1);

namespace App\Controller;

use App\Message\SendMessage;
use App\Repository\MessageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class MessageController extends AbstractController
{
    /**
     * This is based on the openapi.yaml specification that
     * '/messages' path method is defined as GET.
     *
     * @throws ExceptionInterface
     */
    #[Route('/messages', methods: ['GET'])]
    public function list(Request $request, MessageRepository $messagesRepository, NormalizerInterface $normalize): Response
    {
        // The repository should be responsible only for CRUD operations
        // We are not validating the status since it can be anything
        $status = (string) $request->query->get('status');

        // Names are changed for clarity
        $messages = $messagesRepository->filterByStatus($status);

        $messages = $normalize->normalize($messages, 'array', [
            'groups' => ['message_list'],
        ]);

        return new JsonResponse([
            'messages' => $messages,
        ]);
    }

    /**
     *  This is based on the openapi.yaml specification.
     *  The path that sends a message as a text query parameter.
     */
    #[Route('/messages/send', methods: ['GET'])]
    public function send(Request $request, MessageBusInterface $bus, ValidatorInterface $validator): Response
    {
        $text = (string) $request->query->get('text');

        $sendMessage = new SendMessage($text);

        $errors = $validator->validate($sendMessage);

        if (count($errors) > 0) {
            return new Response((string) $errors, 400);
        }

        $bus->dispatch($sendMessage);

        return new Response('Successfully sent', 204);
    }
}
