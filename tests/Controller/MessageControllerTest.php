<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Message\SendMessage;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Messenger\Test\InteractsWithMessenger;

class MessageControllerTest extends WebTestCase
{
    use InteractsWithMessenger;

    public function testListWithStatusNotSet(): void
    {
        $client = static::createClient();

        $client->request('GET', '/messages');

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('Content-Type', 'application/json');
        $this->assertIsString($client->getResponse()->getContent());
        $response = (array) json_decode($client->getResponse()->getContent(), true);
        $this->assertIsArray($response['messages']);
    }

    public function testListWithValidStatusSet(): void
    {
        $client = static::createClient();

        $client->request('GET', '/messages', ['status' => 'sent']);

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('Content-Type', 'application/json');
        $this->assertIsString($client->getResponse()->getContent());
        $response = (array) json_decode($client->getResponse()->getContent(), true);
        $this->assertIsArray($response['messages']);
    }

    public function testListWithInvalidStatusSet(): void
    {
        $client = static::createClient();

        $client->request('GET', '/messages', ['status' => 'invalid_status']);

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('Content-Type', 'application/json');
        $this->assertIsString($client->getResponse()->getContent());
        $response = (array) json_decode($client->getResponse()->getContent(), true);
        $this->assertSame([], $response['messages']);
    }

    public function testThatItFailsIfMessageTextEmpty(): void
    {
        $client = static::createClient();
        $client->request('GET', '/messages/send');

        $this->assertResponseStatusCodeSame(400);
    }

    public function testThatItFailsIfMessageTextLongerThan255Chars(): void
    {
        $client = static::createClient();

        $client->request('GET', '/messages/send', [
            'text' => str_repeat('a', 256),
        ]);

        $this->assertResponseStatusCodeSame(400);
    }

    public function testThatItSendsAMessage(): void
    {
        $client = static::createClient();
        $client->request('GET', '/messages/send', [
            'text' => 'Hello Team "Trust" of Digistore24!',
        ]);

        $this->assertResponseIsSuccessful();
        // This is using https://packagist.org/packages/zenstruck/messenger-test
        $this->transport('sync')
            ->queue()
            ->assertContains(SendMessage::class, 1);
    }
}
