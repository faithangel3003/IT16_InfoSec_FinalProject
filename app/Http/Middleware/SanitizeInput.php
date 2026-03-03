<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SanitizeInput
{
    /**
     * Fields that should NOT be sanitized (passwords, tokens, etc.)
     */
    protected array $except = [
        'password',
        'password_confirmation',
        'current_password',
        'new_password',
        '_token',
        'verification_token',
    ];

    /**
     * Handle an incoming request.
     * Sanitizes all string inputs using htmlspecialchars to prevent XSS attacks.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $input = $request->all();
        
        $sanitized = $this->sanitizeArray($input);
        
        $request->merge($sanitized);
        
        return $next($request);
    }

    /**
     * Recursively sanitize an array of inputs
     */
    protected function sanitizeArray(array $input): array
    {
        foreach ($input as $key => $value) {
            // Skip fields that shouldn't be sanitized
            if (in_array($key, $this->except)) {
                continue;
            }

            if (is_array($value)) {
                $input[$key] = $this->sanitizeArray($value);
            } elseif (is_string($value)) {
                $input[$key] = $this->sanitizeString($value);
            }
        }
        
        return $input;
    }

    /**
     * Sanitize a single string value
     */
    protected function sanitizeString(string $value): string
    {
        // Convert special characters to HTML entities
        $sanitized = htmlspecialchars($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        
        // Remove null bytes
        $sanitized = str_replace("\0", '', $sanitized);
        
        // Trim whitespace
        $sanitized = trim($sanitized);
        
        return $sanitized;
    }
}
