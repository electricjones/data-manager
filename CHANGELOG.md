# Changelog
All Notable changes to `Manager` will be documented in this file

# V0.8.2 - 2015-6-3
 - Allow for Traversables and Arrays in manager initialization
 - Throw exception if trying to initialize Manager with a non-array or traversable
 - Throw exception if nesting under an existing value that is not an array
 - Added CustomizedManager test suite and tests for the above
 - ManagesItemsTrait::__toJson() accepts options

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

