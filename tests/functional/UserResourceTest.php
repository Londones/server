<?php

namespace App\Tests\Functional;

use App\Entity\User;
use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use Symfony\Component\HttpClient\Exception\ClientException;

class UserResourceTest extends ApiTestCase
{
    public function testUser()
    {
        $client = self::createClient();

        // Get the password hasher from the service container
        $passwordHasher = $this->getContainer()->get('security.password_hasher');

        // Hash the password
        $hashedPassword = $passwordHasher->hashPassword(new User(), 'password');
        $hashedPassword2 = $passwordHasher->hashPassword(new User(), 'password');

        $user = new User();
        $user->setNom('test');
        $user->setPrenom('test');
        $user->setRoles(['ROLE_USER']);
        $user->setEmail('test@test.com');
        $user->setPassword($hashedPassword);

        $em = $this->getContainer()->get('doctrine')->getManager();
        $em->persist($user);
        $em->flush();

        $userId = $user->getId();

        $user2 = new User();
        $user2->setNom('test2');
        $user2->setPrenom('test2');
        $user2->setRoles(['ROLE_USER']);
        $user2->setEmail('test2@test.com');
        $user2->setPassword($hashedPassword2);

        $em = $this->getContainer()->get('doctrine')->getManager();
        $em->persist($user2);
        $em->flush();

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
            $this->assertEquals(422, $client->getResponse()->getStatusCode());

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

        $token = $response['token'];

        //access denied for a get on /api/users/{id} that's not the userId
        $client->request('GET', '/api/users/' . ($userId + 1), [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
            ],
        ]);

        $this->assertResponseStatusCodeSame(403);

        //access denied for a get on /api/users
        $client->request('GET', '/api/users', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
            ],
        ]);

        $this->assertResponseStatusCodeSame(403);
    }

    public function testAdminUser()
    {
        $client = self::createClient();

        // Get the password hasher from the service container
        $passwordHasher = $this->getContainer()->get('security.password_hasher');
        $hashedPassword2 = $passwordHasher->hashPassword(new User(), 'password');


        // Hash the password
        $hashedPassword = $passwordHasher->hashPassword(new User(), 'password');

        // Use the hashed password when creating the user
        $admin = new User();
        $admin->setNom('admin');
        $admin->setPrenom('admin');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setEmail('admin@test.com');
        $admin->setPassword($hashedPassword);

        $em = $this->getContainer()->get('doctrine')->getManager();
        $em->persist($admin);
        $em->flush();

        $user2 = new User();
        $user2->setNom('test2');
        $user2->setPrenom('test2');
        $user2->setRoles(['ROLE_USER']);
        $user2->setEmail('test2@test.com');
        $user2->setPassword($hashedPassword2);

        $em = $this->getContainer()->get('doctrine')->getManager();
        $em->persist($user2);
        $em->flush();

        // get admin id
        $adminId = $admin->getId();

        $client->request('POST', '/api/login', [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'email' => 'admin@test.com',
                'password' => 'password',
            ]
        ]);

        $this->assertResponseStatusCodeSame(200);

        $response = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('token', $response);

        $token = $response['token'];

        // access granted for a get on /api/users
        $client->request('GET', '/api/users', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
            ],
        ]);

        $this->assertResponseStatusCodeSame(200);

        // access granted for a get on /api/users/{id} that's not the userId
        $client->request('GET', '/api/users/' . ($adminId + 1), [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
            ],
        ]);

        $this->assertResponseStatusCodeSame(200);


        // access granted for a get on /api/users/{id} that's the userId
        $client->request('GET', '/api/users/' . $adminId, [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
            ],
        ]);

        $response = $client->getResponse();
        $content = json_decode($response->getContent(), true);

        // If the user is supposed to have access, assert that the status code is 200
        // If the user is not supposed to have access, assert that the status code is 403
        $this->assertEquals(200, $response->getStatusCode(), "Unexpected response: " . $response->getContent());
    }
}
