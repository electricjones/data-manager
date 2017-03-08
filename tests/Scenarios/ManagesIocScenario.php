<?php
namespace Michaels\Manager\Test\Scenarios;

use Michaels\Manager\IocManager as Manager;
use Michaels\Manager\Test\Stubs\DependencyFactoryStub;
use StdClass;

trait ManagesIocScenario
{
    public $iocTestData;

    public function setupTestData()
    {
        $object = new stdClass();
        $object->type = 'object';

        $this->iocTestData = [
            'string' => '\SplObjectStorage', // Used here for overhead
            'callable' => function () {
                $return = new stdClass();
                $return->type = 'callable';
                return $return;
            },
            'object' => $object
        ];
    }

    /** Begin Tests **/
    public function test_init_ioc_container()
    {
        $this->setupTestData();
        $manager = $this->getManager();
        $manager->initManager($this->iocTestData);

        $this->assertEquals($this->iocTestData, $manager->getAll(), "Failed to return di manifest");
    }

    // THIS IS NOT PART OF THE TRAIT, ONLY THE CONCRETE CLASS. Tested here to save time.
    public function test_add_dependencies()
    {
        $this->setupTestData();
        $manager = $this->getManager();

        foreach ($this->iocTestData as $key => $value) {
            $manager->add($key, $value);
        }

        $this->assertEquals($this->iocTestData, $manager->getAll(), "Failed to return di manifest");
    }

    public function test_get_dependencies()
    {
        $this->setupTestData();
        $manager = $this->getManager();

        $manager->initManager($this->iocTestData);

        $string = $manager->get('string'); // Should return Manager
        $callable = $manager->get('callable'); // Should return stdClass::type = callable
        $object = $manager->get('object'); // Should return stdClass::type = object

        $this->assertInstanceOf('\SplObjectStorage', $string, "Failed to return string factory");
        $this->assertEquals('callable', $callable->type, 'Failed to return callable factory');
        $this->assertEquals('object', $object->type, "Failed to return object factory");
    }

    public function test_manager_instance_as_factory()
    {
        $factory = new Manager([
            'container' => '\stdClass',
        ]);

        $manager = new Manager([
            'container' => $factory
        ]);

        $actual= $manager->get('container'); // Should return Manager

        $this->assertInstanceOf('\stdClass', $actual, "failed to produce from a string");
    }

    public function test_exception_on_invalid_factory()
    {
        $this->setExpectedException('\Exception');

        $manager = $this->getManager();
        $manager->add('one', true);
        $manager->get('one');
    }

    public function test_share_object()
    {
        // Setup
        $manager = $this->getManager();
        $object = new stdClass();
        $object->prop = 'property';

        $manager->initManager([
            'object' => $object
        ]);

        // Share and modify
        $manager->share('object');
        $manager->get('object')->prop = 'new-object-value';

        // Get the modified object back
        $actual = $manager->get('object');
        $this->assertEquals('new-object-value', $actual->prop, "failed to share string");
    }

    public function test_share_string()
    {
        // Setup
        $manager = $this->getManager();
        $manager->initManager([
            'string' => 'stdClass'
        ]);

        // Share and modify
        $manager->share('string');
        $manager->get('string')->prop = 'new-string-value';

        // Get the shared back
        $actual = $manager->get('string');
        $this->assertEquals('new-string-value', $actual->prop, "failed to share string");
    }

    public function test_share_factory()
    {
        $manager = $this->getManager();
        $manager->initManager([
            'factory' => function () {
                return new stdClass();
            }
        ]);
        $manager->share('factory');

        $manager->get('factory')->prop = 'new-factory-value';

        $this->assertEquals('new-factory-value', $manager->get('factory')->prop, "failed to share string");
    }

    public function test_factory_manager_injection()
    {
        $manager = $this->getManager();
        $email = new stdClass();
        $email->test = 'testing';

        // Register dependencies
        $manager->add('email', $email);

        $manager->add('logger', function ($di) {
            $logger = new stdClass();
            $logger->email = $di->get('email');
            return $logger;
        });

        $manager->add('application', function ($di) {
            $application = new stdClass();
            $application->logger = $di->get('logger');
            return $application;
        });

        $application = $manager->get('application');

        $this->assertEquals($email, $application->logger->email, "failed to set dependencies down the chain");
    }

    public function test_fallbacks()
    {
        $manager = $this->getManager();
        $fallback = new stdClass();
        $fallback->testing = "yes";

        $actual = $manager->get('notset', $fallback);

        $this->assertEquals($fallback, $actual, "failed to return fallback");
    }

    public function test_prepare_dependencies()
    {
        $manager = $this->getManager();
        $manager->add('prepared', new stdClass());
        $manager->add('unprepared', new stdClass());

        $manager->setup('prepared', function ($object, $manager) {
            $object->prepared = 'yes';
            return $object;
        });

        $this->assertEquals('yes', $manager->get('prepared')->prepared, "failed to prepare object");
        $this->assertFalse(isset($manager->get('unprepared')->prepared), "failed to return unprepared object");
    }

    public function test_declaring_dependencies_with_classnames()
    {
        $manager = $this->getManager();

        // Setup the dependencies
        $one = new stdClass();
        $one->a = "A";

        $two = new stdClass();
        $two->b = "B";

        $manager->add('one', $one);
        $manager->add('two', $two);

        // Declare the one that needs dependencies
        $manager->add('three', 'Michaels\Manager\Test\Stubs\DependencyFactoryStub', ['one', 'two', true]);

        $three = $manager->get('three');

        $this->assertEquals("A", $three->one->a, "failed to set the first dependency");
        $this->assertEquals("B", $three->two->b, "failed to set the second dependency");
        $this->assertEquals(true, $three->three, "failed to set the passed argument");
    }

    public function test_declaring_dependencies_with_objects()
    {
        $manager = $this->getManager();

        // Setup the dependencies
        $one = new stdClass();
        $one->a = "A";

        $two = new stdClass();
        $two->b = "B";

        $manager->add('one', $one);
        $manager->add('two', $two);

        // Declare the one that needs dependencies
        $manager->add('three', new DependencyFactoryStub(), ['one', 'two', true]);

        $three = $manager->get('three');

        $this->assertEquals("A", $three->one->a, "failed to set the first dependency");
        $this->assertEquals("B", $three->two->b, "failed to set the second dependency");
        $this->assertEquals(true, $three->three, "failed to set the passed argument");
    }

    public function test_declaring_dependencies_with_closures()
    {
        $manager = $this->getManager();

        // Setup the dependencies
        $one = new stdClass();
        $one->a = "A";

        $two = new stdClass();
        $two->b = "B";

        $manager->add('one', $one);
        $manager->add('two', $two);

        // Declare the one that needs dependencies
        $manager->add('three', function ($di, $one, $two, $three) {
            return new DependencyFactoryStub($one, $two, $three);
        }, ['one', 'two', true]);

        $three = $manager->get('three');

        $this->assertEquals("A", $three->one->a, "failed to set the first dependency");
        $this->assertEquals("B", $three->two->b, "failed to set the second dependency");
        $this->assertEquals(true, $three->three, "failed to set the passed argument");
    }

    public function test_throws_exception_for_no_item_set()
    {
        $this->setExpectedException("\\Michaels\\Manager\\Exceptions\\ItemNotFoundException");
        $manager = $this->getManager();
        $manager->get('nothing_set');
    }

    public function test_links()
    {
        $manager = $this->getManager();
        $manager->add(['one', 'two', 'three'], '\\stdClass');

        $this->assertInstanceOf('\stdClass', $manager->get('one'), "failed to produce the master'");
        $this->assertInstanceOf('\stdClass', $manager->get('two'), "failed to produce the first link'");
        $this->assertInstanceOf('\stdClass', $manager->get('three'), "failed to produce the second link'");
    }

    public function test_get_class()
    {
        $manager = $this->getManager();
        $this->assertInstanceOf('Michaels\Manager\Manager', $manager->get('\Michaels\Manager\Manager'), "failed to produce a dependency from a class'");
    }

    public function test_complex_example()
    {
        $this->setupTestData();
        /* Create some managers */
        $firstManager = new Manager(
            ['first' => '\stdClass'],
            ['one' => ['two' => ['three' => 'three-value']]]
        );

        $secondManager = $this->getManager();

        foreach ($this->iocTestData as $key => $value) {
            $secondManager->add($key, $value);
        }
        $secondManager->add('first', $firstManager);

        /* Use a pipeline */
        $secondManager->setup('object', function ($object, $m) {
            $object->objectSetupProp = 'objectSetupProp';
            return $object;
        });

        $secondManager->setup('second', function ($object, $m) {
            $return = new stdClass();
            $return->second = $object;
            return $return;
        });

        $secondManager->setup('string', function ($object, $m) {
            $object->stringSetupProp = 'stringSetupProp';
            return $object;
        });

        /* Create some singletons */
        $secondManager->share('object');
        $secondManager->get('object')->objectProp = 'objectProp';

        $secondManager->share('first');
        $secondManager->get('first')->firstProp = 'firstProp';

        /* Declare some dependencies */
        $secondManager->add('third', function ($di, $one, $two, $three) {
            return new DependencyFactoryStub($one, $two, $three);
        }, ['first', 'string', true]);

        $secondManager->add('second', new DependencyFactoryStub(), ['third', 'callable']);

        /* Use a fallback */
        $fallback = new stdClass();
        $fallback->testing = "yes";

        /* Assertions */
        /*
         * A Table of dependencies
         * ------------------------------------------------------------------------
         *          name        type    Notes
         * ------------------------------------------------------------------------
         * shared   first     string    stdClass, through $firstManager
         *                              $firstManager['first']
         *                              $first->firstProp = 'firstProp'
         * pipe     second    object    $firstManager['second']
         *                              Instance of DepInjStub
         *                              Dependencies: [one => third, two => callable],
         *                              Pipeline wraps as $newObject
         *                              $newObject->second->one->one (first)
         *                              $newObject->second->one->one->firstProp = 'firstProp'
         *--------------------------------------------------------------------------
         * pipe     string      string  SplObjectStorage
         *                              Pipeline adds ->stringSetupProp = 'stringSetupProp'
         *          callable    closure $object->type = 'callable'
         * share    object      object  $object->type = 'object'
         * pipe                         $object->objectSetupProp = 'objectSetupProp'
         *                              $object->objectProp = 'objectProp' (from shared)
         *--------------------------------------------------------------------------
         *          third       closure Dependencies: [one => first, two => string, three => true]
         *                                            $third->one->firstProp = 'firstProp'
         */
        // First
        $this->assertInstanceOf('\stdClass', $secondManager->get('first'), "failed to return correct `first`");
        $this->assertEquals('firstProp', $secondManager->get('first')->firstProp, "failed to share `first`");

        // Second
        $this->assertInstanceOf('stdClass', $secondManager->get('second'), "failed to return pipelined `second`");
        $this->assertInstanceOf('Michaels\Manager\Test\Stubs\DependencyFactoryStub', $secondManager->get('second')->second, "failed to return correct `second`");
        $this->assertInstanceOf('Michaels\Manager\Test\Stubs\DependencyFactoryStub', $secondManager->get('second')->second->one, "failed to return correct `second` with `third` dependency");
        $this->assertEquals('firstProp', $secondManager->get('second')->second->one->one->firstProp, "failed to set correct `third` dependency chain");

        // Standards
        $string = $secondManager->get('string');
        $callable = $secondManager->get('callable');
        $object = $secondManager->get('object');

        $this->assertInstanceOf('SplObjectStorage', $string, "Failed to return string factory");
        $this->assertEquals('stringSetupProp', $string->stringSetupProp, "failed to pipeline string");
        $this->assertEquals('callable', $callable->type, 'Failed to return callable factory');
        $this->assertEquals('object', $object->type, "Failed to return object factory");
        $this->assertEquals('objectSetupProp', $object->objectSetupProp, "failed to pipeline object");
        $this->assertEquals('objectProp', $object->objectProp, "failed to share object");

        // Multi-level Dependencies [one => first, two => string, three => true]
        $third = $secondManager->get('third');
        $this->assertInstanceOf('Michaels\Manager\Test\Stubs\DependencyFactoryStub', $third, "Failed to return the correct `third`");

        $this->assertInstanceOf('\stdClass', $secondManager->get('third')->one, "failed to return correct `first`");
        $this->assertEquals('firstProp', $secondManager->get('third')->one->firstProp, "failed to share `first`");

        $this->assertInstanceOf('SplObjectStorage', $secondManager->get('third')->two, "Failed to return string factory");
        $this->assertEquals('stringSetupProp', $secondManager->get('third')->two->stringSetupProp, "failed to pipeline string");

        $this->assertTrue($secondManager->get('third')->three);

        /* Test it does not interfere with normal Manager */
        $firstManager->set('one.two.four', 'four-value');
        $this->assertEquals('four-value', $firstManager->getRaw('one.two.four'), "Failed to return normal items");

        /* Test IoC Situations */
        // Fallback
        $this->assertEquals($fallback, $secondManager->get('notset', $fallback), "failed to return fallback");
    }
}