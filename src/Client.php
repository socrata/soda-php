<?php
/**
 * Client.php
 *
 * @copyright 2014 Socrata, Inc.
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache 2.0 License
 */

namespace socrata\soda;

/**
 * A simple CURL-based client for Socrata's Open Data Api (SODA)
 */
final class Client
{
    /**
     * The base URL for this Socrata API, ex: http://data.medicare.gov or http://www.socrata.com
     *
     * @var string
     */
    private $root_url;

    /**
     * App Token
     *
     * @var string
     */
    private $app_token;

    /**
     * Username, used for authenticated requests
     *
     * @var string
     */
    private $user_name;

    /**
     * Password, used for authenticated requests
     *
     * @var string
     */
    private $password;

    /**
     * Basic constructor
     *
     * @param string $root_url
     * @param string $app_token
     * @param string $user_name
     * @param string $password
     */
    public function __construct($root_url, $app_token = "", $user_name = "", $password = "") {
        $this->root_url  = $root_url;
        $this->app_token = $app_token;
        $this->user_name = $user_name;
        $this->password  = $password;
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
        $full_url = $this->buildQueryUrl($path, $params);
        $handle   = $this->buildHandle($full_url);

        return $this->executeAndDecode($handle);
    }

    /**
     * Convenience function for Posts
     *
     * @param  string $path
     * @param  mixed  $data
     * @return mixed
     */
    public function post($path, $data)
    {
        $full_url = $this->root_url . $path;
        $handle   = $this->buildHandle($full_url);

        curl_setopt($handle, CURLOPT_POST, 1);
        curl_setopt($handle, CURLOPT_POSTFIELDS, json_encode($data));

        return $this->executeAndDecode($handle);
    }

    /**
     * Convenience function for Puts
     *
     * @param  string $path
     * @param  mixed  $data
     * @return mixed
     */
    public function put($path, $data)
    {
        $full_url = $this->root_url . $path;
        $handle   = $this->buildHandle($full_url);

        curl_setopt($handle, CURLOPT_PUT, 1);
        curl_setopt($handle, CURLOPT_POSTFIELDS, json_encode($data));

        return $this->executeAndDecode($handle);
    }

    /**
     * create query URL based on the root URL, path, and parameters
     *
     * @param  string $path
     * @param  array  $params
     * @return string
     */
    private function buildQueryUrl($path, array $params = array())
    {
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
    private function buildHandle($full_url)
    {
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
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, 1);

        // Set up request, and auth, if configured
        if ($this->user_name != "" && $this->password != "") {
            curl_setopt($handle, CURLOPT_USERPWD, $this->user_name . ":" . $this->password);
        }

        return $handle;
    }

    /**
     * Execute a CURL resource and json_decode the result
     *
     * @param  resource $handle
     * @return mixed
     * @throws RuntimeException if the request was not successful
     */
    private function executeAndDecode($handle)
    {
        $response = curl_exec($handle);
        curl_close($handle);

        return json_decode($response, true);
    }
}
