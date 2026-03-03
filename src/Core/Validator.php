<?php

namespace POS\Core;

class Validator
{
    /**
     * Validate data against rules
     * 
     * @param array $data
     * @param array $rules ['field' => ['required', 'min:3', 'max:50', 'numeric']]
     * @return array ['ok' => bool, 'errors' => ['field' => ['message...']]]
     */
    public static function validate(array $data, array $rules): array
    {
        $errors = [];
        
        foreach ($rules as $field => $fieldRules) {
            $value = $data[$field] ?? null;
            
            foreach ($fieldRules as $rule) {
                // Parse rule with parameter (e.g., 'min:3')
                $parts = explode(':', $rule, 2);
                $ruleName = $parts[0];
                $param = $parts[1] ?? null;
                
                switch ($ruleName) {
                    case 'required':
                        if ($value === null || $value === '') {
                            $errors[$field][] = ucfirst($field) . ' is required';
                        }
                        break;
                        
                    case 'min':
                        if (is_string($value) && strlen($value) < (int)$param) {
                            $errors[$field][] = ucfirst($field) . ' must be at least ' . $param . ' characters';
                        } elseif (is_numeric($value) && $value < (int)$param) {
                            $errors[$field][] = ucfirst($field) . ' must be at least ' . $param;
                        }
                        break;
                        
                    case 'max':
                        if (is_string($value) && strlen($value) > (int)$param) {
                            $errors[$field][] = ucfirst($field) . ' must not exceed ' . $param . ' characters';
                        } elseif (is_numeric($value) && $value > (int)$param) {
                            $errors[$field][] = ucfirst($field) . ' must not exceed ' . $param;
                        }
                        break;
                        
                    case 'numeric':
                        if (!is_numeric($value) && $value !== null && $value !== '') {
                            $errors[$field][] = ucfirst($field) . ' must be a number';
                        }
                        break;
                }
            }
        }
        
        return [
            'ok' => empty($errors),
            'errors' => $errors
        ];
    }
}