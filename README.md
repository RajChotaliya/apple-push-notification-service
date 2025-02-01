Here’s the text you can copy for your README file:

---

# Apple Push Notification Service (APNs)

A PHP library to send push notifications to Apple devices using the APNs service.

## Installation

Install the library using Composer:

```bash
composer require rajchotaliya/apple-push-notification-service
```

---

## Usage

### Laravel
1. **Configuration:**
   - After installing the package, publish the configuration file using the following Artisan command:
     ```bash
     php artisan vendor:publish --provider="RajChotaliya\ApplePushNotificationService\ApplePushNotificationServiceProvider"
     ```
   - This will publish the `apns.php` file into your Laravel project's `config` directory. You can then customize it to fit your needs.

   Example configuration in `config/apns.php`:
   ```php
   return  [
        'bundle_id' => env('APNS_BUNDLE_ID', ''),
        'key_id' => env('APNS_KEY_ID', ''),
        'team_id' => env('APNS_TEAM_ID', ''),
        'private_key_path' => env('APNS_PRIVATE_KEY_PATH', storage_path('AuthKey.p8')),
    ];
   ```

2. **Using the Library:**
   You can use the library in your Laravel application as follows:
   ```php
   use RajChotaliya\ApplePushNotificationService\ApplePushNotificationService;

   $deviceToken = 'your_device_token';
   $title = 'Hello from APNs!';
   $body = 'This is a test push notification.';

   $apns = new APNs($deviceToken, $title, $body);
   $response = $apns->send();
   ```

---

### Core PHP
1. **Configuration:**
   - Manually create a configuration file at `config/apns.php` in your project root directory:
     ```php
     <?php

     return [
         'bundle_id' => 'com.example.app',
         'key_id' => 'ABC123DEF456',
         'team_id' => 'XYZ789',
         'private_key_path' => __DIR__ . '/AuthKey.p8',
     ];
     ```

2. **Using the Library:**
   You can use the library in a Core PHP project as follows:
   ```php
   require 'vendor/autoload.php';

   use RajChotaliya\ApplePushNotificationService\ApplePushNotificationService;

   $deviceToken = 'your_device_token';
   $title = 'Hello from APNs!';
   $body = 'This is a test push notification.';

   $apns = new APNs($deviceToken, $title, $body);
   $response = $apns->send();
   ```

---

## Features
- **Laravel Support:** Includes a service provider and configuration publishing for seamless integration.
- **Core PHP Compatibility:** Easily load configuration and use the library without a framework.
- **Cross-Platform:** Compatible with all PHP versions >= 8.0.

---

## Support

Feel free to contribute or raise issues in the [GitHub repository](https://github.com/RajChotaliya/apple-push-notification-service).

### Show Your Appreciation

You can show your appreciation by buying me a tea through the following link:

[Buy me a tea](https://drive.google.com/file/d/1-i7MNcvvixDv5C2qrnQJ0uFWdUbUvxI3/view?usp=sharing)

---

Let me know if you need further adjustments!