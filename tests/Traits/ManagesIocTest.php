<?php
namespace Michaels\Manager\Test\Traits;

use Michaels\Manager\Test\Stubs\IocManagerStub as Manager;
use StdClass;

class ManagesIocTest extends \PHPUnit_Framework_TestCase
{
    public $manager;
    public $expected;

    public function setup()
    {
        $object = new stdClass();
        $object->type = 'object';

        $this->expected = [
            'string' => 'Michaels\Manager\Manager',
            'callable' => function () {
                $return = new stdClass();
                $return->type = 'callable';
                return $return;
            },
            'object' => $object
        ];

        $this->manager = new Manager();
    }

    /** Begin Tests **/
    public function testInitIocContainer()
    {
        $manager = new Manager();
        $manager->initDI($this->expected);

        $this->assertEquals($this->expected, $manager->get('_diManifest'), "Failed to return di manifest");
    }

    public function testAddDependencies()
    {
        $manager = new Manager();

        foreach ($this->expected as $key => $value) {
            $manager->di($key, $value);
        }

        $this->assertEquals($this->expected, $manager->get('_diManifest'), "Failed to return di manifest");
    }

    public function testFetchDependencies()
    {
        $manager = new Manager();

        $manager->initDI($this->expected);

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

        $manager->initDI($this->expected);

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