Socrata - Basic PHP Library
Updated 2010-04-26, Chris Metcalf, chris.metcalf (at) socrata.com

This library provides a simple wrapper for accessing some of the features of the Socrata Open Data API from PHP. Currently it supports HTTP GET, POST, and PUT operations.

The library is very simple. To access the Socrata API, you first instantiate a "Socrata" object, passing in the API base URL for the data site you wish to access. The Base URL is always the URL for the root of the datasite, with "/api" added to the path (ex: http://www.socrata.com/api or http://data.medicare.gov/api). Then you can use its included methods to make simple API calls:

```php
$socrata = new Socrata("http://data.medicare.gov/api");
$response = $socrata->get("/views/abcd-2345/rows.json");
```

To use the library to publish data you can use the PUT (replace) or POST (upsert) methods:

```php
$socrata = new Socrata("https://data.medicare.gov", $app_token, $user_name, $password);

// Publish data via 'upsert'
$response = $socrata->post("/resource/abcd-2345.json", $data_as_json);

// Publish data via 'replace'
$response = $socrata->put("/resource/abcd-2345.json", $data_as_json);
```

The library also includes a simple example application, which retrieves rows from a dataset and dumps them in a simple table.
