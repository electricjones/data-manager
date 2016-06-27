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

/**
 * Loads data from configuration-type files into Manager
 * @package Michaels\Manager\Traits
 */
trait LoadsFilesTrait
{
    use DependsOnManagesItemsTrait;

    /** @var  \Michaels\Manager\FileLoader Instance of the FileLoader */
    protected $fileLoader;

    /**
     * Makes sure a FileLoader object was created or creates one.
     * @param FileLoader $fileLoader
     */
    protected function initializeFileLoader(FileLoader $fileLoader = null)
    {
        if (!isset($this->fileLoader)) {
            $this->setFileLoader(($fileLoader) ? $fileLoader : new FileLoader());
        }
    }

    /**
     * This method adds the file loading functionality.
     *
     * @param array $files an array of SplFileInfo Objects
     * @param $append boolean true, if data should be appended to the manager.
     * @return array
     */
    public function loadFiles(array $files, $append = false, $namespace = true)
    {
        $this->initializeFileLoader();
        $this->fileLoader->addFiles($files);
        $data = $this->fileLoader->process($namespace);
        $this->hydrate($data, $append);
    }

    public function loadFile($file, $append = false, $namespace = true)
    {
        return $this->loadFiles([$file], $append, $namespace = true);
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

    /**
     * @return FileLoader
     */
    public function getFileLoader()
    {
        return $this->fileLoader;
    }

    /**
     * @param FileLoader $fileLoader
     */
    public function setFileLoader($fileLoader)
    {
        $this->fileLoader = $fileLoader;
    }
}
