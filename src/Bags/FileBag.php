<?php
namespace Michaels\Manager\Bags;

use Michaels\Manager\Exceptions\BadFileInfoObjectException;

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
        $this->initialize($arrayOfSplFileInfoObjects);
    }

    /**
     * Set up the bag with a proper array of SplFileInfo objects
     * @param $arrayOfSplFileInfoObjects
     * @throws BadFileInfoObjectException
     */
    private function initialize($arrayOfSplFileInfoObjects)
    {
        foreach ($arrayOfSplFileInfoObjects as $object)
        {
            if($this->isSplFileInfoObject($object)){

                array_unshift($this->fileObjects, $object);

            }else{

                throw new BadFileInfoObjectException('The input array does not hold proper SplFileInfo objects.');
            }
        }

    }

    /**
     * Check for an \SplfileInfo object
     * @param $object
     * @return bool
     */
    protected function isSplFileInfoObject($object)
    {
        return $object instanceof  \SplFileInfo ? true : false;
    }

    /**
     * Return an array of SplFileInfo objects
     * @return array
     */
    public function getAllFileInfoObjects()
    {
        return $this->fileObjects;
    }

    /**
     * Reset the FileBag back to an empty array.
     * @return array - an empty array
     */
    public function emptyBag()
    {
        $this->fileObjects = array();
        return $this->fileObjects;
    }

}