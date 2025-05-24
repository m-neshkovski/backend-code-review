<?php
declare(strict_types=1);

namespace Controller;

use App\Message\SendMessage;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Messenger\Test\InteractsWithMessenger;

class MessageControllerTest extends WebTestCase
{
    use InteractsWithMessenger;

    function test_list_with_status_not_set(): void
    {
        $client = static::createClient();

        $client->request('GET', '/messages');

        $this->assertResponseIsSuccessful();
        $this->assertIsString($client->getResponse()->getContent());
        $response = (array) json_decode($client->getResponse()->getContent(), true);
        $this->assertIsArray($response['messages']);
    }

    function test_list_with_valid_status_set(): void
    {
        $client = static::createClient();

        $client->request('GET', '/messages', ['status' => 'sent']);

        $this->assertResponseIsSuccessful();
        $this->assertIsString($client->getResponse()->getContent());
        $response = (array) json_decode($client->getResponse()->getContent(), true);
        $this->assertIsArray($response['messages']);
    }

    function test_list_with_invalid_status_set(): void
    {
        $client = static::createClient();

        $client->request('GET', '/messages', ['status' => 'invalid_status']);

        $this->assertResponseIsSuccessful();
        $this->assertIsString($client->getResponse()->getContent());
        $response = (array) json_decode($client->getResponse()->getContent(), true);
        $this->assertSame([], $response['messages']);
    }

    function test_that_it_sends_a_message(): void
    {
        $client = static::createClient();
        $client->request('GET', '/messages/send', [
            'text' => 'Hello World',
        ]);

        $this->assertResponseIsSuccessful();
        // This is using https://packagist.org/packages/zenstruck/messenger-test
        $this->transport('sync')
            ->queue()
            ->assertContains(SendMessage::class, 1);
    }
}