<?php

return array(
    'gemini_api_key' => 'YOUR_GEMINI_API_KEY_HERE',
    'gemini_api_url' => 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash-lite:generateContent',
    
    'api_version' => 'v1',
    'max_request_size' => 1024 * 1024,
    
    'cors_enabled' => true,
    'allowed_origins' => array('*'),
    
    'rate_limit_enabled' => false,
    'max_requests_per_minute' => 60,
);