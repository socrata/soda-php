<?php
  require_once("socrata.php");

  $view_uid = "ykw4-j3aj";
  $root_url = "https://data.austintexas.gov";
  $app_token = "B0ixMbJj4LuQVfYnz95Hfp3Ni";
  $response = NULL;

  $latitude = array_get("latitude", $_POST);
  $longitude = array_get("longitude", $_POST);
  $range = array_get("range", $_POST);

  if($latitude != NULL && $longitude != NULL && $range != NULL) {
    // Create a new unauthenticated client
    $socrata = new Socrata($root_url, $app_token);

    $params = array("\$where" => "within_circle(location, $latitude, $longitude, $range)");

    $response = $socrata->get("/resource/$view_uid.json", $params);
  }
?>
<html>
  <head>
    <title>Austin Dangerous Dogs</title>
  </head>
  <body>
    <h1>Austin Dangerous Dogs</h1>

    <?php if($response == NULL) { ?>
      <form action="index.php" method="POST">
        <p>Try 30.27898, -97.68351 with a range of 1000 meters</p>

        <label for="latitude">Latitude</label>
        <input type="text" name="latitude" size="10" value="30.27898"/><br/>

        <label for="longitude">Longitude</label>
        <input type="text" name="longitude" size="10" value="-97.68351"/><br/>

        <label for="range">Range</label>
        <input type="text" name="range" size="10" value="1000"/><br/>

        <input type="submit" value="Submit"/>
      </form>
    <?php } else { ?>
      <h2>Results</h2>

      <?# Create a table for our actual data ?>
      <table border="1">
        <tr>
          <th>Description</th>
          <th>Address</th>
        </tr>
        <?# Print rows ?>
        <?php foreach($response as $row) { ?>
          <tr>
            <td><?= $row["description_of_dog"] ?></td>
            <td><?= $row["address"] ?></td>
          </tr>
        <?php } ?>
      </table>

      <h3>Raw Response</h3>
      <pre><?= var_dump($response) ?></pre>
    <?php } ?>
  </body>
</html>

