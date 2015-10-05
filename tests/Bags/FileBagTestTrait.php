<?php
namespace Michaels\Manager\Test\Bags;

use StdClass;
use SplFileInfo;

trait FileBagTestTrait
{
    /**
     * This function creates the necessary SplFileInfo objects for testing. Hint, this is what the client coder will
     * need as a minimum, to pass files into the FileBag to load in the data-manager.
     * @param $path
     * @return array
     */
    public function setFilesToSplInfoObjects($path)
    {
        $fileObjects = array();
        if ($handle = opendir($path)) {
            while (false !== ($entry = readdir($handle))) {
                if ($entry !== "." && $entry !== "..") {
                    $fileObject = new SplFileInfo($path.'/'.$entry);
                    $fileObjects[] = $fileObject;
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
    public function setFilesToBadObjects()
    {
        $objects = array();
        $object = new StdClass();
        for($i=0; $i <= 3; $i++) {
            array_push($objects, $object);
        }
        return $objects;
    }

}