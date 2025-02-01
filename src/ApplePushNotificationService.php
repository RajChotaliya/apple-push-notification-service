<?php

namespace RajChotaliya\ApplePushNotificationService;

use Exception;

class ApplePushNotificationService
{
    protected $deviceToken;
    protected $bundleId;
    protected $keyId;
    protected $teamId;
    protected $privateKeyPath;
    protected $title;
    protected $body;

    // List of required config keys
    protected $requiredConfigKeys = [
        'bundle_id' => 'The "bundle_id" configuration is required.',
        'key_id' => 'The "key_id" configuration is required.',
        'team_id' => 'The "team_id" configuration is required.',
        'private_key_path' => 'The "private_key_path" configuration is required.',
    ];

    public function __construct($deviceToken, $title = 'Hello from Raj!', $body = 'This is a test push notification.')
    {
        // Determine if we are in a Laravel environment or Core PHP
        $config = $this->loadConfig();

        // Validate the loaded configuration
        $this->validateConfig();

        $this->deviceToken = $deviceToken;
        $this->bundleId = $config['bundle_id'];
        $this->keyId = $config['key_id'];
        $this->teamId = $config['team_id'];
        $this->privateKeyPath = $config['private_key_path'];
        $this->title = $title;
        $this->body = $body;
    }

    /**
     * Load the configuration depending on the environment (Laravel or Core PHP).
     *
     * @return array
     */
    private function loadConfig()
    {
        if (function_exists('config')) {
            // In Laravel, fetch the configuration using the `config` function
            return config('apns');
        }

        // In Core PHP, manually load the configuration file
        // In Core PHP, manually load the configuration file from the project root directory
        $configPath = __DIR__ . '/../../config/apns.php'; // Move up 4 levels to get to the project root

        if (!file_exists($configPath)) {
            throw new Exception("Configuration file not found at {$configPath}");
        }

        return require $configPath;
    }

    /**
     * Validate the configuration to ensure all required keys are present
     *
     * @throws Exception if any config key is missing
     */
    protected function validateConfig()
    {
        foreach ($this->requiredConfigKeys as $key => $errorMessage) {
            if (empty($this->config[$key])) {
                throw new Exception($errorMessage);
            }
        }
    }

    /**
     * Send Apple Push Notification
     *
     * @return array Response message and success status
     */
    public function sendNotification()
    {
        try {
            // Generate the JWT token for APNs
            $jwt = $this->fetchJWT();

            // Prepare the payload for the push notification
            $payload = [
                'aps' => [
                    'alert' => [
                        'title' => $this->title,
                        'body' => $this->body,
                    ],
                    'sound' => 'default',
                ],
            ];

            // APNs endpoint URL (sandbox for testing, production for live)
            $url = "https://api.sandbox.push.apple.com/3/device/{$this->deviceToken}";

            // Initialize cURL session
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_2_0);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                "Authorization: Bearer {$jwt}",
                "apns-topic: {$this->bundleId}",
                "Content-Type: application/json",
            ]);

            // Execute cURL request and capture the response
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            // Check if the response status is 200 (success)
            $success = $httpCode === 200;

            return [
                'success' => $success,
                'message' => $success
                    ? 'Notification sent successfully'
                    : 'Failed to send notification',
                'response' => $response,
            ];
        } catch (Exception $e) {
            // Handle any exceptions and return the error message
            return [
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Fetch a JWT (JSON Web Token) for APNs authentication
     *
     * @return string The generated JWT token
     */
    public function fetchJWT()
    {
        return $this->generateJWT($this->keyId, $this->teamId, $this->privateKeyPath);
    }

    /**
     * Generate a JWT (JSON Web Token) for APNs authentication
     *
     * @param string $keyId The Key ID from Apple Developer
     * @param string $teamId The Team ID from Apple Developer
     * @param string $privateKeyPath Path to the .p8 private key file
     * @return string The generated JWT token
     */
    private function generateJWT(string $keyId, string $teamId, string $privateKeyPath)
    {
        // Read the private key content from the file
        $privateKey = file_get_contents($privateKeyPath);

        // JWT Header
        $header = [
            'alg' => 'ES256',
            'kid' => $keyId,
        ];

        // JWT Payload
        $payload = [
            'iss' => $teamId,
            'iat' => time(),
        ];

        // Base64 URL-safe encode header and payload
        $headerEncoded = rtrim(strtr(base64_encode(json_encode($header)), '+/', '-_'), '=');
        $payloadEncoded = rtrim(strtr(base64_encode(json_encode($payload)), '+/', '-_'), '=');

        // Generate the signature
        $dataToSign = $headerEncoded . '.' . $payloadEncoded;
        $signature = '';
        openssl_sign($dataToSign, $signature, $privateKey, 'sha256');

        // Base64 URL-safe encode the signature
        $signatureEncoded = rtrim(strtr(base64_encode($signature), '+/', '-_'), '=');

        // Return the complete JWT token
        return $headerEncoded . '.' . $payloadEncoded . '.' . $signatureEncoded;
    }
}

