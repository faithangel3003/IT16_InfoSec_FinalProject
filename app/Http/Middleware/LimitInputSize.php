<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LimitInputSize
{
    /**
     * Maximum allowed sizes for different field types (in bytes/characters)
     */
    protected array $limits = [
        'string' => 1000,           // Default string limit
        'text' => 10000,            // Textarea/description fields
        'email' => 255,             // Email addresses
        'name' => 255,              // Name fields
        'phone' => 20,              // Phone numbers
        'ip' => 45,                 // IP addresses (IPv6 max)
        'password' => 128,          // Password fields
        'url' => 2048,              // URLs
        'json' => 65535,            // JSON data
        'file_size' => 10485760,    // 10MB for files
    ];

    /**
     * Field patterns to identify field types
     */
    protected array $fieldPatterns = [
        'email' => ['email', 'e-mail'],
        'name' => ['name', 'title', 'label'],
        'phone' => ['phone', 'telephone', 'mobile', 'fax'],
        'ip' => ['ip_address', 'ip', 'ipaddress'],
        'password' => ['password', 'password_confirmation'],
        'url' => ['url', 'link', 'website', 'href'],
        'text' => ['description', 'notes', 'content', 'body', 'message', 'comment', 'remarks', 'resolution_notes'],
    ];

    /**
     * Maximum total request size in bytes (5MB)
     */
    protected int $maxTotalRequestSize = 5242880;

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check total request size
        $contentLength = $request->header('Content-Length', 0);
        if ($contentLength > $this->maxTotalRequestSize) {
            return $this->rejectRequest('Total request size exceeds the maximum allowed limit of 5MB.');
        }

        // Validate input field sizes
        $errors = $this->validateInputSizes($request->all());
        
        if (!empty($errors)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Input validation failed',
                    'errors' => $errors
                ], 422);
            }
            
            return back()->withErrors($errors)->withInput();
        }

        return $next($request);
    }

    /**
     * Validate all input field sizes
     */
    protected function validateInputSizes(array $inputs, string $prefix = ''): array
    {
        $errors = [];

        foreach ($inputs as $key => $value) {
            $fieldName = $prefix ? "{$prefix}.{$key}" : $key;

            if (is_array($value)) {
                // Check array size (max 100 items)
                if (count($value) > 100) {
                    $errors[$fieldName][] = "The {$fieldName} field cannot contain more than 100 items.";
                    continue;
                }
                
                // Recursively validate nested arrays
                $nestedErrors = $this->validateInputSizes($value, $fieldName);
                $errors = array_merge($errors, $nestedErrors);
            } elseif (is_string($value)) {
                $limit = $this->getFieldLimit($key);
                $length = strlen($value);

                if ($length > $limit) {
                    $errors[$fieldName][] = "The {$fieldName} field cannot exceed {$limit} characters (current: {$length}).";
                }

                // Additional security checks
                if ($this->containsSuspiciousPatterns($value)) {
                    $errors[$fieldName][] = "The {$fieldName} field contains potentially harmful content.";
                }
            }
        }

        return $errors;
    }

    /**
     * Get the appropriate size limit for a field
     */
    protected function getFieldLimit(string $fieldName): int
    {
        $fieldLower = strtolower($fieldName);

        foreach ($this->fieldPatterns as $type => $patterns) {
            foreach ($patterns as $pattern) {
                if (str_contains($fieldLower, $pattern)) {
                    return $this->limits[$type];
                }
            }
        }

        return $this->limits['string'];
    }

    /**
     * Check for suspicious patterns that could indicate attacks
     */
    protected function containsSuspiciousPatterns(string $value): bool
    {
        // Skip empty or very short strings
        if (strlen($value) < 10) {
            return false;
        }

        $suspiciousPatterns = [
            // SQL Injection patterns
            '/(\bunion\b.*\bselect\b|\bselect\b.*\bfrom\b.*\bwhere\b)/i',
            '/(\bdrop\b.*\btable\b|\btruncate\b.*\btable\b)/i',
            '/(\binsert\b.*\binto\b.*\bvalues\b)/i',
            '/(\bdelete\b.*\bfrom\b.*\bwhere\b)/i',
            
            // Command injection patterns
            '/(;|\||`)\s*(cat|ls|dir|rm|del|wget|curl|bash|sh|cmd|powershell)\b/i',
            '/\$\((.*)\)/',  // Command substitution
            
            // Path traversal
            '/\.\.\/\.\.\/\.\.\//',  // Multiple directory traversal
            
            // Null byte injection
            '/\x00/',
            
            // Excessively repeated characters (potential DoS)
            '/(.)\1{100,}/',  // Same character repeated 100+ times
        ];

        foreach ($suspiciousPatterns as $pattern) {
            if (preg_match($pattern, $value)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Return a rejection response
     */
    protected function rejectRequest(string $message): Response
    {
        return response()->json([
            'message' => 'Request rejected',
            'error' => $message
        ], 413);
    }
}
