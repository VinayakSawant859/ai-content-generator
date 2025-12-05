<?php

namespace App\Services;

class GeminiService
{
    private $apiKey;
    private $apiUrl;
    
    public function __construct($config)
    {
        $this->apiKey = $config['gemini_api_key'];
        $this->apiUrl = $config['gemini_api_url'];
    }
    
    public function generateWebsiteContent($businessData)
    {
        $prompt = $this->buildPrompt($businessData);
        $response = $this->makeApiRequest($prompt);
        return $this->parseResponse($response);
    }
    
    private function buildPrompt($data)
    {
        $prompt = "Generate website content for this business:\n\n";
        $prompt .= "Business Name: " . $data['business_name'] . "\n";
        $prompt .= "Category: " . $data['category'] . "\n";
        $prompt .= "City: " . $data['city'] . "\n";
        $prompt .= "Services: " . $data['services'] . "\n\n";
        $prompt .= "Please provide:\n";
        $prompt .= "1. A catchy headline (max 60 characters)\n";
        $prompt .= "2. A description (100-150 words)\n";
        $prompt .= "3. 5-8 SEO keywords\n\n";
        $prompt .= "Return as JSON:\n";
        $prompt .= '{"headline": "...", "description": "...", "keywords": ["..."]}';
        
        return $prompt;
    }
    
    private function makeApiRequest($prompt)
    {
        $data = array(
            'contents' => array(
                array(
                    'parts' => array(
                        array('text' => $prompt)
                    )
                )
            ),
            'generationConfig' => array(
                'temperature' => 0.7,
                'maxOutputTokens' => 800,
                'topP' => 0.8,
                'topK' => 40
            )
        );
        
        $ch = curl_init($this->apiUrl . '?key=' . $this->apiKey);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        if (curl_errno($ch)) {
            $error = curl_error($ch);
            curl_close($ch);
            throw new \Exception("cURL Error: " . $error);
        }
        
        curl_close($ch);
        
        if ($httpCode !== 200) {
            throw new \Exception("Gemini API Error: HTTP " . $httpCode . " - " . $response);
        }
        
        $responseData = json_decode($response, true);
        
        if (!isset($responseData['candidates'][0]['content']['parts'][0]['text'])) {
            throw new \Exception("Invalid response from Gemini API");
        }
        
        return $responseData['candidates'][0]['content']['parts'][0]['text'];
    }
    
    private function parseResponse($aiResponse)
    {
        $result = array(
            'headline' => '',
            'description' => '',
            'keywords' => array()
        );
        
        // Try to extract JSON from the response
        // Sometimes AI wraps it in markdown code blocks
        $cleaned = str_replace('```json', '', $aiResponse);
        $cleaned = str_replace('```', '', $cleaned);
        $cleaned = trim($cleaned);
        
        $parsed = json_decode($cleaned, true);
        
        if ($parsed && isset($parsed['headline'])) {
            $result['headline'] = $parsed['headline'];
            $result['description'] = $parsed['description'];
            $result['keywords'] = $parsed['keywords'];
        } else {
            // Fallback parsing if JSON didn't work
            $lines = explode("\n", $aiResponse);
            $result['headline'] = $this->extractHeadline($lines);
            $result['description'] = $this->extractDescription($aiResponse);
            $result['keywords'] = $this->extractKeywords($aiResponse);
        }
        
        return $result;
    }
    
    private function extractHeadline($lines)
    {
        foreach ($lines as $line) {
            $line = trim($line);
            if (!empty($line) && strlen($line) > 10 && strlen($line) < 100) {
                return $line;
            }
        }
        return "Professional Business Services";
    }
    
    private function extractDescription($text)
    {
        $text = preg_replace('/^.*?headline.*?\n/i', '', $text);
        $text = preg_replace('/keywords?:.*$/i', '', $text);
        $text = trim($text);
        
        if (strlen($text) > 300) {
            $text = substr($text, 0, 297) . '...';
        }
        
        return !empty($text) ? $text : "Quality services delivered with excellence.";
    }
    
    private function extractKeywords($text)
    {
        if (preg_match('/keywords?:\s*\[(.*?)\]/i', $text, $matches)) {
            $keywordStr = $matches[1];
            $keywords = explode(',', $keywordStr);
            return array_map('trim', $keywords);
        }
        
        return array('business', 'services', 'professional', 'quality', 'local');
    }
}
