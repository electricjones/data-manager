# Changelog
All Notable changes to `Manager` will be documented in this file

#v0.8.4 - NEXT
 - Added DependsOnManagesItemsTrait to ensure Collections and ChainsNestedItems can manage items
 - Bug: reversed nestable items so you can get a single item without calling it as method
 - Added: CRUD through nested items magic methods (ChainsNEstedItemsTrait) Issue #3

#v0.8.3 - 2015-6-24
 - Move constructor out of `ManagesItemsTrait`
 - Added `getIfHas()` which returns the item or a `NoItemFoundMessage`
 - Refactor internal to use `getIfHas()` for performance
 - Allows for configurable $items property

# V0.8.2 - 2015-6-3
 - Allow any value in manager initialization, will be cast to array
 - Throw exception if nesting under an existing value that is not an array
 - Added CustomizedManager test suite and tests for the above
 - ManagesItemsTrait::__toJson() accepts options
 - alias all() to getAll()

# V0.8.1 - 2015-5-12
 - Use initManager() to initialize Manager when extending the base class or using traits

# v0.8 - 2015-4-19
This was the first public release version

### Added
- add(), get(), exists(), remove(), getAll(), set(), clear(), reset(), toJson(), isEmpty()
- initialize with items
- full dot-notation and nesting
- throw exception for items not found
- Manager\Manager container interoperability
- Use magic methods to access deeply nested items
- Return json encoded string when used as string
- Manager\Manager use as array and iterator

