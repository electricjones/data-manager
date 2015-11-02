<?php
namespace Michaels\Manager\Traits;

/**
 * Allows a class to have file loading capabilities.
 *
 * @implements Michaels\Manager\Contracts\ManagesItemsInterface
 * @package Michaels\Manager
 */

use Michaels\Manager\Contracts\DecoderInterface;
use Michaels\Manager\FileLoader;

trait LoadsFilesTrait
{
    private $fileLoader;

    /**
     * Makes sure a FileLoader object was created or creates one.
     *
     */
    private function initializeFileLoader()
    {
        if ( ! is_object($this->fileLoader)){

            $this->fileLoader = new FileLoader($this);
        }
    }

    /**
     * This method adds the file loading functionality.
     *
     *
     * @param array $files an array of SplFileInfo Objects
     * @param $append boolean true, if data should be appended to the manager.
     * @return array
     */
    public function loadFiles(array $files, $append=false)
    {
        $this->initializeFileLoader();
        $this->fileLoader->addFiles($files);
        $this->fileLoader->hydrateManager($append);

    }

    /**
     * Allows for the addition of a custom decoder to the manager load files system.
     *
     * @param DecoderInterface $decoder
     * @return mixed
     */
    public function addDecoder(DecoderInterface $decoder)
    {
        $this->initializeFileLoader();
        $this->fileLoader->addDecoder($decoder);
    }
}

