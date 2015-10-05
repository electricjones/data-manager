<?php
namespace Michaels\Manager\Test;

use Michaels\Manager\Bags\FileBag;
use Michaels\Manager\Test\Bags\FileBagTestTrait;


/**
 * Class FileBagTest
 * The object of the FileBag is to store SplFileINfo objects only. Anything else and we get exceptions.
 * @package Michaels\Manager\Test
 */

class FileBagTest extends \PHPUnit_Framework_TestCase
{
    use FileBagTestTrait;

    private $goodTestFileDirectory;

    private $goodTestFileObject = array();

    private $badTestFileObject = array();

    private $goodTestFileObjects = array();

    private $badTestFileObjects = array();

    private $fileBag;

    /**
     * Setup test data and FileBag
     */
    public function setup()
    {
        $this->goodTestFileDirectory = realpath(__DIR__ . '/../Fixtures/FilesWithGoodNames');

        $this->goodTestFileObjects = $this->setFilesToSplInfoObjects($this->goodTestFileDirectory);
        $this->badTestFileObjects = $this->setFilesToBadObjects();

        $this->goodTestFileObject = $this->goodTestFileObjects[0];
        $this->badTestFileObject = $this->badTestFileObjects[0];

        $this->fileBag = new FileBag($this->goodTestFileObjects);

    }

    /**
     * @expectedException \Michaels\Manager\Exceptions\BadFileInfoObjectException
     */
    public function testCreationOfFileBagWithBadFileObjects()
    {

        $this->fileBag = new FileBag($this->badTestFileObjects);

    }

    public function testCreationOfFileBagWithGoodFileObjects()
    {
        $this->fileBag->emptyBag();
        $this->fileBag = new FileBag($this->goodTestFileObjects);
        $this->assertEquals($this->goodTestFileObjects, $this->fileBag->getAllFileInfoObjects(), "Creation of file bag was not successful.");
    }

    public function testGetAllFileObjectsInBag()
    {
        $this->assertEquals(
            $this->goodTestFileObjects, $this->fileBag->getAllFileInfoObjects(), "All file objects were not retrieved properly.");
    }

    public function testEmptyingOfFileBag()
    {
        $this->assertEmpty($this->fileBag->emptyBag(), "The fileBag was not properly emptied.");
    }

}