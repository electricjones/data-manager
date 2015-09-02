<?php
namespace Michaels\Manager\Test\Traits;

use Michaels\Manager\IocManager as Manager;
use StdClass;

class ManagesIocTest extends \PHPUnit_Framework_TestCase
{
    public $manager;
    public $testData;

    public function setUp()
    {
        $object = new stdClass();
        $object->type = 'object';

        $this->testData = [
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
    public function testInitIocContainer()
    {
        $manager = new Manager();
        $manager->initDI($this->testData);

        $this->assertEquals($this->testData, $manager->get('_diManifest'), "Failed to return di manifest");
    }

    // THIS IS NOT PART OF THE TRAIT, ONLY THE CONCRETE CLASS. Tested here to save time.
    public function testAnotherInit()
    {
        $manager = new Manager($this->testData, ['other' => ['items' => true]]);
        $this->assertTrue($manager->get("other.items"), "failed to set generic items");
        $this->assertEquals($this->testData, $manager->get('_diManifest'), "Failed to return di manifest");
    }

    public function testGetIocManifest()
    {
        $manager = new Manager($this->testData);

        $this->assertEquals($this->testData, $manager->getIocManifest(), "Failed to return di manifest");
    }

    public function testGetEmptyManifestIfUninitialized()
    {
        $manager = new Manager();

        $this->assertEquals([], $manager->getIocManifest(), "Failed to return di manifest");
    }

    public function testAddDependencies()
    {
        $manager = new Manager();

        foreach ($this->testData as $key => $value) {
            $manager->di($key, $value);
        }

        $this->assertEquals($this->testData, $manager->get('_diManifest'), "Failed to return di manifest");
    }

    public function testFetchDependencies()
    {
        $manager = new Manager();

        $manager->initDI($this->testData);

        $string = $manager->fetch('string'); // Should return Manager
        $callable = $manager->fetch('callable'); // Should return stdClass::type = callable
        $object = $manager->fetch('object'); // Should return stdClass::type = object

        $this->assertInstanceOf('\SplObjectStorage', $string, "Failed to return string factory");
        $this->assertEquals('callable', $callable->type, 'Failed to return callable factory');
        $this->assertEquals('object', $object->type, "Failed to return object factory");
    }

    public function testManagerInstanceAsFactory()
    {
        $factory = new Manager([
            'container' => '\stdClass',
        ]);

        $manager = new Manager([
            'container' => $factory
        ]);

        $actual= $manager->fetch('container'); // Should return Manager

        $this->assertInstanceOf('\stdClass', $actual, "failed to produce from a string");
    }

    /**
     * @expectedException \Exception
     */
    public function testExceptionOnInvalidFactory()
    {
        $manager = new Manager();

        $manager->di('one', true);

        $manager->fetch('one');
    }

    public function testShareObject()
    {
        // Setup
        $manager = new Manager();
        $object = new stdClass();
        $object->prop = 'property';

        $manager->initDI([
            'object' => $object
        ]);

        // Share and modify
        $manager->share('object');
        $manager->fetch('object')->prop = 'new-object-value';

        // Get the modified object back
        $actual = $manager->fetch('object');
        $this->assertEquals('new-object-value', $actual->prop, "failed to share string");
    }

    public function testShareString()
    {
        // Setup
        $manager = new Manager();
        $manager->initDI([
            'string' => 'stdClass'
        ]);

        // Share and modify
        $manager->share('string');
        $manager->fetch('string')->prop = 'new-string-value';

        // Get the shared back
        $actual = $manager->fetch('string');
        $this->assertEquals('new-string-value', $actual->prop, "failed to share string");
    }

    public function testShareFactory()
    {
        $manager = new Manager();
        $manager->initDI([
            'factory' => function () {
                return new stdClass();
            }
        ]);
        $manager->share('factory');

        $manager->fetch('factory')->prop = 'new-factory-value';

        $this->assertEquals('new-factory-value', $manager->fetch('factory')->prop, "failed to share string");
    }
}