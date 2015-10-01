<?php
namespace Michaels\Manager\Bags;

/**
 * Class FileBag - a collection object needed to create SplFileInfo objects from file references, in order to inject
 * into the FileLoader. This collection class can only store SplFileInfo objects. Anything else will cause exceptions.
 *
 * @package Michaels\Manager\Bags
 */

class FileBag
{

    private $fileObjects = array();

    public function __construct($arrayOfSplFileInfoObjects)
    {
        $this->initialize();
    }

    private function initialize($arrayOfSplFileInfoObjects)
    {

    }

    private function isSplFileInfoObject()
    {

    }

    /**
     * @return array
     */
    protected function getAllFileObjects()
    {
        return $this->fileObjects;
    }

    /**
     *  Reset the FileBag back to an empty array.
     */
    protected function emptyBag()
    {
        $this->fileObjects = array();
        return $this->fileObjects;
    }

}