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
    /**
     * @var array An array of SplFileInfo objects
     */
    protected $fileObjects = [];

    /**
     * Constructs a new FileBag
     * @param $arrayOfSplFileInfoObjects
     */
    public function __construct(array $arrayOfSplFileInfoObjects = [])
    {
        $this->initialize($arrayOfSplFileInfoObjects);
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
        $this->fileObjects = [];
        return $this->fileObjects;
    }

    /**
     * Set up the bag with a proper array of SplFileInfo objects
     * @param array $splFileInfoObjects
     * @internal param $arrayOfSplFileInfoObjects
     */
    protected function initialize(array $splFileInfoObjects = [])
    {
        if (!empty($splFileInfoObjects)) {
            foreach ($splFileInfoObjects as $object) {
                array_push($this->fileObjects, $this->createSplFileInfoObject($object));
            }
        }
    }

    /**
     * Check for an \SplFileInfo object or a custom namespaces object
     * @param $entity
     * @return bool
     */
    protected function createSplFileInfoObject($entity)
    {
        // This is already a valid object
        if ($entity instanceof \SplFileInfo) {
            return $entity;
        }

        // This is a valid path
        if (is_string($entity) && file_exists($entity)) {
            return new \SplFileInfo($entity);
        }

        // This is an array with a valid object
        $isArray = is_array($entity);
        if ($isArray && $entity[0] instanceof \SplFileInfo) {
            return $entity;
        }

        // This is an array with a valid path
        if ($isArray && is_string($entity[0]) && file_exists($entity[0])) {
            return [new \SplFileInfo($entity[0]), $entity[1]];
        }

        // We've run out of options
        throw new BadFileInfoObjectException('The input array does not hold proper SplFileInfo objects.');
    }
}
