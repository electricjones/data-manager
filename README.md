# Midas-Data

[![Latest Version](https://img.shields.io/github/release/chrismichaels84/midas.svg?style=flat-square)](https://github.com/chrismichaels84/midas/releases)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://img.shields.io/travis/chrismichaels84/midas/master.svg?style=flat-square)](https://travis-ci.org/chrismichaels84/midas)
[![Coverage Status](https://coveralls.io/repos/chrismichaels84/midas/badge.svg?branch=master)](https://coveralls.io/r/chrismichaels84/midas?branch=master)
[![Total Downloads](https://img.shields.io/packagist/dt/michaels/midas.svg?style=flat-square)](https://packagist.org/packages/michaels/midas)

[![SensioLabsInsight](https://insight.sensiolabs.com/projects/74752ec3-3676-4167-a0f0-b17affea9928/big.png)](https://insight.sensiolabs.com/projects/74752ec3-3676-4167-a0f0-b17affea9928)

Framework-agnostic manager for data processing, management, and analysis. Turn raw data into gold.

> According to myth, Midas was a man bestowed with a golden hand that would transform all he touched to gold. Midas-Data does the same for your data sets. Just don't turn your wife to gold.

This package is in the development stages currently releasing its first BETA. The functionality is stable and production ready, but feedback about the API is still be accepted. Things may change between versions! Please see [CONTRIBUTING.md](CONTRIBUTING.md) to pitch in.

## Goals
  * Light weight algorithm manager with a fluent API
  * Use third-party algorithms packs via composer
  * Pass data and parameters to algorithms and have RefinedData returned
  * Multiple types of algorithms (commands that transform, questions, etc)
  * Save and reuse datasets 
  * **Not Yet Implemented**
    * Nest and chain algorithms
    * Stream data through multiple algorithms
    * Use Data Objects with full dataset history

Please see the [PROPOSAL.md](PROPOSAL.md) for more information.

## Install
Via Composer
``` bash
$ composer require michaels/midas
```

## Getting Started
``` php
$midas = new Michaels\Midas\Midas();

/** Add a command **/
$midas->addCommand('touch', function($data, $params) {
    $data .= " has been turned to " . $params . " gold!";
    return $data;
});

$result = $midas->touch('my data', 'pure'); // "my data has been turned to pure gold"
```

## Usage and Concepts
### Basic Concepts
An **[algorithm](http://en.wikipedia.org/wiki/Algorithm)** is simply a function that does something with data and returns a value. So, lets say you wanted to filter a dataset. The algorithm would take the full dataset and return the filtered one.

Midas uses different kinds of algorithms. **Commands** transform data in some way (the same way that Midas the man transformed objects into gold). So commands could filter or rearange or compress data.

There will be other kinds of algorithms in the future like questions. They have not yet been implimented. See [the proposal](PROPOSAL.md)

### Adding and Managing Commands
A **command** processes your data and returns the result.

You can add commands to midas in one of three ways. First and simplest is as a **closure**.
```php
$midas->addCommand('alias', function($data, $params, $command) {
    // algorithm here that returns a result
});
```
The closure is handed three arguments when its run: `$data`, `$params`, and `$command`. `$data` is the input to be processed and `$params` are any parameters that the algorithm needs. When a command is executed, Midas turns the closure into an object that is an instance of `Commands\GenericCommand` which means it comes with some helpers. These helpers are accessed from the `$command` argument. Think of `$command` as `$this`. And you don't have to use it.

For more complex commands (especially those that may use dependencies), you can add an instance of `Commands\CommandInterface` either by **classname** or an **instantiated object**.

```php
class MyAwesomeCommand implements \Michaels\Midas\Commands\CommandInterface
{
    // This is the only required method
    public function run($data, $params)
    {
        // Just like the closure, process and return results
    }
}

$midas->addCommand('alias', 'Namespace\MyAwesomeCommand');
// or
$midas->addCommand('alias', new Namespace\MyAwesomeCommand());
```
You may also extend `Commands\GenericCommand` to inherit the helpers that closures get. They are used through `$this`

Once you have commands added to Midas, you can manage them in a variety of ways.
```php
$midas->addCommand($name, $command);
$midas->addCommands([$name1 => $command, $name2 => $command]);
$midas->getCommand('alias'); // Returns the raw command (closure, object, or classname)
$midas->getAllCommands(); // Returns array of all commands
$midas->fetchCommand('alias'); // Returns the executable command object
$midas->isCommand('alias'); // Has this command been added?
$midas->setCommand('alias', 'new value'); // Adds or overwrites a command
$midas->removeCommand('alias'); // Removes a single command
$midas->clearCommands(); // Yep, removes all commands
```

### Using Commands
Now that you have commands added, all you have to do to use them is talk to midas.
```php
$result = $midas->alias($data, $params);
```
It is best practice to make the aliases verbs so you can speak fluently to Midas.
```php
$result = $midas->convert($data, $params);
$result = $midas->filter($data, $params); // etc
```
This is all done with magic methods. There are some reserved words. See `Midas\Midas` for a list.

### Namespaced Commands
You can also namespace your commands with dot notation. For instance, `one.two.three` and `a.b.three` are entirely different commands. This allows you to vendor prefix your commands just like composer packages. It is reccomended to use the same structure `vendor.package.command`.

To issue or run a namespaced command, you have two options. First, you can use the `run()` method.
```php
$midas->addCommand('some.cool.example', function($data, $params) { return $data });
$result = $midas->run('some.cool.example', $data, $params);
```

You may also use magic methods to get at it directly.
```php
$midas->addCommand('some.cool.example', function($data, $params) { return $data });
$result = $midas->some->cool->example($data, $params);
```

Both of the above will work identically.

### Save and Manage Datasets
You can also save sets of data to be reused. Anytime you have to manage something, the API is the similar as managing commands. In this case, only `fetch()` works differently.
```php
$midas->addData('alias', $data);
$midas->getData('alias');
$midas->getAllData();
$midas->fetchData('alias'); // Returns the data converted to a Data\RawData instance
$midas->isData('alias'); // Has this data been added?
$midas->setData('alias', 'new value'); // Adds or overwrites a data
$midas->removeData('alias'); // Removes a single data
$midas->clearData(); // Yep, removes all data

$midas->data('alias'); // This will return the raw data you gave it
$midas->data('alias', true); // This will return a RawData instance with some helpers
```
Then, get at your data like this.
```php
$midas->addData('friends', ['michael', 'nicole', 'bethany']);
$midas->doSomeCommand($midas->data('friends'), $params);
```

### Ask and Manage Questions
Not yet implemented. See [proposal](PROPOSAL.md) for more information.

### Stream and Pipe Data
Not yet implemented. See [proposal](PROPOSAL.md) for more information.

### Nesting and Chaining
Not yet implemented. See [proposal](PROPOSAL.md) for more information.

### Configure Midas
Midas have several configurable options. You may set them at instantiation or at any point afterward.

  * `reserved_words`: An array of words that cannot be used as aliases for commands.
  * More options are on the way.

```php
/* Configure at instantiation */
$midas = new Midas(['option' => 'value']);

/* Configure via manager methods */
$midas->config($item, $fallback); // Get a config item or a fallback
$midas->setConfig($item, $value); // Set a config item or an array of items
$midas->getConfig($item, $fallback);
$midas->getAllConfig();
$midas->getDefaultConfig($item, $fallback); // Get a factory shipped config item
```

### Algorithm Packs
Midas itself is a lightweight manager. That's it. For now, it does not contain any actual algoritms. You can, of course, add your own commands as above, but wouldn't it be nice if we could distribute and repurpose algorithms? After all, DRY is a way of life.

Using composer and a simple convention, you can load third-party packs of algorithms or distribute your own packs. In fact, Midas was originally created to be used in [Spider-Graphs](https://github.com/chrismichaels84/Spider-Graph) to manage algorithms for graph calculations.

An algorithm pack is just a PSR-4 composer package with a MidasProvider class that provides a simple manifest.

#### Loading Algorithm Packs
First, `composer require` the third-party package. Assuming it was created with best practices in mind, it should follow the namespace scheme `Vendor\Packname\<AlgorithmName>` with as many sub namespaces as needed.

In order to *use* that pack, simply `$midas->addPack('vendor.packname')` replacing `\` with `.`. Now, all the algorithms (questions, commands, etc) are free to use under that namespace. `$midas->vendor->packname->command()`.

If you want to add a single command:
```php
$midas->addCommand('name')->from('vendor.packname');
$midas->vendor->packname->name($data, $params);
```

You can also add all the commands from a pack:
```php
$midas->addCommands()->from('vendor.packname');
$midas->vendor->packname->commandOne();
$midas->vendor->packname->commandTwo();
```

Finally, you may add commands from a pack to a specific namespace, or even to the top-level.
```php
$midas->addCommands()->under('some.namespace')->from('vendor.packname');
// or
$midas->addCommand('commandOne')->under('some.namespace')->from('vendor.packname');
$midas->some->namespace->commandOne();

$midas->addCommands()->toTop()->from('vendor.packname');
$midas->commandOne();
```
Be careful doing this! The new commands will overwrite any already registered commands with that alias.

#### Creating and Distributing Packs
A pack is a composer package that follows PSR-4 namespacing and includes a MidasProvider class:
```php
namespace Vendor\Pack;

use Michaels\Midas\Packs\MidasProviderInterface;

class MidasProvider implements MidasProviderInterface
{
    /**
    * Returns a manifest of algorithms provided
    * @return array
    */
    public static function provides()
    {
        return [
            'commands' => [
                'commandOne' => 'class|closure|object' // just like addCommand();
                'commandTwo' => 'Commands\MyCommand' 
                // It adds the pack namespace automatically, so this is actually Vendor\Pack\Commands\MyCommand
            ],
        ];
     }
}
```

That's it. Just provide a manifest of your commands. It is best practice to organize your algorithms my type `Vendor\Pack\Commands\<command>`, `Vendor\Pack\Questions\<question>`, etc.

Once you have done that, create a composer package and push it up to packagist. Make sure to declare Midas as a dependency. Typically, name the package something like `midas-packname` and tag it with `algorithms` and `midas`

## Testing
``` bash
$ phpunit
```

## Contributing
Contributions are welcome and will be fully credited. Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security
If you discover any security related issues, please email chrismichaels84@users.noreply.github.com instead of using the issue tracker.

## Credits
- [Michael Wilson](https://github.com/chrismichaels84)
- Open an issue to join it!

## License
The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
