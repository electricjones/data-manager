# Composing Your Own Data Manager
If you have your own container objects and want to add Manager functionality to them, you may import traits into your class.
This is also a great way to mix and match exactly which feature you want.

The basic trait you always need to start with is `Michaels\Manager\ManagesItemsTrait`.
There is an accompanying interface.

After that, you may add any of the feature traits you like. 
It's important to note, however, that all of these feature traits depend on `ManagesItemsTrait`,
so you must include that one FIRST:

```php
use ManagesItemsTrait, ArrayableTrait, CollectionTrait;
```

None of the traits include a constructor. If you want to have your class be initializable with data:

```php
class MyClass
{
    use ManagesItemsTrait;
    
    public function __construct($beginningItems)
    {
        $this->initManager($beginningItems);
    }
}
```
initManager() is used so it doesn't conflict with user-defined init() methods.


## Available Traits
  1. [`ManagesItemsTrait`](#getting-started) fulfills `ManagesItemsInterface` and adds most functionality. Look at the interface for full list.
  2. [`ArrayableTrait`](arrayable.md) makes the class usable as an array (`$manager['some']['data']`) or in loops and such
  3. [`ChainsNestedItemsTrait`](chains.md) allows you to use fluent properties to manage data (`$manager->one()->two()->three = 'three`)
  4. [`CollectionTrait`](collections.md) returns collections with all sorts of [array helpers](https://github.com/bocharsky-bw/Arrayzy)
  5. [`ManagesIocTrait`](ioc.md) turns Manager into a simple, but complete IoC or Dependency Injection manager
  6. [`LoadsFilesTrait`](load-files.md) allows Manager to load data from config files.

There are special considerations when using ManagesIocTrait. See its documentation.

You may also use the **tests** under `tests/traits` to test your integrated functionality. You may have to grab these through cloning the repo. composer usually won't include tests in your `require`
