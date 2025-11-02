# Dilovod PHP SDK

PHP SDK для інтеграції з API Dilovod (https://help.dilovod.ua/uk/article/api-dilovod-1gwt3m0/).

## Встановлення

Встановіть залежності через Composer:

```bash
composer install
```

## Використання

### Базове використання

```php
<?php
require_once 'vendor/autoload.php';

use Dilovod\Sdk\DilovodApiClient;
use Dilovod\Sdk\Exception\DilovodApiException;

try {
    // Ініціалізація клієнта
    $client = new DilovodApiClient('YOUR_API_KEY');
    
    // Отримання списку товарів
    $products = $client->getObjects('catalogs.goods', [], ['name', 'price'], '', 10);
    print_r($products);
    
} catch (DilovodApiException $e) {
    echo "Помилка API: " . $e->getMessage();
}
```

## Методи

### getObject(string $id)

Отримує один об'єкт за його ID.

```php
$product = $client->getObject('PRODUCT_ID');
```

### saveObject(array $objectData)

Створює або оновлює об'єкт.

```php
$newPartner = [
    'type' => 'catalogs.persons',
    'name' => 'Новий Клієнт',
    'is_buyer' => true
];
$result = $client->saveObject($newPartner);
echo "Створено партнера з ID: " . $result['id'];
```

### saleOrderCreate(array $orderData)

Створює замовлення покупця.

```php
$orderData = [
    'partner' => 'PARTNER_ID',
    'products' => [
        ['product' => 'PRODUCT_ID', 'quantity' => 5]
    ]
];
$result = $client->saleOrderCreate($orderData);
```

### getObjects(string $type, array $filter = [], array $fields = [], string $orderby = '', int $limit = 0)

Отримує список об'єктів з фільтрацією, сортуванням та обмеженням.

```php
// Отримати товари з фільтром
$products = $client->getObjects(
    'catalogs.goods',
    ['price' => ['>', 100]], // Фільтр
    ['name', 'price'],       // Поля
    'name ASC',              // Сортування
    10                       // Ліміт
);
```

## Обробка помилок

SDK використовує кастомний виняток `DilovodApiException` для обробки помилок API.

```php
try {
    $client->getObject('INVALID_ID');
} catch (DilovodApiException $e) {
    echo "Помилка: " . $e->getMessage();
    if ($e->hasApiErrorCode()) {
        echo "Код помилки: " . $e->getApiErrorCode();
    }
}
```

## Документація API

Детальна документація API доступна за адресою:
https://help.dilovod.ua/uk/article/api-dilovod-1gwt3m0/

## Ліцензія

MIT

## Автор

Ваше ім'я

