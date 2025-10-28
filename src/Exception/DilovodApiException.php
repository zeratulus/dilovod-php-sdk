<?php

namespace Dilovod\Sdk\Exception;

/**
 * Custom exception class for Dilovod API errors.
 * 
 * This exception is thrown when an API request fails due to HTTP errors,
 * API-level errors, or unexpected responses from the Dilovod API.
 * 
 * @package Dilovod\Sdk\Exception
 */
class DilovodApiException extends \Exception
{
    /**
     * Error code returned by the API.
     * 
     * @var string|null
     */
    private ?string $apiErrorCode;

    /**
     * Constructor.
     * 
     * @param string $message The error message
     * @param int $code The error code
     * @param \Throwable|null $previous The previous exception
     * @param string|null $apiErrorCode The API error code (if available)
     */
    public function __construct(string $message = "", int $code = 0, ?\Throwable $previous = null, ?string $apiErrorCode = null)
    {
        parent::__construct($message, $code, $previous);
        $this->apiErrorCode = $apiErrorCode;
    }

    /**
     * Get the API error code.
     * 
     * @return string|null The API error code or null if not available
     */
    public function getApiErrorCode(): ?string
    {
        return $this->apiErrorCode;
    }

    /**
     * Check if an API error code is available.
     * 
     * @return bool True if API error code is available, false otherwise
     */
    public function hasApiErrorCode(): bool
    {
        return $this->apiErrorCode !== null;
    }
}

