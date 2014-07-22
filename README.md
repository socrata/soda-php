Socrata - Basic PHP Library
Updated 2014-07-22, Chris Metcalf, chris.metcalf (at) socrata.com

This library provides a simple wrapper for accessing some of the features of the Socrata Open Data API from PHP. Currently it supports HTTP GET, POST, and PUT operations.

The library is very simple. To access the Socrata API, you first instantiate a "Socrata" object, passing in the API base URL for the data site you wish to access. The Base URL is always the URL for the root of the datasite (ex: http://www.socrata.com or http://data.medicare.gov). Then you can use its included methods to make simple API calls:

```php
$socrata = new Socrata("http://data.medicare.gov");
$response = $socrata->get("/resource/abcd-2345.json");
```

## Querying

[Simple filters](http://dev.socrata.com/docs/filtering.html) and [SoQL Queries](http://dev.socrata.com/docs/queries.html) can be passed as a parameter to the `get` function:

```php
$socrata = new Socrata("https://data.austintexas.gov", $app_token);

$params = array("\$where" => "within_circle(location, $latitude, $longitude, $range)");

$response = $socrata->get("/resource/$view_uid.json", $params);
```

## Publishing

To use the library to publish data you can use the PUT (replace) or POST (upsert) methods:

```php
$socrata = new Socrata("https://data.medicare.gov", $app_token, $user_name, $password);

// Publish data via 'upsert'
$response = $socrata->post("/resource/abcd-2345.json", $data_as_json);

// Publish data via 'replace'
$response = $socrata->put("/resource/abcd-2345.json", $data_as_json);
```

The library also includes a simple example application, which retrieves rows from a dataset and dumps them in a simple table.
