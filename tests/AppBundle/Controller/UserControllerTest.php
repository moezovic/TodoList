<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use AppBundle\Entity\User;
use Faker;

class UserControllerTest extends WebTestCase
{
    public function testUserListingWithoutAuthentification()
    {
        $client = static::createClient();
        $client->followRedirects();
        $crawler = $client->request('GET', '/users');
 

        $this->assertGreaterThan(
            0,
            $crawler->filter('html:contains("Se connecter")')->count()
        );
    }

    public function testUserListingWithAuthentification()
    {
        $client = static::createClient([], [
            'PHP_AUTH_USER' => 'user_1',
            'PHP_AUTH_PW'   => 'user_1',
        ]);
        $client->followRedirects();
        $crawler = $client->request('GET', '/users');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        
    //     var_dump($client->getResponse()->getContent());
    //    die;

        $this->assertGreaterThan(
            0,
            $crawler->filter('html:contains("Liste des utilisateurs")')->count()
        );
        
    }

    public function testNewUserAccountCreation(){
        $client = static::createClient();
        $client->followRedirects();
        $crawler = $client->request('GET', '/users/create');

        $faker = Faker\Factory::create('fr_FR');
 
        $form = $crawler->selectButton('Ajouter')->form();
        $form['user[username]'] = $faker->name;
        $form['user[password][first]'] = "test-password";
        $form['user[password][second]'] = "test-password";
        $form['user[email]'] = $faker->email;

        // After submission check true redirection
        $crawler = $client->submit($form);

        $this->assertGreaterThan(
            0,
            $crawler->filter('html:contains("L\'utilisateur a bien été ajouté.")')->count()
        );

    }

    public function testUserInfoEdition(){

        $client = static::createClient([], [
            'PHP_AUTH_USER' => 'user_2',
            'PHP_AUTH_PW'   => 'user_2',
        ]);

        $kernel = self::bootKernel();

        $em = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $user = $em->getRepository(User::class)->findBy(['username' => 'user_2']);

        $crawler = $client->request('GET', '/users/'.$user[0]->getId().'/edit');

        $form = $crawler->selectButton('Modifier')->form();
        $form['user[username]'] = "edit-user";
        $form['user[password][first]'] = "edit-password";
        $form['user[password][second]'] = "edit-password";
        $form['user[email]'] = "edit@edit.com";


        $crawler = $client->submit($form);


        $this->assertTrue(
          $client->getResponse()->isRedirect('/')
        );

        $this->assertGreaterThan(
            0,
            $crawler->filter('html:contains("L\'utilisateur a bien été modifié.")')->count()
        );

    }

    public function testSwitchingRoleToAdmin(){
        $client = static::createClient([], [
            'PHP_AUTH_USER' => 'user_1',
            'PHP_AUTH_PW'   => 'user_1',
        ]);
        $client->followRedirects();
        $crawler = $client->request('GET', '/users');

        $link = $crawler
        ->filter('a:contains("Mettre en administrateur")')
        ->eq(0) 
        ->link()
        ;
        $crawler = $client->click($link);

        $this->assertCount(
            2,
            $crawler->filter('a:contains("Mettre en utilisateur")')
        );
    }

    public function testSwitchingRoleToUser(){
        $client = static::createClient([], [
            'PHP_AUTH_USER' => 'user_1',
            'PHP_AUTH_PW'   => 'user_1',
        ]);
        $client->followRedirects();
        $crawler = $client->request('GET', '/users');

        $link = $crawler
        ->filter('a:contains("Mettre en utilisateur")')
        ->eq(0) 
        ->link()
        ;
        $crawler = $client->click($link);

        $this->assertEquals(
            1,
            $crawler->filter('a:contains("Mettre en utilisateur")')->count()
        );
    }
    
}