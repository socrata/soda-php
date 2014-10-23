<?php
if (file_exists("../vendor/autoload.php")) {
  require_once "../vendor/autoload.php";
} else {
  require_once "../src/Client.php";
}

use socrata\soda\Client;

// Convenience functions
function array_get($needle, array $haystack, $default = NULL) {
  return isset($haystack[$needle]) ? $haystack[$needle] : $default;
}

function pre_dump($var) {
  echo "<pre>" . print_r($var) . "</pre>";
}

$view_uid = "y9us-9xdf";
$root_url = "https://data.medicare.gov";
$footnote = array_get("footnote", $_POST);

// Create a new unauthenticated client
$sodaClient = new Client($root_url);
$params     = array();

if (!empty($footnote)) {
  $params['$where'] = "footnote=$footnote";
}

$response = $sodaClient->get("/resource/{$view_uid}.json", $params);
?>
<html>
  <head>
    <title>Medicare Footnote Crosswalk</title>
  </head>
  <body>
    <h1>Medicare Footnote Crosswalk</h1>
    <p><?= $root_url . "/resource/{$view_uid}.json" ?></p>
    <form method="POST">
      <label>Footnote: <input type="number" name="footnote" value="<?= $footnote ?>" /></label>
      <input type="submit" value="Search" />
    </form>
    <?php if (!isset($response['error'])) : ?>
      <h2>Results</h2>

      <?# Create a table for our actual data ?>
      <table border="1">
        <tr>
          <th>Footnote</th>
          <th>Footnote Text</th>
        </tr>
        <?# Print rows ?>
        <?php foreach($response as $row) : ?>
          <tr>
            <td><?= $row["footnote"] ?></td>
            <td><?= $row["footnote_text"] ?></td>
          </tr>
        <?php endforeach; ?>
      </table>
    <?php endif; ?>

    <h3>Raw Response</h3>
    <pre><?= var_dump($response) ?></pre>
  </body>
</html>

