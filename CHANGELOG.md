# Changelog
All Notable changes to `Manager` will be documented in this file

#v0.9.3
 - Up minimum version to php 5.6 (though 5.4 still passes with older versions of phpunit)
 - Up to phpunit 5.7 (must composer update in 5.6 environment to pass with 5.6)
 - Use new PSR-11 container-interop
 - Update Travis

# v0.9.2
 - IoC Manager is 100% compliant with container-interop
 - Moved to PHPUnit 3 to keep php 5.4 compatibility
 - Removed @expectedExceptions for php 5.4 compatibility
 - Remove `UberManager`, it was begging for trouble

# v0.9.1 - 8-22-2016
 - Move to a new testing structure
 - Add integration tests
 - Cleanup and refactor
 - add `has('$dep.whatever')` interpolation
 - add linking (multiple aliases) to ioc manager
 - add to ioc manager, fetch a class by string
 - Allow user to force no namespacing in file loading
 - Cleanup Test Coverage and Code Analysis
 - Initialize new Docs and Readme
 - Initialize API Documentation and Marketing Site
 
# v0.8.9 - 3-11-2016
 - Add full composer.json

# v0.8.8 - 12-29-2015
 - Auto namespaces FileLoader data under the file's name
 - Allows for custom namespacing as [fileObject, 'namespace']
 - Allows for loading files using just a valid path
 - Reproduces Arrayzy API inside CollectionsTrait
 - Deals with collisions between ChainsNestedItemsTrait and CollectionsTrait
 - Moves and rename $_items into ManagesItemsTrait

# v0.8.7 - 12-2-2015
 - Moves helper methods to a static `Helpers` class
 - Converts all test methods to `snake_case` not `camelCase`

# v0.8.6 - 11-3-2015
 - Updates `ManagesItemsInterface`
 - Updates Readme documentation
 
 - Adds `push()` to `ManagesItemsTrait` to push values onto a nested array (#27)
 - Adds ability to load configuration from files using various decoders
   - Adds `FileBag`, `FileLoader`, various decoders, and `LoadsFilesTrait` with tests
 - Adds `isArrayable()` helper method to `ManagesItemsTrait`
 - Adds method in `ManagesItemsTrait` to prepare data before its returned to user
 - Adds basic collection functionality 
   - Returns instance of `MutableArray` collection if requested
   - Introduced CollectionTrait with tests
   
 - Bug: `getIfExists()` now returns `null` if item has been initializes with null(#17)

# v0.8.5 - 9-17-2015
 - Adds `IocContainerTrait` for basic DI functionality
   - initDI() and share()
   - produce from classname, object, factory, or instance of manager
   - fallbacks, pipelines, and explicit dependencies
 - Adds `loadDefaults(array $defaults)` to `ManagesItemsTrait`
 - Adds `DependsOnManagesItemsTrait` to ensure that other traits require Manages Items.
 - Adds `hydrateFrom()` and `appendFrom()` for non-native object formats
 
# v0.8.4 - 2015-7-14
 - Better documentation (README.md)
 - Add protected items functionality
 - Split Arrayable stuff in Manager to ArrayableTrait Issue #11 with tests
 - Bug: reversed nestable items so you can get a single item without calling it as method
 - Added: CRUD through nested items magic methods (ChainsNestedItemsTrait) Issue #3

# v0.8.3 - 2015-6-24
 - Move constructor out of `ManagesItemsTrait`
 - Added `getIfHas()` which returns the item or a `NoItemFoundMessage`
 - Refactor internal to use `getIfHas()` for performance
 - Allows for configurable $items property

# v0.8.2 - 2015-6-3
 - Allow any value in manager initialization, will be cast to array
 - Throw exception if nesting under an existing value that is not an array
 - Added CustomizedManager test suite and tests for the above
 - ManagesItemsTrait::__toJson() accepts options
 - alias all() to getAll()

# v0.8.1 - 2015-5-12
 - Use initManager() to initialize Manager when extending the base class or using traits

# v0.8 - 2015-4-19
This was the first public release version

### Introduced
- add(), get(), exists(), remove(), getAll(), set(), clear(), reset(), toJson(), isEmpty()
- initialize with items
- full dot-notation and nesting
- throw exception for items not found
- Manager\Manager container interoperability
- Use magic methods to access deeply nested items
- Return json encoded string when used as string
- Manager\Manager use as array and iterator

