# Data Manager: Fluent Chain Access
If you prefer to access data as though they were nested PHP objects, `use Michaels\Manager\ChainsNestedItemsTrait`.

This allows you to
```php
$manager = new Manager([
    'some' => [
        'starting' => [
            'data' => 'here'
        ]
    ]
]);

$manager->some()->starting()->data; // 'here'
$manager->some()->item = 'item'; // sets some.item = 'item'
$manager->some()->item()->drop(); // deletes some.item
```

Note that levels are called as a method with no params. The data is then called, updated, or set as a property.