# AI Website Content Generator API

A simple PHP backend API that generates website content using Google Gemini AI.

## Features

- Generate headlines, descriptions, and keywords for businesses
- RESTful API design with JSON responses  
- Input validation and error handling
- Google Gemini AI integration
- Clean MVC architecture

## Quick Start

1. **Configure API Key**
   ```bash
   cp config.example.php config.php
   # Add your Gemini API key to config.php
   ```

2. **Start Server**
   ```bash
   php -S localhost:8000 -t public
   ```

3. **Test API**
   ```bash
   curl -X POST http://localhost:8000/api/generate-content \
     -H "Content-Type: application/json" \
     -d '{
       "business_name": "Tech Solutions Inc",
       "category": "IT Services", 
       "city": "Mumbai",
       "services": "Web Development, Cloud Solutions"
     }'
   ```

## API Endpoints

### GET `/api`
Returns API information and available endpoints.

### POST `/api/generate-content`
Generates website content for a business.

**Required Fields:**
- `business_name` (string, 2-100 chars)
- `category` (string, 2-50 chars)
- `city` (string, 2-50 chars)
- `services` (string, 5-500 chars)

**Example Response:**
```json
{
  "status": "success",
  "data": {
    "generated_content": {
      "headline": "Transform Your Business with Expert IT Solutions",
      "description": "Tech Solutions Inc delivers cutting-edge IT services...",
      "keywords": ["IT services Mumbai", "web development", "cloud solutions"]
    }
  }
}
```

## Project Structure

```
├── public/          # Web server root
│   └── index.php    # API entry point
├── src/
│   ├── Controllers/ # Request handlers
│   ├── Services/    # AI integration
│   └── Core/        # Utilities
└── config.php       # Configuration
```

## Requirements

- PHP 7.4+
- Google Gemini API key (free from [Google AI Studio](https://makersuite.google.com/app/apikey))
- cURL extension

## Tech Stack

- PHP (no frameworks - pure PHP)
- Google Gemini AI API
- PSR-4 autoloading
- RESTful API design

## License

MIT License