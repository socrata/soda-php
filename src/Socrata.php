<?php

namespace Socrata;

class Socrata
{
    /**
     * The base URL for this Socrata API, ex: http://data.medicare.gov or http://www.socrata.com
     *
     * @var string
     */
    private $root_url = "http://opendata.socrata.com";

    /**
     * App Token
     *
     * @var string
     */
    private $app_token = "";

    /**
     * Username, used for authenticated requests
     *
     * @var string
     */
    private $user_name = "";

    /**
     * Password, used for authenticated requests
     *
     * @var string
     */
    private $password = "";

    /**
     * Basic constructor
     *
     * @param string $root_url
     * @param string $app_token
     * @param string $user_name
     * @param string $password
     */
    public function __construct($root_url, $app_token = "", $user_name = "", $password = "") {
        $this->root_url = $root_url;
        $this->app_token = $app_token;
        $this->user_name = $user_name;
        $this->password = $password;
    }

    /**
     * create query URL based on the root URL, path, and parameters
     *
     * @param  string $path
     * @param  array  $params
     * @return string
     */
    private function create_query_url($path, array $params = array())
    {
        // The full URL for this resource is the root + the path
        $full_url = $this->root_url . $path;

        // Build up our array of parameters
        $parameters = array();
        foreach ($params as $key => $value) {
            array_push($parameters, urlencode($key) . "=" . urlencode($value));
        }
        if (count($parameters) > 0) {
            $full_url .= "?" . implode("&", $parameters);
        }

        return $full_url;
    }

    /**
     * create cURL handle, which can then be submitted via get
     *
     * @param  string $path
     * @param  array  $params
     * @return resource
     */
    private function create_curl_handle($path, array $params = array())
    {
        // The full URL for this resource is the root + the path
        $full_url = $this->create_query_url($path, $params);

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
        if ($this->user_name != "" && $this->password != "") {
            curl_setopt($handle, CURLOPT_USERPWD, $this->user_name . ":" . $this->password);
        }

        return $handle;
    }

    /**
     * Convenience function for GET calls
     *
     * @param  string $path
     * @param  array  $params
     * @return mixed
     */
    public function get($path, array $params = array())
    {
        $handle = $this->create_curl_handle($path, $params);

        $response = curl_exec($handle);
        $code = curl_getinfo($handle, CURLINFO_HTTP_CODE);
        if ($code != "200") {
            die("Error \"$code\" from server: $response");
        }

        return json_decode($response, true);
    }

    /**
     * Convenience function for Posts
     *
     * @param  string $path
     * @param  bool   $json_filter
     * @return mixed
     */
    public function post($path, $json_filter)
    {
        // The full URL for this resource is the root + the path
        $full_url = $this->root_url . $path;

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
        curl_setopt($handle, CURLOPT_POST, true);
        curl_setopt($handle, CURLOPT_POSTFIELDS, $json_filter);
        curl_setopt($handle, CURLOPT_CUSTOMREQUEST, "POST");

        // Set up request, and auth, if configured
        if ($this->user_name != "" && $this->password != "") {
            curl_setopt($handle, CURLOPT_USERPWD, $this->user_name . ":" . $this->password);
        }

        $response = curl_exec($handle);
        $code = curl_getinfo($handle, CURLINFO_HTTP_CODE);
        if ($code != "200") {
            die("Error \"$code\" from server: $response");
        }

        return json_decode($response, true);
    }

    /**
     * Convenience function for Puts
     *
     * @param  string $path
     * @param  bool   $json_filter
     * @return mixed
     */
    public function put($path, $json_filter)
    {
        // The full URL for this resource is the root + the path
        $full_url = $this->root_url . $path;

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
        curl_setopt($handle, CURLOPT_POSTFIELDS, $json_filter);
        curl_setopt($handle, CURLOPT_CUSTOMREQUEST, "PUT");

        // Set up request, and auth, if configured
        if ($this->user_name != "" && $this->password != "") {
            curl_setopt($handle, CURLOPT_USERPWD, $this->user_name . ":" . $this->password);
        }

        $response = curl_exec($handle);
        $code = curl_getinfo($handle, CURLINFO_HTTP_CODE);
        if ($code != "200") {
            die("Error \"$code\" from server: $response");
        }

        return json_decode($response, true);
    }
}
