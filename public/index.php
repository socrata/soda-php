<?php
  require_once("socrata.php");

  function array_get($needle, $haystack) {
    return (in_array($needle, array_keys($haystack)) ? $haystack[$needle] : NULL);
  }

  $view_uid = "h8x4-nvyi";
  $root_url = "data.austintexas.gov";
  $app_token = "B0ixMbJj4LuQVfYnz95Hfp3Ni";
  $response = NULL;

  $latitude = array_get("latitude", $_POST);
  $longitude = array_get("longitude", $_POST);
  $range = array_get("range", $_POST);

  if($latitude != NULL && $longitude != NULL && $range != NULL) {
    // Create a new unauthenticated client
    $socrata = new Socrata($root_url, $app_token);

    $params = array("\$where" => "within_circle(location, $latitude, $longitude, $range)");

    $response = $socrata->get($view_uid, $params);
  }
?>
<html>
  <head>
    <title>Austin Dangerous Dogs</title>
  </head>
  <body>
    <h1>Austin Dangerous Dogs</h1>

    <p>If you get no results, its likely because there are no dangerous dogs at that location. Try another lat/long.</p>

    <?php if($response == NULL) { ?>
      <form action="index.php" method="POST">
        <label for="latitude">Latitude</label>
        <input type="text" name="latitude" size="10" value="30.244588"/><br/>

        <label for="longitude">Longitude</label>
        <input type="text" name="longitude" size="10" value="-97.5824817"/><br/>

        <label for="range">Range</label>
        <input type="text" name="range" size="10" value="10000"/><br/>

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
            <td><a href="https://www.google.com/maps/search/<?= $row["location"]["coordinates"][1] ?>,<?= $row["location"]["coordinates"][0] ?>"><?= $row["address"] ?></a></td>
          </tr>
        <?php } ?>
      </table>

      <h3>Raw Response</h3>
      <pre><?= var_dump($response) ?></pre>
    <?php } ?>
  </body>
</html>

