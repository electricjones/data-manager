<?php
namespace Michaels\Manager\Test;

use Michaels\Manager\Bags\FileBag;
use Michaels\Manager\Test\Utilities\FileBagTestTrait;


/**
 * Class FileBagTest
 * The object of the FileBag is to store SplFileINfo objects only. Anything else and we get exceptions.
 * @package Michaels\Manager\Test
 */

class FileBagTest extends \PHPUnit_Framework_TestCase
{
    use FileBagTestTrait;

    private $goodTestFileDirectory;

    private $goodTestFileObject = [];

    private $badTestFileObject = [];

    private $goodTestFileObjects = [];

    private $badTestFileObjects = [];

    private $fileBag;

    /**
     * Setup test data and FileBag
     */
    public function setup()
    {
        $this->goodTestFileDirectory = realpath(__DIR__ . '/../Fixtures/FilesWithGoodExtensions');

        $this->goodTestFileObjects = $this->setFilesToSplInfoObjects($this->goodTestFileDirectory);
        $this->badTestFileObjects = $this->setFilesToBadObjects();

        $this->goodTestFileObject = $this->goodTestFileObjects[0];
        $this->badTestFileObject = $this->badTestFileObjects[0];

        $this->fileBag = new FileBag($this->goodTestFileObjects);

    }

    /**
     * @expectedException \Michaels\Manager\Exceptions\BadFileInfoObjectException
     */
    public function test_creation_of_file_bag_with_bad_file_objects()
    {
        $this->fileBag = new FileBag($this->badTestFileObjects);
    }

    public function test_creation_of_file_bag_with_good_file_objects()
    {
        $this->fileBag = new FileBag($this->goodTestFileObjects);
        $this->assertEquals($this->goodTestFileObjects, $this->fileBag->getAllFileInfoObjects(), "Creation of file bag was not successful.");
    }

    public function test_get_all_file_objects_in_bag()
    {
        $this->assertEquals(
            $this->goodTestFileObjects, $this->fileBag->getAllFileInfoObjects(), "All file objects were not retrieved properly.");
    }

    public function test_emptying_of_file_bag()
    {
        $this->assertEmpty($this->fileBag->emptyBag(), "The fileBag was not properly emptied.");
    }

}
