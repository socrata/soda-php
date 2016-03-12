# Socrata - Basic PHP Library
This library provides a simple wrapper for accessing some of the features of the Socrata Open Data API from PHP. Currently it supports HTTP GET, POST, and PUT operations.

The library is very simple. To access the Socrata API, you first instantiate a "Socrata" object, passing in the domain of the data site you wish to access. The library will also accept the full root path including the protocol (ex: `http://data.medicare.gov`). Then you can use its included methods to make simple API calls:

## Install
Via composer

``` bash
composer require socrata/soda-php
```

In your API calls, specify ether the full endpoint relative path (eg: `/resource/abcd-2345.json`), or the dataset ID (eg: `abcd-2345`).

## Usage
```php
$socrata = new Socrata("data.medicare.gov");
$response = $socrata->get("abcd-2345");
```

## Querying

[Simple filters](http://dev.socrata.com/docs/filtering.html) and [SoQL Queries](http://dev.socrata.com/docs/queries.html) can be passed as a parameter to the `get` function:

```php
$socrata = new Socrata("data.austintexas.gov", $app_token);

$params = array("\$where" => "within_circle(location, $latitude, $longitude, $range)");

$response = $socrata->get($view_uid, $params);
```

## Publishing

To use the library to publish data you can use the PUT (replace) or POST (upsert) methods:

```php
$socrata = new Socrata("data.medicare.gov", $app_token, $user_name, $password);

// Publish data via 'upsert'
$response = $socrata->post("abcd-2345", $data_as_json);

// Publish data via 'replace'
$response = $socrata->put("abcd-2345", $data_as_json);
```

The library also includes a simple example application, which retrieves rows from a dataset and dumps them in a simple table.

## License

Apache License, Version 2.0. Please see [License File](LICENSE) for more information.
