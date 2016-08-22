# Data Manager and Loading Files
Manager also gives you the ability to load file data into the Manager. 
A good use case for this is loading configuration data out of different configuration files.

  1. `use LoadsFilesTrait`
  2. Use `$manager->loadFiles($files)` to load a group of files. 
  
The `$files` argument can be a `FileBag` object or an array of:
  1. `\SplFileInfo` objects
  2. Valid paths (`__DIR__.'/some/path/here.json'`)
  
For more powerful uses (like loading entire directories or advanced filesystem searches), you may also us [Symfony's Finder Component](https://github.com/symfony/Finder).
```php
$finder = new Finder();
$finder->files()->in(__DIR__);
$manager->loadFiles($finder);
```

The data will be added to manager under the filename. So, if you load `config.json` you could `$manager->get('config.item')`;
It is possible to set a custom namespace for each file:
```php
$manager->loadFiles([
  __DIR__.'/path/to/filename.json', // auto namespaces under filename
  [new \SplFileInfo('path/to/file.yaml'), 'namespace'], // will namespace like ->get('namespace.item')
  [__DIR__.'/path/to/file.php, 'another'] // will namespace like ->get('another.item')
]);
```

This works well to turn Manager into a Config Bank.
You may also use the `ManagesItemsTrait` default data.
See `Michaels\Manager\ConfigManager` for examples.