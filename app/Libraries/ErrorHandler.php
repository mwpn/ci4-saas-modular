<?php

namespace App\Libraries;

class ErrorHandler
{
    /**
     * Handle API errors with proper response format
     */
    public static function handleApiError(\Throwable $e, string $message = 'An error occurred'): array
    {
        $errorCode = $e->getCode() ?: 500;
        $errorMessage = $message;
        
        // Log the error
        log_message('error', 'API Error: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
        
        // Return appropriate error response
        $response = [
            'success' => false,
            'message' => $errorMessage,
            'code' => $errorCode
        ];
        
        // Add debug info in development
        if (ENVIRONMENT === 'development') {
            $response['debug'] = [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ];
        }
        
        return $response;
    }
    
    /**
     * Handle validation errors
     */
    public static function handleValidationError(array $errors): array
    {
        return [
            'success' => false,
            'message' => 'Validation failed',
            'errors' => $errors,
            'code' => 400
        ];
    }
    
    /**
     * Handle authentication errors
     */
    public static function handleAuthError(string $message = 'Unauthorized'): array
    {
        return [
            'success' => false,
            'message' => $message,
            'code' => 401
        ];
    }
    
    /**
     * Handle not found errors
     */
    public static function handleNotFoundError(string $message = 'Resource not found'): array
    {
        return [
            'success' => false,
            'message' => $message,
            'code' => 404
        ];
    }
    
    /**
     * Handle database errors
     */
    public static function handleDatabaseError(\Throwable $e): array
    {
        log_message('error', 'Database Error: ' . $e->getMessage());
        
        return [
            'success' => false,
            'message' => 'Database operation failed',
            'code' => 500
        ];
    }
}
