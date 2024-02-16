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
    }
}
