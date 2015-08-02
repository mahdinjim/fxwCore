<?php

namespace Acmtool\AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AdminControllerControllerTest extends WebTestCase
{
    public function testCreate()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/secret/once/create');
    }

    public function testAuthentificate()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/public/super/login');
    }

    public function testUpdate()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/private/super/update');
    }

}
