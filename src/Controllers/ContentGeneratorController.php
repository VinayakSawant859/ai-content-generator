<?php

namespace App\Controllers;

use App\Core\Response;
use App\Core\Validator;
use App\Services\GeminiService;

class ContentGeneratorController
{
    private $config;
    private $geminiService;
    
    public function __construct($config)
    {
        $this->config = $config;
        $this->geminiService = new GeminiService($config);
    }
    
    public function generateContent()
    {
        // Read the request body
        $rawInput = file_get_contents('php://input');
        $input = json_decode($rawInput, true);
        
        // Check if JSON is valid
        if (json_last_error() !== JSON_ERROR_NONE) {
            Response::error('Invalid JSON input', 400);
            return;
        }
        
        // Validate the input fields
        $validator = new Validator();
        $validator->required($input, ['business_name', 'category', 'city', 'services'])
                 ->minLength($input, 'business_name', 2)
                 ->maxLength($input, 'business_name', 100)
                 ->minLength($input, 'category', 2)
                 ->maxLength($input, 'category', 50)
                 ->minLength($input, 'city', 2)
                 ->maxLength($input, 'city', 50)
                 ->minLength($input, 'services', 5)
                 ->maxLength($input, 'services', 500);
        
        if (!$validator->passes()) {
            Response::validationError($validator->getErrors());
            return;
        }
        
        // Clean up the input to prevent XSS
        $businessData = array(
            'business_name' => htmlspecialchars(trim($input['business_name']), ENT_QUOTES, 'UTF-8'),
            'category' => htmlspecialchars(trim($input['category']), ENT_QUOTES, 'UTF-8'),
            'city' => htmlspecialchars(trim($input['city']), ENT_QUOTES, 'UTF-8'),
            'services' => htmlspecialchars(trim($input['services']), ENT_QUOTES, 'UTF-8')
        );
        
        try {
            // Call the AI service to generate content
            $generatedContent = $this->geminiService->generateWebsiteContent($businessData);
            
            // Prepare the response
            $response = array(
                'input' => $businessData,
                'generated_content' => $generatedContent,
                'metadata' => array(
                    'generated_at' => date('Y-m-d H:i:s'),
                    'ai_model' => 'Gemini Flash',
                    'version' => $this->config['api_version']
                )
            );
            
            Response::success($response, 200);
            
        } catch (\Exception $e) {
            error_log('Content Generation Error: ' . $e->getMessage());
            
            Response::error(
                'Failed to generate content. Please try again later.',
                500,
                array('technical_details' => $e->getMessage())
            );
        }
    }
}
