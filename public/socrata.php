<?php

class Socrata {
  // The base URL for this Socrata API, ex: http://data.medicare.gov/api or http://www.socrata.com/api
  private $root_url = "http://opendata.socrata.com/api";

  // App Token
  private $app_token = "";

  // Username and password, used for authenticated requests
  private $user_name = "";
  private $password = "";

  // Basic constructor
  public function __construct($root_url = "", $app_token = "",  $user_name = "", $password = "") {
    $this->root_url = $root_url;
    $this->app_token = $app_token;
    $this->user_name = $user_name;
    $this->password = $password;
    return true;
  }

  // Convenience function for GET calls
  public function get($path, $params = array()) {

    // The full URL for this resource is the root + the path
    $full_url = $this->root_url . $path;

    // Build up our array of parameters
    $parameters = array();
    foreach($params as $key => $value) {
      array_push($parameters, urlencode($key) . "=" . urlencode($value));
    }
    if(count($parameters) > 0) {
      $full_url .= "?" . implode("&", $parameters);
    }

    // Build up the headers we'll need to pass
    $headers = array(
      'Accept: application/json',
      'Content-type: application/json',
      "X-App-Token: " . $this->app_token
    );

    // Time for some cURL magic...
    $handle = curl_init();
    curl_setopt($handle, CURLOPT_URL, $full_url);
    curl_setopt($handle, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);

    // Set up request, and auth, if configured
    if($this->user_name != "" && $this->password != "") {
      curl_setopt($handle, CURLOPT_USERPWD, $this->user_name . ":" . $this->password);
    }

    $response = curl_exec($handle);
    $code = curl_getinfo($handle, CURLINFO_HTTP_CODE);
    if($code != "200") {
      echo "Error \"$code\" from server: $response";
      die();
    }

    return json_decode($response, true);
  }
}

// Convenience functions
function array_get($needle, $haystack) {
  return (in_array($needle, array_keys($haystack)) ? $haystack[$needle] : NULL);
}

function pre_dump($var) {
  echo "<pre>" . print_r($var) . "</pre>";
}
?>
