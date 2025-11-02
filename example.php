<?php

/**
 * Example usage of Dilovod PHP SDK
 * 
 * This file demonstrates how to use the DilovodApiClient to interact
 * with the Dilovod API.
 * 
 * Before running this example, make sure to:
 * 1. Install dependencies: composer install
 * 2. Replace 'YOUR_API_KEY' with your actual API key
 */

require_once __DIR__ . '/vendor/autoload.php';

use Dilovod\Sdk\DilovodApiClient;
use Dilovod\Sdk\Exception\DilovodApiException;

// ========================================
// Ініціалізація клієнта
// ========================================

try {
    // Замініть 'YOUR_API_KEY' на ваш реальний API ключ
    $client = new DilovodApiClient('YOUR_API_KEY');
    
    echo "=== Dilovod PHP SDK Examples ===\n\n";
    
    // ========================================
    // Приклад 1: Отримання списку товарів
    // ========================================
    echo "1. Отримання списку товарів:\n";
    echo "--------------------------------\n";
    
    try {
        $products = $client->getObjects(
            'catalogs.goods',  // Тип об'єкта
            [],                    // Фільтр (порожній = всі товари)
            ['name', 'price'],    // Поля для повернення
            'name ASC',           // Сортування за назвою
            10                     // Ліміт: 10 товарів
        );
        
        echo "Знайдено товарів: " . count($products) . "\n";
        if (!empty($products)) {
            echo "Перший товар: " . json_encode($products[0], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . "\n";
        }
        
    } catch (DilovodApiException $e) {
        echo "Помилка при отриманні товарів: " . $e->getMessage() . "\n";
        if ($e->hasApiErrorCode()) {
            echo "Код помилки API: " . $e->getApiErrorCode() . "\n";
        }
    }
    
    echo "\n";
    
    // ========================================
    // Приклад 2: Отримання одного об'єкта за ID
    // ========================================
    echo "2. Отримання одного об'єкта за ID:\n";
    echo "--------------------------------\n";
    
    try {
        // Приклад: отримання товару за ID (замініть на реальний ID)
        $product = $client->getObject('SOME_PRODUCT_ID');
        echo "Об'єкт отримано:\n";
        echo json_encode($product, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . "\n";
        
    } catch (DilovodApiException $e) {
        echo "Помилка при отриманні об'єкта: " . $e->getMessage() . "\n";
    }
    
    echo "\n";
    
    // ========================================
    // Приклад 3: Створення партнера (контрагента)
    // ========================================
    echo "3. Створення партнера:\n";
    echo "--------------------------------\n";
    
    try {
        $newPartner = [
            'type' => 'catalogs.persons',
            'name' => 'Новий Клієнт 007',
            'is_buyer' => true,
            // Додайте інші обов'язкові поля згідно з документацією API
        ];
        
        $result = $client->saveObject($newPartner);
        
        echo "Партнер створено успішно!\n";
        echo "Результат: " . json_encode($result, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . "\n";
        
        if (isset($result['id'])) {
            echo "ID нового партнера: " . $result['id'] . "\n";
        }
        
    } catch (DilovodApiException $e) {
        echo "Помилка при створенні партнера: " . $e->getMessage() . "\n";
        if ($e->hasApiErrorCode()) {
            echo "Код помилки API: " . $e->getApiErrorCode() . "\n";
        }
    }
    
    echo "\n";
    
    // ========================================
    // Приклад 4: Отримання документів (наприклад, замовлень)
    // ========================================
    echo "4. Отримання списку документів:\n";
    echo "--------------------------------\n";
    
    try {
        $orders = $client->getObjects(
            'documents.sale_order', // Тип документів: замовлення покупця
            [],                     // Фільтр
            [],                     // Всі поля
            'date DESC',           // Сортування за датою (нові спочатку)
            5                       // Останні 5 замовлень
        );
        
        echo "Знайдено документів: " . count($orders) . "\n";
        
    } catch (DilovodApiException $e) {
        echo "Помилка при отриманні документів: " . $e->getMessage() . "\n";
    }
    
    echo "\n";
    
    // ========================================
    // Приклад 5: Створення замовлення покупця
    // ========================================
    echo "5. Створення замовлення покупця:\n";
    echo "--------------------------------\n";
    
    try {
        $orderData = [
            // Структура замовлення (замініть на реальні дані)
            'partner' => 'PARTNER_ID',
            'products' => [
                [
                    'product' => 'PRODUCT_ID',
                    'quantity' => 5,
                    'price' => 100.00
                ]
            ]
            // Додайте інші обов'язкові поля згідно з документацією API
        ];
        
        $result = $client->saleOrderCreate($orderData);
        
        echo "Замовлення створено успішно!\n";
        echo "Результат: " . json_encode($result, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . "\n";
        
    } catch (DilovodApiException $e) {
        echo "Помилка при створенні замовлення: " . $e->getMessage() . "\n";
    }
    
    echo "\n";
    
    echo "=== Приклади завершено ===\n";
    
} catch (\Exception $e) {
    echo "Критична помилка: " . $e->getMessage() . "\n";
}

