# Data Manager
Framework-agnostic manager for data processing, management, and analysis. Turn raw data into gold.

> According to myth, Midas was a man bestowed with a golden hand that would transform all he touched to gold. Midas-Data does the same for your data sets. Just don't turn your wife to gold.

This package is in the development stages currently releasing its first BETA. The functionality is stable and production ready, but feedback about the API is still be accepted. Things may change between versions! Please see [CONTRIBUTING.md](CONTRIBUTING.md) to pitch in. For API or direction suggestions, submit a PR against `develop`s PROPOSAL.md file.

## Introduction
Midas is a processing object that works on whatever data you provide it. You will be able to ask *questions* of that data or issue *commands* to process that data in some way. The Midas package itself is almost empty. That is because all the questions and commands are algorithms that you load into (or teach to) midas. You can also save data sets to be reused, use data in mutable or immutable ways, nest algorithms and data, and stream data through multiple algorithms, outputting it in any way you choose.

## Use Cases
When would it be good to use Midas?

## Overreaching Goals
  * Light weight algorithm manager with a fluent API
  * Use third-party algorithms packs via composer
  * Pass data and parameters to algorithms and have RefinedData returned
  * Multiple types of algorithms (commands that transform, questions, etc)
  * Save and reuse datasets 
  * **Not Yet Implemented**
    * Nest and chain algorithms
    * Stream data through multiple algorithms
    * Use Data Objects with full dataset history

### More Goals
  * ~~Ability to load algorithms and equations, and then solve given parameters~~
  * ~~Save and reuse datasets and algorithms~~
  * When streaming, use Outputters to format output for CLI, HTTP, Etc
  * ~~Ability to process data in an immutable way. (input one structure, output another)~~
  * Create a DataObject that can save its own version history
  * Architecture is DRY, SOLID, PSR, and best practices

## Concepts
These are the basic terms and concepts that make midas work.

An **Algorithm** is a function that takes `data` and `parameters` and processes said data in a procedural way, returning a value.

A **command** transforms your data according to an algorithm and then returns results.

A **question** analyzes your data according to an algorithm and returns `true` or `false`. There may be the ability for questions in the future which return more complex answers.

A **stream** or **pipe** is an ordered sequence or algorithms which your data is processed through, finally returning a RefinedDataObject or outputing the data in some way.

## Compared to Gulp
  * First, gulp runs in php. That's the main difference
  * Becuase of this, Midas is not truly streaming
  * Midas is completely synchronous

## Reserved Words
  * These words may not be used as any aliases: `is`, `does`, `operation`, `command`, `algorithm`, 
  `data`, `parameter`, `midas`, `stream`, `pipe`, `end`, `result`, `out`, `output`, `finish`
  `solve`, `process`, `solveFor`

## Roadmap for the Future
#### ~~v0.1 Midas Container~~
  * ~~Main Midas Container~~
  * ~~Manage Commands~~

#### ~~v0.2 Process Data and Return~~
  * ~~Process through commands~~
  * ~~Create RefindedData Objects that extend Collections~~
  * ~~Return Refined Data Objects (Not MidasData)~~
  * ~~Save datasets for reuse~~
  * ~~Midas Configuration: reserved words, error handling~~
  * ~~Generic Command Helpers Init~~

#### ~~v0.3 Algorithm Packs and Samples~~
  * ~~Add multiple algorithms (commands, questions, etc) from Algorithm Packs~~

#### v0.4 Questions
  * Ask a question
  * Chain questions
  * Use conjunctions
  * Wrap questions in operations
 
#### v0.5 Streaming A
  * Stream `$data` via an array of commands and algorithms
  * ```php $midas->stream($data, [['command', $params]]);```
  * Nest Algorithms here or at streaming?

#### v0.6 Streaming B
  * Stream `$data` using pipes
  * ```php $midas->stream($data)->through()->algorithm()->return();```
  * Endpoints: `return()`, `end()`, `out()`, `out(Outputter $outputter)`

#### v0.7 Midas Data Objects
  * Create Midas Data Objects for self storage
  * ```php $data = $midas->make($data); $data->command('x');```

#### v1.0 Bugsquash and Awesome
  * Can be released after v0.7 and run parallel with First Party Algorithms

#### Beyond
  * React PHP async streaming support

### To Look Into
  * Dependency Injection
  * Autoloading Commands from Packs

## Sample API
```php
/** Commands **/
$result = $midas->solve($data, $params); // magic method
$result = $midas->filter($data, $criteria);

/* Manage commands */
$midas->addCommand('solve', new EquationCommand()); //done
$midas->addCommand('solve', 'Namespace\EquationCommand'); //done
$midas->addCommand('solve', function(RawData $data){ //done
  return $processedData;
});

// Or use an IoC container to resolve Command Dependencies
$container = new Container; // PHP League
$container->add('Dependency');
$container->add('solver', 'SolverCommand')
          ->withArgument('Dependency')
          ->withArgument($someConfig);
          
$midas->addCommand('solve', $container->get('solver'));

// Getters, Setters, and Helpers
$midas->getCommand('solve');
$midas->isCommand(); $midas->setCommand(); $midas->deleteCommand();
$midas->clearCommands():

/** Data **/
$data = $midas->make($data);
$data->isQuestion($params); //for one question
$data->is($data)->question()->question()->ask() // multiple questions

/* Save Data Sets for reuse */
$midas->addData('dataset', $data);
$midas->solve('dataset', $params);
$midas->process('dataset', $params);

$dataset = $midas->getDataSet('dataset'); // Returns MidasDataObject
$dataset->solve($params);

$dataset = $midas->getDataSet('dataset', false); // Returns a ResultDataSet, not methods

/** Optionally, you can create a MidasDataObject **/
// Saves all stages to the data object
$data = $midas->newDataObject() // or data($data) or make($data)
$data->set($data) // if using data() or make(), you can skip this
$data->command(); // 0
$data->process('algorithm') //1
$data->stream()->through()->algorithm()->end(); // 2

// Now you can get it
$zero = $data->getResult(0) // getFirstResult()
$one = $data->getResult(1) // getResult(2)->getPreviousResult()->getNextResult()
$two = $data->getResult(2) // getLastResult()
$two = $data->get(); // get's latest result

/** Questions **/
$answer = $midas->is($data, $questions, $params); // for one question
$answer = $midas->is($data)->question($params)->ask(); // for question chaining

// Chain questions with conjunctions
$midas->is($data)
 ->question1($params)
 ->and()->question2($params)
 ->or()->question3($params)
 ->butNot()->question4($params)
 ->ask();
 
 // Finally, you can use closures to order comparisons
 $midas->is($data)
   ->opperation(function($a){
      return $a->question1($params)->and('question2')->ask()
   })->butNot()->opperation(function($a){
      return $a->question3($params)->or('question4')->ask();
   })
   ->ask();

/** Use nested algorithms **/
// Just use the handed $midas object?
function($data, $params, $command) {
    $command->midas->run('some.dependency');
}

/** Algorithm Packs **/
// Composer packages with a valid MidasProvider::provides()

$midas->addX('algorithm')->from('vendor.pack'); // add a specific command
$midas->addXs()->from('vendor.pack'); // add all commands from pack
$midas->addPack('vendor.pack'); // add all algorithms from pack

$midas->vendor->pack->command();

Class MidasPovider
{
    public static function provides()
    {
        return [
            'commands' => [
                'com1' => 'Vendor\Pack\Commands\Command',
                'com2' => 'vendor\Pack\Commands\AnotherCommand',
            ],
            'questions' => [],
        ]
    }
}

// A pack must be PSR-4: Vendor\Pack

/** Streaming and Pipes **/
$midas->stream($data, [
  ['command', $params],
  ['command', $params],
  [':algorithm', $params]
]);

$midas->stream($data) // or ->pipe($data)
 ->through()
   ->command('name', $params)
   ->algorithm($params)
   ->out(new Outputter()) // default echoes, otherwise goes through Outputter
   ->algorithm($params)
   ->command('name', $params)
   ->return(); // Ends the stream an returns refined data
```

## Potential first-party algorithms/commands
  * `solve()` for equations
  * `marshal()` for conforming data w/ dependency
  * `transform()` for outputting data w/ fractal dependency
  * `solveFor()` for equations
  * `filter()` returning results from dataset w/ dependency
  * `valdate()` returns data schema errors w/ dependency

**Other potential names**: Alchemist, Spinner, Forge, Kiln, Cauldron
