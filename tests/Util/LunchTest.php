<?php
namespace App\Tests\Util;

use App\LunchCalculator;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class LunchTest extends WebTestCase
{
    public function testIndex()
    {
        
        $client = static::createClient();

        $client->request('GET', '/lunch');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            ),
            'the "Content-Type" header is "application/json"' // optional message shown on failure
        );

        $client->request('GET', '/lunch?date=date');

        $this->assertEquals(400, $client->getResponse()->getStatusCode());
        
    }

}