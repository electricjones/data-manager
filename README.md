# Data Manager
[![Latest Version](https://img.shields.io/github/release/chrismichaels84/data-manager.svg?style=flat-square)](https://github.com/chrismichaels84/data-manager/releases)
[![Documentation Status](https://readthedocs.org/projects/data-manager/badge/?version=latest)](http://data-manager.readthedocs.io/en/latest/?badge=latest)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://img.shields.io/travis/chrismichaels84/data-manager/master.svg?style=flat-square)](https://travis-ci.org/chrismichaels84/data-manager)
[![Coverage Status](https://coveralls.io/repos/chrismichaels84/data-manager/badge.svg?branch=master)](https://coveralls.io/r/chrismichaels84/data-manager?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/chrismichaels84/data-manager/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/chrismichaels84/data-manager/?branch=master)
[![Total Downloads](https://img.shields.io/packagist/dt/michaels/data-manager.svg?style=flat-square)](https://packagist.org/packages/michaels/data-manager)

[![SensioLabsInsight](https://insight.sensiolabs.com/projects/3ef3b9a4-6078-4ddf-bf0d-c84dac87f37a/big.png)](https://insight.sensiolabs.com/projects/3ef3b9a4-6078-4ddf-bf0d-c84dac87f37a)

Simple data manager for nested data, dot notation access, extendability, and container interoperability.

**[See Full Documentation](http://data-manager.readthedocs.io/en/latest/)**

## Goals
  * Light weight and fluent, simple, clear API
  * Manage nested data via dot-notation
  * Be [composable](docs/compose.md) - integrate into current containers via traits (extras)
  * Include extras for 
    * [Loading Files](docs/load-files.md), 
    * [Managing IoC](docs/ioc.md) / Dependencies.
  * Allow for protected data (immutable) and default values.
  * IoC container should: 
    * Resolve via classes, factories, etc 
    * Configure dependencies for dependencies,
    * Allow for fallbacks, preparing objects, and more.
  * Full test coverage, PSR compliant, [container interoperability](https://github.com/container-interop/container-interop), and best practices

    
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

/* You can also use $manager as an array or in loops */
$manager['some']['starting']['data']; // 'here (optional)'
//etc

foreach ($manager as $item => $value) {
    // do whatever your heart desires
}

/* You may also push elements onto an array */
$manager->set('a.b', []);
$manager->push('a.b', 'c', 'd', 'e');
$manager->get('a.b'); // ['c', 'd', 'e']

/* Finally, you may manage values using magic methods */
$manager->some()->starting()->data; // 'here (optional)'
$manager->some()->item = 'item'; // sets some.item = 'item'
$manager->some()->item()->drop(); // deletes some.item

// Note that levels are called as a method with no params. The data is then called, updated, or set as a property.
```

## Advanced Features
See [documentation](http://data-manager.readthedocs.io/en/latest/) for topics like protecting data, using as an ioc container, loading files, using as an array, defaults, composing, and more.

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
