# Data Manager
Simple data manager for nested data, dot notation access, extendability, and container interoperability.

**See Also the Auto Generated [API Documentation](api/index.html)**

## Goals
  * Light weight with a fluent, simple, clear API
  * Manage any data type (closure, object, primitives, etc.)
  * Manage nested data via dot-notation (one.two.three)
  * Manage nested data via magic methods ($manager->one()->two()->three), if desired
  * Be composable - integrate into current containers via traits
  * Be extensible.
  * Allow for protected data (immutable)
  * Test coverage, PSR compliant, [container interoperability](https://github.com/container-interop/container-interop), and best practices

## Extras
On top of being a powerful data-manager, there are traits that add features. 
Please see [Composing](compose.md) for information about mixing and matching features or integrating
different traits into your project.

* [Arrayable](arrayable.md): Use Manager as an array.

* [Chain Access](chains.md): Access data through `$manager->method()->chaining()->for()->value`

* [IoC Container](ioc.md): Use Manager as a simple but powerful Dependency Injection Container. Includes:
    * Resolving dependencies from classnames, closures, eager loading, and more.
    * Creating singletons.
    * Configuring dependencies for dependencies.
    * Fallbacks, preparing objects, and more.
    * Use Manager as a configuration bank, complete with defaults.
    * Load configuration files (php, yaml, json, xml, and custom)

* [Collections](collections.md): Adds extra array-helper methods (based on [Arrayzy](https://github.com/bocharsky-bw/Arrayzy))  

* [Load From Files](load-files.md): 
Load from various types of files. Json, Php, and Yaml supported by default.
Add your own Custom Decoders easily.
Also allows for namespacing items under a safe file name. Great for a configuration bank.


## Install
Via Composer
``` bash
$ composer require michaels/data-manager
```

## Getting Started
Manager does exactly what you would expect: it *manages* complex items such as config data, arrays, and closures.
The best way to get started is simply instantiate `Michaels\Manager\Manager`

```php
$manager = new Michaels\Manager\Manager([
    'some' => [
        'starting' => [
            'data' => 'here (optional)'
        ]
    ]
]);
// Note, you may initialize Manager with an array or any instance of Traversable (like Manager itself)

/* Basic Usage. All works with dot notation as well */
$manager->add('name', 'value');
$manager->add('some.nested.data', 3); // Use dot notation for namespacing or nesting
$manager->get('name'); // 'value'
$manager->get('doesntexist', 'fallback'); // 'fallback'
$manager->get('doesntexist') // throws an ItemNotFoundException with no fallback
$manager->getIfHas('doesntexist') // returns a NoItemFoundMessage instead of a script-stopping exception
$manager->getAll(); // returns array of all items
$manager->all(); // returns array of all items
$manager->exists('name'); // true
$manager->exists('some.starting.data'); // true
$manager->exists('nope'); // false
$manager->has('something'); // alias of exist
$manager->set('name', 'new-value'); // updates item
$manager->remove('some.starting.data');
$manager->isEmpty(); // true or false
$manager->toJson(); // returns json of all items
echo $manager; // returns json string of all items
$manager->reset($array); // rebuild with new items
$manager->clear(); // empty the manager
```

### Protecting Data
You can also guard any item or nest from being changed. Simply,
```php
$manager->protect('some.data'); //now some.data and everything under it cannot be altered
$manager->set('some.data.here', 'new-value'); // throws an exception
```

### Merging Defaults Into Current Dataset
When using Manager to store configuration data, it is important to be able to set defaults.
You can merge an array of defaults into manager via `loadDefaults(array $defaults)`

Imagine your configuration starts like
```php
$manager = new Manager([
    'name' => 'My Awesome App',
    'site' => [
        'url' => 'https://youwishyouwerethiscool.com/',
        'protocol' => 'https',
    ]
]);
```

But your app needs `site.assets` for the assets directory. Simply
```php
$manager->loadDefaults([
    'site' => [
        'url' => 'http://the_default_url.com/',
        'assets' => '/assets',
    ],
    'database' => "mysql"
]);
```

And now, your configuration looks like
```php
    'name' => 'My Awesome App',
    'site' => [
        'url' => 'https://youwishyouwerethiscool.com/'
        'protocol' => "https",
        'assets' => '/assets'
    ],
    'database' => "mysql"
```

A couple of things to keep in mind:
  * This works recursively and as far down as you want.
  * If any value is set before loading defaults, that value is preserved
  * If a starting value is set to an array (`one.two = []`) and a default lives beneath (`one.two.three = default`), then the default **will** be set.
  * On the other hand, if the value exists and is **not** an array, the default will be ignored. 
  (`one.two = 'something'`) In this case, there is no `one.two.three`, even after loading defaults.

## Using Other Managers
Each feature of Manager is a different trait, but they are all designed to work in whatever combination you want.
You can either [composer your own](#compose) or use one of the build in classes
  - **Basic Manager**: Only manages items (what you see above).
  - **Manager**: The basic manager with array access and the ability to chain nested items
  - **Config Manager**: Manages items like above, loads config files, allows for defaults.
  - **IoC Manager**: Manages an IoC (DI) container. You can resolve dependencies, etc.
  - **Uber Manager**: Everything in one place. Just for fun, really.


## Some Advanced Features
By default, Manager stores all the items in an `$items` property. 
If you are using the `ManagesItemsTrait` and want to use an internal property besides `$items` to avoid collisions, you have two options:

  1. Use `$manager->setItemsName($nameOfProperty)` either in your constructor or before you add anything
  2. Set the `$dataItemsName` property to a string of the new property name. Then be sure to call `initManager()` in your constructor.


## Exceptions
If you try to `get()` an item that doesn't exist, and there is no fallback, an `ItemNotFoundException` will be thrown.

If you do not want an exception, use `getIfHas($alias)` which will return a `NoItemFoundMessage` object, or use a fallback value `get($item, $fallback)`.

If you try to nest under an existing value that is not an array, an `NestingUnderNonArrayException` will be thrown.
```php
$manager = new Manager(['one' => 1]);
$manager->add("one.two", "two-value"); // exception
```

If you try to alter a protected item, a `ModifyingProtectedItemException` will be thrown.

See /exceptions for more


## Interoperability
Data Manager is [PSR compliant](http://www.php-fig.org/) and [Container Interoperability](https://github.com/container-interop/container-interop) compliant. Any oversights, please let me know.


## Testing
We try for at least 80% test coverage.
``` bash
$ phpunit
```

You may also use the **tests** under `tests/traits` to test your integrated functionality. You may have to grab these through cloning the repo. composer usually won't include tests in your `require`

## Contributing
Contributions are welcome and will be fully credited. Please see [CONTRIBUTING](CONTRIBUTING.md) for details.


## Security
If you discover any security related issues, please email chrismichaels84@gmail.com instead of using the issue tracker.


## Credits
- [Michael Wilson](https://github.com/chrismichaels84)
- [Scott](https://github.com/smolinari)
- Open an issue to join in!


## License
The MIT License (MIT). Please see [License File](LICENSE.md) for more information.