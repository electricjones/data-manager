<?php
namespace Michaels\Manager\Contracts;

/**
 * API Methods for loading file data into the manager
 *
 * See src/Traits/LoadsFilesTrait.php for implementation example
 */

interface LoadsFilesInterface
{
    /**
     * This method adds load file functionality.
     *
     * @param array $files an array of SplFileInfo Objects
     * @param $append boolean
     * @return array
     */
    public function loadFiles(array $files, $append=false);

    /**
     * Allows for the addition of a custom decoder to load custom files..
     *
     * @param DecoderInterface $decoder
     * @return mixed
     */
    public function addDecoder(DecoderInterface $decoder);

}