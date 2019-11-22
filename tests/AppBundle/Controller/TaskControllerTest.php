<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use AppBundle\Entity\Task;

class TaskControllerTest extends WebTestCase
{
    public function testTaskListingWithAuthentification()
    {
        
        $client = static::createClient([], [
            'PHP_AUTH_USER' => 'user_1',
            'PHP_AUTH_PW'   => 'user_1',
        ]);
        $client->followRedirects();
        $crawler = $client->request('GET', '/tasks');


       $this->assertGreaterThan(
        0,
        $crawler->filter('html:contains("Marquer comme faite")')->count()
    );
    }

    public function testTaskListingNotAuthentified()
    {
        
        $client = static::createClient();
        $client->followRedirects();
        $crawler = $client->request('GET', '/tasks');


        $this->assertTrue(
            $client->getResponse()->isRedirect('/login')
        );
    }

    public function testTaskCreation(){
        $client = static::createClient([], [
            'PHP_AUTH_USER' => 'user_1',
            'PHP_AUTH_PW'   => 'user_1',
        ]);
        $client->followRedirects();
        $crawler = $client->request('GET', '/tasks/create');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
 
        $form = $crawler->selectButton('Ajouter')->form();
        $form['task[title]'] = "test-title";
        $form['task[content]'] = "test-content";

        $crawler = $client->submit($form);
        $this->assertGreaterThan(
            0,
            $crawler->filter('html:contains("La tâche a été bien été ajoutée.")')->count()
        );
    }

    public function testTaskEdition(){

        $client = static::createClient([], [
            'PHP_AUTH_USER' => 'user_1',
            'PHP_AUTH_PW'   => 'user_1',
        ]);
        $client->followRedirects();

        $crawler = $client->request('GET', '/tasks/'.$this->getTaskId().'/edit');

        $form = $crawler->selectButton('Modifier')->form();
        $form['task[title]'] = "Edited title";
        $form['task[content]'] = "Edited content";

        $crawler = $client->submit($form);

        $this->assertGreaterThan(
            0,
            $crawler->filter('html:contains("La tâche a bien été modifiée.")')->count()
        );

    }

    public function testTaskToggleAction(){

        $client = static::createClient([], [
            'PHP_AUTH_USER' => 'user_1',
            'PHP_AUTH_PW'   => 'user_1',
        ]);
        $client->followRedirects();

        $crawler = $client->request('GET', '/tasks/'.$this->getTaskId().'/toggle');

        $this->assertGreaterThan(
            0,
            $crawler->filter('html:contains("Superbe")')->count()
        );
        
    }

    public function testTaskDelete(){

        $client = static::createClient([], [
            'PHP_AUTH_USER' => 'user_1',
            'PHP_AUTH_PW'   => 'user_1',
        ]);
        $client->followRedirects();

        $crawler = $client->request('GET', '/tasks/'.$this->getTaskId().'/delete');


        $this->assertGreaterThan(
            0,
            $crawler->filter('html:contains("La tâche a bien été supprimée.")')->count()
        );

    }

    public function getTaskId(){

        $kernel = self::bootKernel();

        $em = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $query = $em->createQuery(
            'SELECT t
            FROM AppBundle:Task t
            JOIN t.user u
            WHERE u.username = :username'
        )->setParameter('username', 'user_1');

        $tasks = $query->getResult();

        return $tasks[0]->getId();

    }
}