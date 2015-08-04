<?php

namespace Acmtool\AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AuthentificationControllerTest extends WebTestCase
{
    public function testApiauthentification()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/public/login');
    }

}
