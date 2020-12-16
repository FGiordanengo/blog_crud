<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AppTest extends WebTestCase
{
    public function testSomething()
    {
        $this->assertTrue(true);
    }

    public function testIndex() {
        $client = self::createClient();
        $client->request('GET', '/');
        $this->assertResponseStatusCodeSame(200);
    }

    public function testForceAdmin() {
        $client = self::createClient();
        $client->request('GET', '/admin');
        $this->assertResponseRedirects('/connexion');
        $this->assertResponseStatusCodeSame(302);
    }

    public function testLoginAdminWithGoodCredentials() {
        $client = self::createClient();
        $crawler = $client->request('GET', '/connexion');
        $form = $crawler->selectButton('Connexion')->form([
            'email' => 'florent.giordanengo@hotmail.fr',
            'password' => 'WyE8x45Kn'
        ]);
        $client->submit($form);
        $this->assertResponseRedirects('/admin');
    }

    public function testLoginAdminWithBadCredentials() {
        $client = self::createClient();
        $crawler = $client->request('GET', '/connexion');
        $form = $crawler->selectButton('Connexion')->form([
            'email' => 'florent.giordanengo@hotmail.fr',
            'password' => 'WysssE8x4sss5Kn'
        ]);
        $client->submit($form);
        $this->assertResponseRedirects('/connexion');
        $client->followRedirect();
        $this->assertSelectorExists('.alert.alert-danger');
    }
}
