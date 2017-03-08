# Data Manager as an IoC Container
On top of being a powerful data-manager, `Manager` can be used as a flexible IoC container.

For more information about IoC and dependency injection see [Php The Right Way](http://www.phptherightway.com/#dependency_injection)

Manager can:
  * Resolve dependencies from classnames, closures, already instantiated objects, and instances of Manager itself,
  * Create and Resolve Singletons
  * Configure dependencies for dependencies. This can be done a number of ways and to any depth.
  * Allow for fallbacks, if you `get()` a dependency that doesn't exist.
  * Send any object through a closure so you can prepare objects globally.

Please note that this overrides some of the features of `ManagesItemsTrait`. While you an use this to store regular values,
you MUST use `getRaw($alias, $fallback)` to retrieve a value. Otherwise it will try to become a dependency and break.

Also, the previous versions used `di()` and `fetch()`. These are still available, but deprecated.

## Setup
The easiest way is to `$manager = new \Michaels\Manager\IocManager`.

If you want use the traits, you must resolve some conflicts.

```php
class IocManager
{
    use ManagesItemsTrait, ManagesIocTrait {
        ManagesIocTrait::add insteadof ManagesItemsTrait;
        ManagesIocTrait::get insteadof ManagesItemsTrait;
    }
```

**NOTE THAT** `ManagesIocTrait` depend on ManagesItemsTrait. 
If you to use `ManagesIocTrait` without `ManagesItemsTrait`, you will get all sorts of errors.

The IoC container is inspired, mostly, by [Pimple](http://pimple.sensiolabs.org/)

## Basic Usage
Now you can setup dependencies.
```php
/* Setup a dependency using a classname */
$manager->add('event_dispatcher', 'Full\Class\Here');

/* Setup a dependency using a factory (closure) for lazy loading */
$manager->add('event_dispatcher', function ($di) {
    // you have access to the container through $di
    return new WhateverObject($di->get('another_dependency'));
});

/* Setup a dependency using an object for eager loading */
$manager->add('event_dispatcher', new WhateverObject($manager->get('another_dependency'));

/* Setup a dependency using an instance of the IoC container itself */
$manager->add('cool_eventer', $myCoolEventer);
$manager->add('event_dispatcher', $manager);
// Which will call `get('event_dispatcher') on the manager you passed and return what it returns.
```

When you're ready to call dependencies:
```php
$manager->get('event_dispatcher');
```

You may also register multiple aliases to a single dependency
```php
$manager->add(['one', 'two', 'three'], $factory);
$manager->get('one');
$manager->get('two');
$manager->get('three'); // All the same
```

And, just ask for a class. If it exists (and nothing by that name was registered), it will be loaded.
```php
$manager->get('Some/Class')
```

## Dependencies that need Dependencies
The easiest way to setup a dependency that needs a dependency is to use a closure.
```php
$manager->add('email', 'Some\Email\Class');

$manager->add('logger', function ($di) {
    return new Logger($di->get('email'));
});

$manager->add('application', function ($di) {
    return new Application($di->get('logger'));
});
```
Now an `Application` will have a `Logger` and a `Logger` will have an `Email`

## Fallbacks
By default, if you get an item that does not exist, you will get an `ItemNotFoundException`.
If you want a fallback, simply `$manager->get('doesnt_exist', $fallback);`

## Using Singletons
By default, every time you `get()` an item, it will return a new instance of that item (unless the item is an object).
If you want a singleton (that is return the same one each time):
```php
$events->share('event_dispatcher');
$singleton = $events->get('event_dispatcher');
```

## Prepare an object after created but before returned
It is also possible to pass a dependency through some sort of pipeline that will alter the object before returned.
```php
$manager->setup('event_dispatcher', function ($dispatcher, $manager) {
    // Do whatever you want to $dispatcher and then return it
});
```

Note that fallbacks are not sent through the pipeline.

Also note that you must setup pipelines BEFORE you `get()` the first instance of a `share()`d dependency.

## Setting Dependencies Implicitly
You can tell Manager how to configure a certain dependency when you register it.
For the example, let's assume:
```php
$manager->add('one', new One());
$manager->add('two', new Two());
```

**Setting up using a classname**
```php
$manager->add('event_dispatcher', 'My\Event\Dispatcher', ['one', 'two']);
```
Will pass a `new One()` and `new Two()` into `Dispatcher($one, $two)`

**Setting up using a closure**
```php
$manager->add('event_dispatcher', function ($di, $one, $two) {
}, ['one', 'two']);
```
Will pass a `new One()` and `new Two()` into the closure.

**Setting up using an object**
```php
$manager->add('event_dispatcher', $object, ['one', 'two']);
```
Will pass a `new One()` and `new Two()` into the `needs()` method of `$object` if it exists.

If you pass a value that is not a registered dependency, then the value itself is passed.

NOTE: For the moment, you cannot prepare dependencies that are instances of containers.
