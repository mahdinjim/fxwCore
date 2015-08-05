<?php

namespace Acmtool\AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DeveloperControllerTest extends WebTestCase
{
    public function testCreate()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/private/super/developer/create');
    }

    public function testUpdate()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/private/developer/update');
    }

    public function testDelete()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/private/super/developer/delete/{id}');
    }

    public function testList()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/private/super/developer/all/{page}');
    }

    public function testDetails()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/private/developer/profile/{id}');
    }

}
