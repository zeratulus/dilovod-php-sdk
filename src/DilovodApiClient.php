<?php

namespace Dilovod\Sdk;

use Dilovod\Sdk\Exception\DilovodApiException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

/**
 * Main client class for interacting with Dilovod API.
 * 
 * This class provides methods to interact with the Dilovod API, including
 * retrieving objects, creating/updating objects, and creating sale orders.
 * 
 * @package Dilovod\Sdk
 */
class DilovodApiClient
{
    /**
     * The base URL for the Dilovod API.
     */
    private const BASE_URL = 'https://api.dilovod.ua';

    /**
     * The API key (token) for authentication.
     * 
     * @var string
     */
    private string $apiKey;

    /**
     * The HTTP client for making requests.
     * 
     * @var Client
     */
    private Client $httpClient;

    /**
     * Constructor.
     * 
     * Initializes the API client with the provided API key.
     * 
     * @param string $apiKey The API token (generated in Dilovod system)
     */
    public function __construct(string $apiKey)
    {
        $this->apiKey = $apiKey;
        $this->httpClient = new Client([
            'base_uri' => self::BASE_URL,
            'timeout' => 30.0,
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
        ]);
    }

    /**
     * Private method to handle all API requests.
     * 
     * This method sends POST requests to the Dilovod API with the specified action
     * and parameters. It automatically includes the API key in the request body.
     * 
     * @param string $action The API action to execute (e.g., 'getObject', 'saveObject')
     * @param array $params The parameters for the API request
     * @return array The API response data
     * @throws DilovodApiException If the API request fails or returns an error
     */
    private function request(string $action, array $params): array
    {
        try {
            // Prepare the request body
            $body = [
                'action' => $action,
                'params' => $params,
                'key' => $this->apiKey,
            ];

            // Send POST request
            $response = $this->httpClient->post('', [
                'json' => $body,
            ]);

            // Get response body and decode JSON
            $responseBody = (string) $response->getBody();
            $data = json_decode($responseBody, true);

            // Check if JSON decoding was successful
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new DilovodApiException(
                    sprintf('Failed to decode API response: %s', json_last_error_msg()),
                    0,
                    null,
                    null
                );
            }

            // Check for API-level errors
            if (isset($data['error'])) {
                $errorMessage = $data['error']['message'] ?? 'Unknown API error';
                $errorCode = $data['error']['code'] ?? null;
                throw new DilovodApiException(
                    $errorMessage,
                    0,
                    null,
                    $errorCode
                );
            }

            // Check if result is present
            if (!isset($data['result'])) {
                throw new DilovodApiException(
                    'API response does not contain expected result field',
                    0,
                    null,
                    null
                );
            }

            return $data['result'];

        } catch (GuzzleException $e) {
            throw new DilovodApiException(
                sprintf('HTTP error: %s', $e->getMessage()),
                $e->getCode(),
                $e,
                null
            );
        }
    }

    /**
     * Get a single object by its ID.
     * 
     * Retrieves a document, catalog entry, or other object by its unique identifier.
     * 
     * @param string $id The ID of the object to retrieve
     * @return array The object data
     * @throws DilovodApiException If the request fails or object is not found
     */
    public function getObject(string $id): array
    {
        return $this->request('getObject', ['id' => $id]);
    }

    /**
     * Save (create or update) an object.
     * 
     * Creates a new object or updates an existing one in the Dilovod system.
     * The objectData array should contain all required fields for the object type.
     * 
     * @param array $objectData The object data to save (must include 'type' field)
     * @return array The save result (typically contains 'id' of created/updated object)
     * @throws DilovodApiException If the request fails or validation error occurs
     */
    public function saveObject(array $objectData): array
    {
        return $this->request('saveObject', $objectData);
    }

    /**
     * Create a sale order (order from buyer).
     * 
     * This is a simplified method specifically for creating sale orders (замовлення покупця).
     * 
     * @param array $orderData The order data (should contain required order fields)
     * @return array The creation result (typically contains the order ID)
     * @throws DilovodApiException If the request fails or validation error occurs
     */
    public function saleOrderCreate(array $orderData): array
    {
        return $this->request('saleOrderCreate', $orderData);
    }

    /**
     * Get multiple objects with filtering, field selection, sorting, and limiting.
     * 
     * Retrieves a list of objects based on the specified criteria.
     * 
     * @param string $type The object type (e.g., 'catalogs.products', 'documents.sale_order')
     * @param array $filter Associative array for filtering (e.g., ['name' => 'Value'])
     * @param array $fields Array of field names to return (empty array returns all fields)
     * @param string $orderby Sort field and direction (e.g., 'name ASC', 'date DESC')
     * @param int $limit Maximum number of records to return (0 means no limit)
     * @return array Array of objects matching the criteria
     * @throws DilovodApiException If the request fails or type is invalid
     */
    public function getObjects(string $type, array $filter = [], array $fields = [], string $orderby = '', int $limit = 0): array
    {
        // Build params array
        $params = [
            'type' => $type,
            'filter' => $filter,
            'fields' => $fields,
            'orderby' => $orderby,
            'limit' => $limit,
        ];

        // Remove empty values to keep request clean
        $params = array_filter($params, function ($value) {
            if (is_array($value)) {
                return !empty($value);
            }
            return $value !== '' && $value !== 0;
        });

        return $this->request('getObjects', $params);
    }
}

