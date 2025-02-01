<?php

return [
    'bundle_id' => env('APNS_BUNDLE_ID', ''),
    'key_id' => env('APNS_KEY_ID', ''),
    'team_id' => env('APNS_TEAM_ID', ''),
    'private_key_path' => env('APNS_PRIVATE_KEY_PATH', storage_path('AuthKey.p8')),
];
