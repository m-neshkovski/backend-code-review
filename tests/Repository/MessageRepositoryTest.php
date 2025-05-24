<?php
declare(strict_types=1);

namespace Repository;

use App\Repository\MessageRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class MessageRepositoryTest extends KernelTestCase
{
    /*
     * TODO Fix this test
     *
     * ------ ------------------------------------------------
     * Line   tests/Repository/MessageRepositoryTest.php
     * ------ ------------------------------------------------
     * 17     Call to an undefined method object::findAll().
     * ------ ------------------------------------------------
     */

    public function test_it_has_connection(): void
    {
        self::bootKernel();

        $messages = self::getContainer()->get(MessageRepository::class);

        $this->assertSame([], $messages->findAll());
    }
}