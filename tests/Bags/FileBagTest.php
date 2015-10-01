<?php
namespace Michaels\Manager\Test;

use Michaels\Manager\Bags\FileBag;

/**
 * Class FileBagTest
 * The object of the FileBag is to store SplFileINfo objects only. Anything else and we get exceptions.
 * @package Michaels\Manager\Test
 */

class FileBagTest extends \PHPUnit_Framework_TestCase
{
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
        $this->goodTestFileDirectory = realpath(__DIR__ . 'Fixtures/Good');

        $this->goodTestFileObjects = $this->setFilesToSplInfoObjects($this->goodTestFileDirectory);
        $this->badTestFileObjects = $this->setFilesToBadObjects();

        $this->goodTestFileObject = array_push($this->goodTestFileObject,array_pop($this->goodTestFileObjects));
        $this->badTestFileObject = array_push($this->badTestFileObject, array_pop($this->badTestFileObjects));

        $this->fileBag = new FileBag($this->goodTestFileObjects);

    }


    /**
     * This function creates the necessary SplFileInfo objects for testing. Hint, this is what the client coder will
     * need as a minimum, to pass files into the FileBag to load in the data-manager.
     * @param $directory
     * @return array|int
     */
    function setFilesToSplInfoObjects($directory)
    {
        $fileObjects = array();
        if ($handle = opendir($directory)) {
            while (false !== ($entry = readdir($handle))) {
                if ($entry != "." && $entry != "..") {
                    $fileObject = new \SplFileObject($entry);
                    $fileObjects = array_push($fileObjects, $fileObject);
                }
            }
            closedir($handle);
        }
        return $fileObjects;
    }

    /**
     * This function creates an array of empty (non-SplFileInfo) objects
     *
     */
    function setFilesToBadObjects()
    {
        $objects = array();
        for($i=0; $i <= 2; $i++) {
            $object = new \stdClass();
            $objects = array_push($objects, $object);
        }
        return $objects;
    }

    /**
     * @expectedException \Michaels\Manager\Exceptions\BadFileObjectException
     */
    public function creationOfFileBagWithBadFileObjectsTest()
    {

        $this->fileBag = new FileBag($this->badTestFileObjects);

    }

    /**
     * Test to make sure files are in the FileBag properly.
     */
    public function creationOfFileBagWithGoodFileObjectsTest()
    {

        $expected = $this->goodTestFileObject;
        $this->assertEquals($expected, $this->fileBag->getLast());
    }


    /**
     *
     */
    public function getAllFileObjectsInBagTest()
    {

    }

    /**
     *
     */
    public function emptyFileBagTest()
    {
        $expected = array();
        $this->assertEquals($expected,$this->fileBag->emptyBag());
    }



}