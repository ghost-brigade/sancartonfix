<?php

namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;

class AuthenticationTest extends ApiTestCase
{
    public function testUserAuthentication(): void
    {
        $response = static::createClient()->request('POST', '/authentication_token', [
            'json' => [
                'email' => 'admin@localhost',
                'password' => 'password',
            ],
        ]);

        $GLOBALS['token'] = $response->toArray()['token'];

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/json');
    }
}
