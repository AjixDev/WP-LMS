<?php
class CodeSandboxAPI
{
    private $api_key;

    public function __construct($api_key)
    {
        $this->api_key = $api_key;
    }

    public function get_sandboxes()
    {
        $url = 'https://codesandbox.io/api/v1/sandboxes'; // Replace with actual endpoint
        $headers = [
        'Authorization' => 'Bearer ' . $this->api_key,
        ];

        $response = wp_remote_get(
            $url, [
            'headers' => $headers,
            ]
        );

        if (is_wp_error($response)) {
            return false;
        }

        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        return $data;
    }

    // Other API methods as needed
}
