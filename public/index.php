<?php
  require_once("socrata.php");

  $view_uid = array_get("view_uid", $_POST);
  $data_site = array_get("data_site", $_POST);
  $app_token = array_get("app_token", $_POST);
  $response = NULL;
  if($view_uid != NULL && $data_site != NULL) {
    // Create a new unauthenticated client
    $socrata = new Socrata("http://$data_site/api", $app_token);

    $params = array();
    $row_ids_only = array_get("row_ids_only", $_POST);
    if($row_ids_only != NULL && $row_ids_only == "on") {
      $params["row_ids_only"] = "true";
      $row_ids_only = true;
    }
    if(array_get("max_rows", $_POST) != NULL && array_get("max_rows", $_POST) > 0) {
      $params["max_rows"] = array_get("max_rows", $_POST);
    }

    // Request rows from the DC Hospitals view
    $response = $socrata->get("/views/$view_uid/rows.json", $params);
  }
?>
<html>
  <head>
    <title>Socrata API PHP Example</title>
  </head>
  <body>
    <h1>Socrata API PHP Example</h1>

    <?php if($response == NULL) { ?>
      <form action="index.php" method="post">
        <label for="data_site">Data Site:</label>
        <select name="data_site">
          <option value="data.medicare.gov" selected="true">Data.Medicare.Gov</option>
          <option value="data.seattle.gov">Data.Seattle.Gov</option>
          <option value="explore.data.gov">Data.Gov</option>
          <option value="opendata.go.ke">OpenData.go.ke</option>
          <option value="opendata.socrata.com">Socrata</option>
        </select><br/>

        <label for="app_token">App Token (<a href="http://dev.socrata.com/authentication">details</a>)</label>
        <input type="text" name="app_token" size="10"/><br/>

        <label for="view_uid">View ID</label>
        <input type="text" name="view_uid" size="10"/><br/>

        <label for="row_ids_only">Return only row IDs:</label>
        <input type="checkbox" name="row_ids_only"/><br/>

        <label for="max_rows">Return only # rows:</label>
        <input type="text" name="max_rows" size="4"/><br/>

        <input type="submit" value="Submit"/>
      </form>
    <?php } else { ?>
      <h2><?= $response["meta"]["view"]["name"] ?></h2>
      <p><?= $response["meta"]["view"]["description"] ?></p>

      <?# Create a table for our actual data ?>
      <table border="1">
        <tr>
          <?# Print header row ?>
          <?php foreach($response["meta"]["view"]["columns"] as $column) { ?>
            <th><?= $column["name"] ?></th>
          <?php } ?>
        </tr>
        <?# Print rows ?>
        <?php foreach($response["data"] as $row) { ?>
          <tr>
            <?php if($row_ids_only == true) { ?>
              <td><?= $row ?></td>
            <?php } else { ?>
              <?php foreach($row as $cell) { ?>
                <td><?= $cell ?></td>
              <?php } ?>
            <?php } ?>
          </tr>
        <?php } ?>
      </table>

      <h3>Raw Response</h3>
      <pre><?= var_dump($response) ?></pre>
    <?php } ?>
  </body>
</html>

