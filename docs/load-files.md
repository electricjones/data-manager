# Data Manager and Loading Files
Manager also gives you the ability to load file data into the Manager. 
A good use case for this is loading configuration data out of different configuration files.

## Getting Started
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

## Decoders
Each file is decoded via an instance of a `DecoderInterface`. There are three included:
  1. `PhpDecoder` which decodes any `*.php` file that `return []`
  2. `JsonDecoer` will load any valid `*.json` file
  
There is also a standard `YamlDecoder`, but you must include `symfony/yaml` via composer.

You may create a decoder for anything you like. 
Simply implement the `DecoderInterface` and `$manager->addDecoder($decoder)`

For an example custom decoder, have a look at the `\CustomXmlDecoder` class in the `/Decoders` directory. 

Once you've created your custom decoder, you can add it with the `$manager->addDecoder()` method *before* loading any file data. 


## Namespaces
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