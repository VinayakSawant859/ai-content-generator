<?php

namespace App\Core;

class Response
{
    public static function success($data, $statusCode = 200)
    {
        http_response_code($statusCode);
        echo json_encode(array(
            'status' => 'success',
            'data' => $data
        ), JSON_PRETTY_PRINT);
        exit;
    }
    
    public static function error($message, $statusCode = 400, $errors = array())
    {
        http_response_code($statusCode);
        $response = array(
            'status' => 'error',
            'message' => $message
        );
        
        if (!empty($errors)) {
            $response['errors'] = $errors;
        }
        
        echo json_encode($response, JSON_PRETTY_PRINT);
        exit;
    }
    
    public static function validationError($errors)
    {
        self::error('Validation failed', 422, $errors);
    }
}
