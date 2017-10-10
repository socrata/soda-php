<?php

namespace socrata\soda\test;

use PHPUnit\Framework\TestCase as TestCase;
use socrata\soda\Client;

class ClientTest extends TestCase
{
    const SODA_HOST  = 'https://sandbox.demo.socrata.com';
    const SODA_DATASET_FOR_QUERIES = 'nimj-3ivp';
    const SODA_DATASET_FOR_UPDATES  = 'def3-fazd';
    const SODA_TOKEN = 'Va5h7Eu1PA1f1mVwbsWI89SvY';

    public function getAuthenticatedClient()
    {
        return new Client(self::SODA_HOST, self::SODA_TOKEN, getenv("SOCRATA_USER"), getenv("SOCRATA_PASSWORD"));
    }

    public function getAnonymousClient()
    {
        return new Client(self::SODA_HOST, self::SODA_TOKEN);
    }

    public function buildPath($uid)
    {
        return '/resource/' . $uid . '.json';
    }

    // READ Functions
    public function testGetReturnsArray()
    {
        $client = $this->getAnonymousClient();;
        $result = $client->get($this->buildPath(self::SODA_DATASET_FOR_QUERIES), array('$limit' => '10'));
        $this->assertInternalType('array', $result);
        $this->assertCount(10, $result);
    }

    public function testGetWithFilter()
    {
        $client = $this->getAnonymousClient();;
        $result = $client->get($this->buildPath(self::SODA_DATASET_FOR_QUERIES), array('$limit' => '10', 'source' => 'ak'));
        $this->assertInternalType('array', $result);
        $this->assertCount(10, $result);
        foreach($result as $idx => $entry)
        {
          $this->assertEquals('ak', $entry['source']);
        }
    }

    public function testGetWithQuery()
    {
        $client = $this->getAnonymousClient();;
        $result = $client->get($this->buildPath(self::SODA_DATASET_FOR_QUERIES), array('$limit' => '10', '$where' => 'magnitude > 2.0'));
        $this->assertInternalType('array', $result);
        $this->assertCount(10, $result);
        foreach($result as $idx => $entry)
        {
          $this->assertTrue($entry['magnitude'] > 2.0);
        }
    }

    public function testInvalidGet()
    {
        $client = $this->getAnonymousClient();;
        $result = $client->get($this->buildPath(self::SODA_DATASET_FOR_QUERIES), array('$kaboom' => true));
        $this->assertTrue($result['error']);
    }


    // WRITE Functions
    public function testPostSingleRow()
    {
        $client = $this->getAuthenticatedClient();
        $result = $client->post($this->buildPath(self::SODA_DATASET_FOR_UPDATES), array(
            'text' => 'foobar',
            'number' => 42
        ));
        $this->assertArrayHasKey('text', $result);
        $this->assertEquals('foobar', $result['text']);
    }

    public function testPostUpsert()
    {
        $client = $this->getAuthenticatedClient();
        $result = $client->post($this->buildPath(self::SODA_DATASET_FOR_UPDATES), array(
          array(
            'text' => 'foobar',
            'number' => 42
          ),
          array(
            'text' => 'quobang',
            'number' => 43
          )
        ));
        $this->assertArrayHasKey('Rows Created', $result);
        $this->assertEquals(2, $result['Rows Created']);
    }
}
