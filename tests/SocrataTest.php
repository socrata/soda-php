<?php

use PhpUnit\Framework\TestCase;
require './public/socrata.php';

class SocrataTest extends TestCase {

  // Similar to other libraries, bare domain name and UID, no query passed
  public function test_domain_and_uid() {
    $client = new Socrata("soda.demo.socrata.com", getenv("APP_TOKEN"));
    $response = $client->get("6yvf-kk3n");

    // Do we get a result set
    $this->assertGreaterThan(0, sizeof($response));

    // Can we get the magnitude of the first quake?
    $this->assertGreaterThan(0, $response[0]["magnitude"]);
  }

  // Can we pass scheme + domain?
  public function test_scheme_and_domain() {
    $client = new Socrata("https://soda.demo.socrata.com", getenv("APP_TOKEN"));
    $response = $client->get("6yvf-kk3n");

    // Do we get a result set
    $this->assertGreaterThan(0, sizeof($response));

    // Can we get the magnitude of the first quake?
    $this->assertGreaterThan(0, $response[0]["magnitude"]);
  }

  // Can we pass a full resource path?
  public function test_resource_path() {
    $client = new Socrata("https://soda.demo.socrata.com", getenv("APP_TOKEN"));
    $response = $client->get("/resource/6yvf-kk3n");

    // Do we get a result set
    $this->assertGreaterThan(0, sizeof($response));

    // Can we get the magnitude of the first quake?
    $this->assertGreaterThan(0, $response[0]["magnitude"]);
  }

  // Can we pass a query with a limit?
  public function test_limit() {
    $client = new Socrata("soda.demo.socrata.com", getenv("APP_TOKEN"));
    $response = $client->get("6yvf-kk3n", array("\$limit" => 5));

    // Do we get a result set of size 5?
    $this->assertCount(5, $response);

    // Can we get the magnitude of the first quake?
    $this->assertGreaterThan(0, $response[0]["magnitude"]);
  }

  // Can we pass a simple filter?
  public function test_filter() {
    $client = new Socrata("soda.demo.socrata.com", getenv("APP_TOKEN"));
    $response = $client->get("6yvf-kk3n", array("\$limit" => 5, "source" => "ak"));

    // Do we get a result set of size 5?
    $this->assertCount(5, $response);

    // Verify the sources are all AK
    foreach($response as $quake) {
      $this->assertEquals("ak", $quake["source"]);
    }
  }

  // Can we pass an aggregation?
  public function test_agg() {
    $client = new Socrata("soda.demo.socrata.com", getenv("APP_TOKEN"));
    $response = $client->get("6yvf-kk3n", array("\$select" => "source,count(*) AS count", "\$group" => "source"));

    // Do we get our aggregation?
    $this->assertArrayHasKey("source", $response[0]);
    $this->assertArrayHasKey("count", $response[0]);
  }

  // What happens when we encounter an error?
  public function test_query_error() {
    $client = new Socrata("soda.demo.socrata.com", getenv("APP_TOKEN"));

    // We expect an error!
    $this->expectException(Exception::class);
    $response = $client->get("6yvf-kk3n", array("\$paramthatdoesntexist" => "diequerydie"));
  }

  // PUBLISHER TESTS
  public function test_upsert() {
    $client = new Socrata("soda.demo.socrata.com", getenv("APP_TOKEN"), getenv("SOCRATA_USER"), getenv("SOCRATA_PASSWORD"));
    $update = array(
      array("id" => "42", "name" => "answer to everything")
    );

    // Post our response
    $response = $client->post("wezw-qxis", json_encode($update));

    // Check that the response looks like what we want
    $this->assertArrayHasKey("Rows Updated", $response);
  }
}
?>
