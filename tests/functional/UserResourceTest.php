<?php

namespace App\Tests\Functional;

use App\Entity\User;
use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use Symfony\Component\HttpClient\Exception\ClientException;

class UserResourceTest extends ApiTestCase
{
    public function testCreateUser()
    {
        $client = self::createClient();

        $client->request('POST', '/api/users', [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'nom' => 'test',
                'prenom' => 'test',
                'roles' => ['ROLE_USER'],
                'email' => 'test@test.com',
                'password' => 'password',
            ]
        ]);

        $this->assertEquals(201, $client->getResponse()->getStatusCode());

        try {
            $client->request('POST', '/api/users', [
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'nom' => 'test2',
                    'prenom' => 'test2',
                    'roles' => ['ROLE_USER'],
                    'email' => 'test@test.com',
                    'password' => 'password2',
                ]
            ]);
        } catch (ClientException $e) {
            // Assert that the response status code is 422 (Unprocessable Entity)
            $this->assertEquals(422, $client->getResponse()->getStatusCode());

            // Assert that the response body contains the expected error message
            $response = json_decode($client->getResponse()->getContent(), true);
            $this->assertEquals('email', $response['violations'][0]['propertyPath']);
            $this->assertEquals('Un compte existe déjà avec cet email', $response['violations'][0]['message']);
        }

        $client->request('POST', '/api/login', [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'email' => 'test@test.com',
                'password' => 'password',
            ]
        ]);

        $this->assertResponseStatusCodeSame(200);

        $response = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('token', $response);
    }
}
