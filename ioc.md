# Data Manager as an IoC Container
On top of being a powerful data-manager, `Manager` can be used as a flexible IoC container.

For more information about IoC and dependency injection see [Php The Right Way](http://www.phptherightway.com/#dependency_injection)

Manager can:
  * Resolve dependencies from classnames, closures, already instantiated objects, and instances of Manager itself,
  * Create and Resolve Singletons
  * Configure dependencies for dependencies. This can be done a number of ways and to any depth.
  * Allow for fallbacks, if you `fetch()` a dependency that doesn't exist.
  * Send any object through a closure so you can prepare objects globally.

## Setup
Simply include the `Michaels\Manager\ManagesIocTrait` in your class, or create a new `Michaels\Manager\IocManager`.
**NOTE THAT** `ManagesIocTrait` depend on ManagesItemsTrait. 
If you to use `ManagesIocTrait` without `ManagesItemsTrait`, you will get all sorts of errors.

The IoC container is inspired, mostly, by [Pimple](http://pimple.sensiolabs.org/)

```php
class MyContainer {
    use Michaels\Manager\ManagesItemsTrait;
    use Michaels\Manager\ManagesIocTrait;
}

$manager = new MyContainer();

// Or, use the built in
$manager = new Michaels\Manager\IocManager();
```

## Basic Usage
Now you can setup dependencies.
```php
/* Setup a dependency using a classname */
$manager->di('event_dispatcher', 'Full\Class\Here');

/* Setup a dependency using a factory (closure) for lazy loading */
$manager->di('event_dispatcher', function ($di) {
    // you have access to the container through $di
    return new WhateverObject($di->fetch('another_dependency'));
});

/* Setup a dependency using an object for eager loading */
$manager->di('event_dispatcher', new WhateverObject($manager->fetch('another_dependency'));

/* Setup a dependency using an instance of the IoC container itself */
$manager->di('cool_eventer', $myCoolEventer);
$manager->di('event_dispatcher', $manager);
// Which will call `fetch('event_dispatcher') on the manager you passed and return what it returns.
```

When you're ready to call dependencies:
```php
$manager->fetch('event_dispatcher');
```

## Dependencies that need Dependencies
The easiest way to setup a dependency that needs a dependency is to use a closure.
```php
$manager->di('email', 'Some\Email\Class');

$manager->di('logger', function ($di) {
    return new Logger($di->fetch('email'));
});

$manager->di('application', function ($di) {
    return new Application($di->fetch('logger'));
});
```
Now an `Application` will have a `Logger` and a `Logger` will have an `Email`

## Fallbacks
By default, if you fetch an item that does not exist, you will get an `ItemNotFoundException`.
If you want a fallback, simply `$manager->fetch('doesnt_exist', $fallback);`

## Using Singletons
By default, every time you `fetch()` an item, it will return a new instance of that item (unless the item is an object).
If you want a singleton (that is return the same one each time):
```php
$events->share('event_dispatcher');
$singleton = $events->fetch('event_dispatcher');
```

## Prepare an object after created but before returned
It is also possible to pass a dependency through some sort of pipeline that will alter the object before returned.
```php
$manager->setup('event_dispatcher', function ($dispatcher, $manager) {
    // Do whatever you want to $dispatcher and then return it
});
```

Note that fallbacks are not sent through the pipeline.

Also note that you must setup pipelines BEFORE you `fetch()` the first instance of a `share()`d dependency.

## Setting Dependencies Implicitly (Not Yet Implemented)
You can tell Manager how to configure a certain dependency when you register it.
For the example, let's assume:
```php
$manager->di('one', new One());
$manager->di('two', new Two());
```

**Setting up using a classname**
```php
$manager->di('event_dispatcher', 'My\Event\Dispatcher', ['one', 'two']);
```
Will pass a `new One()` and `new Two()` into `Dispatcher($one, $two)`

**Setting up using a closure**
```php
$manager->di('event_dispatcher', function ($di, $one, $two) {
}, ['one', 'two']);
```
Will pass a `new One()` and `new Two()` into the closure.

**Setting up using an object**
```php
$manager->di('event_dispatcher', $object, ['one', 'two']);
```
Will pass a `new One()` and `new Two()` into the `needs()` method of `$object` if it exists.

If you pass a value that is not a registered dependency, then the value itself is passed.

NOTE: For the moment, you cannot prepare dependencies that are instances of containers.

Any feedback here would be appreciated. Take a look at `IocManagerInterface` for future plans.
