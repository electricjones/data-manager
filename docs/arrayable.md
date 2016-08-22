# Data Manager: Arrayable Trait
If you include the `Michaels\Manager\ArrayableTrait`, then you can use Manager as an array:

```php
$manager = new Manager([
    'one' => 'a', 
    'two' => [
        'three' => 'b'
    ]
]);

$manager['two']['three']; // 'b'
$manager['four'] = 'c'
isset($manager['one']); // true
unset($manager['one']);
count($manager); // 2
json_encode($manager);

foreach ($manager as $key => $value) {
    //...
}
```

Note that this is considered `Traversable`, but will NOT pass an `is_array()` check.