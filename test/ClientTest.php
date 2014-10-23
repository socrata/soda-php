<?php

namespace socrata\soda\test;

use PHPUnit_Framework_TestCase as TestCase;
use socrata\soda\Client;

class ClientTest extends TestCase
{
    const SODA_HOST  = 'https://data.medicare.gov';
    const SODA_VIEW  = 'y9us-9xdf';
    const SODA_TOKEN = 'totallyfakenotrealapptoken';
    const SODA_USER  = 'fakeuser@socrata.com';
    const SODA_PASS  = 'fakepassword';

    private $path;

    public function setUp()
    {
        $this->path = '/resource/' . self::SODA_VIEW . '.json';
    }

    public function testGetReturnsArray()
    {
        $client = new Client(self::SODA_HOST);
        $result = $client->get($this->path, array('$limit' => '10'));
        $this->assertInternalType('array', $result);
        $this->assertCount(10, $result);
    }

    public function testPostResultIsError()
    {
        $client = new Client(self::SODA_HOST, self::SODA_TOKEN, self::SODA_USER, self::SODA_PASS);
        $result = $client->post($this->path, array(
            'footnote' => '99',
            'footnote_text' => 'soda-php sdk test - disregard'
        ));
        $this->assertArrayHasKey('error', $result);
        $this->assertTrue($result['error']);
    }

    public function testPutResultIsError()
    {
        $client = new Client(self::SODA_HOST, self::SODA_TOKEN);
        $result = $client->put($this->path, array(
            'footnote' => '99',
            'footnote_text' => 'remove me'
        ));
        $this->assertArrayHasKey('error', $result);
        $this->assertTrue($result['error']);
    }
}
