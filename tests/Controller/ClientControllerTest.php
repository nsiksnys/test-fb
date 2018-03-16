<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ClientControllerTest extends WebTestCase
{
/*    public function testGetProfileOK()
    {
        $client = static::createClient();
		$id = rand();
		
        $client->request('GET', "/profile/facebook/$id");

        $this->assertEquals($client->getResponse()->isSuccessful());
    }
*/	
	public function testResponseIsJson()
	{
		$client = static::createClient();
		$id = rand();
		
        $client->request('GET', "/profile/facebook/$id");
		// asserts that the "Content-Type" header is "application/json"
		$this->assertTrue($client->getResponse()->headers->contains('Content-Type','application/json'));
	}
	
	public function testGetProfileError()
	{
        $client = static::createClient();
		$id = 1 ;
		
        $client->request('GET', "/profile/facebook/$id");

        $this->assertEquals(500, $client->getResponse()->getStatusCode());
    }

}