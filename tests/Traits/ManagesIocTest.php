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
            'string' => 'Michaels\Manager\Manager',
            'callable' => function () {
                $return = new stdClass();
                $return->type = 'callable';
                return $return;
            },
            'object' => $object
        ];
    }

    /** Begin Tests **/
//    public function testInitIocContainer()
//    {
//        $manager = new Manager();
//        $manager->initDI($this->testData);
//
//        $this->assertEquals($this->testData, $manager->get('_diManifest'), "Failed to return di manifest");
//    }

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

        $this->assertInstanceOf('Michaels\Manager\Manager', $string, "Failed to return string factory");
        $this->assertEquals('callable', $callable->type, 'Failed to return callable factory');
        $this->assertEquals('object', $object->type, "Failed to return object factory");
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

    /* ToDo: This does not actually test the share functionality */
    public function testShare()
    {
        $manager = new Manager();

        $manager->initDI($this->testData);

        $manager->share('string');
        $manager->share('callable');
        $manager->share('object');

        $string = $manager->fetch('string'); // Should return Manager
        $callable = $manager->fetch('callable'); // Should return stdClass::type = callable
        $object = $manager->fetch('object'); // Should return stdClass::type = object

        $this->assertInstanceOf('Michaels\Manager\Manager', $string, "Failed to return string factory");
        $this->assertEquals('callable', $callable->type, 'Failed to return callable factory');
        $this->assertEquals('object', $object->type, "Failed to return object factory");
    }
}