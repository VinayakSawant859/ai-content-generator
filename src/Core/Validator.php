<?php

namespace App\Core;

class Validator
{
    private $errors = array();
    
    public function required($data, $fields)
    {
        foreach ($fields as $field) {
            if (!isset($data[$field]) || empty(trim($data[$field]))) {
                $this->errors[$field] = ucfirst(str_replace('_', ' ', $field)) . ' is required';
            }
        }
        return $this;
    }
    
    public function minLength($data, $field, $min)
    {
        if (isset($data[$field]) && strlen(trim($data[$field])) < $min) {
            $this->errors[$field] = ucfirst(str_replace('_', ' ', $field)) . " must be at least " . $min . " characters";
        }
        return $this;
    }
    
    public function maxLength($data, $field, $max)
    {
        if (isset($data[$field]) && strlen(trim($data[$field])) > $max) {
            $this->errors[$field] = ucfirst(str_replace('_', ' ', $field)) . " must not exceed " . $max . " characters";
        }
        return $this;
    }
    
    public function passes()
    {
        return empty($this->errors);
    }
    
    public function getErrors()
    {
        return $this->errors;
    }
}
