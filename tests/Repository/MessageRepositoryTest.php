<?php

declare(strict_types=1);

namespace Repository;

use App\Repository\MessageRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class MessageRepositoryTest extends KernelTestCase
{
    public function testItHasConnection(): void
    {
        self::bootKernel();

        /**
         * @var MessageRepository $messagesRepository
         */
        $messagesRepository = self::getContainer()->get(MessageRepository::class);

        $this->assertSame([], $messagesRepository->findAll());
    }
}
