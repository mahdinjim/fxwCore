<?php

namespace Acmtool\AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TeamLeaderControllerTest extends WebTestCase
{
    public function testCreate()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/private/super/teamleader/create');
    }

    public function testUpdate()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', 'private/teamleader/update');
    }

    public function testDelete()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/private/super/teamleader');
    }

    public function testList()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/private/super/teamleader/all');
    }

    public function testDetails()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/private/teamleader/profile');
    }

}
