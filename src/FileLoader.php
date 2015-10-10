<?php
namespace Michaels\Manager;

use Michaels\Manager\Contracts\DecoderInterface;
use Michaels\Manager\Bags\FileBag;
use Michaels\Manager\Exceptions\UnsupportedFilesException;
use Michaels\Manager\Exceptions\BadFileDataException;
use Exception;


class FileLoader
{

    private $supportedMimeTypes = [];

    private $fileBag = [];

    private $decoders = [];

    private $manager;

    private $unsupportedFiles = [];

    private $decodedData = [];

    /**
     * Constructor - gets a manager instance to work with.
     *
     * @param Manager $manager
     */

    public function __construct(Manager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * Add a file decoder to the FileLoader
     *
     * @param DecoderInterface $decoder
     */
    public function addDecoder(DecoderInterface $decoder)
    {
        $mimeTypes = $decoder->getMimeType();
        $this->supportedMimeTypes = array_merge($this->supportedMimeTypes, $mimeTypes);
        foreach($mimeTypes as $type)
        {
            $this->decoders[$type] = $decoder;
        }
    }

    /**
     * Add a file bag, or change an array of SplFileInfo Objects to proper objects.
     *
     * @param mixed $files a filebag or an array of SplFileInfo objects.
     */
    public function addFiles($files)
    {
        if ($files instanceof Filebag){
            $this->fileBag =  $files->getAllFileInfoObjects();
            return;
        }
        if (is_array($files)){
            $tempFileBag = new FileBag($files);
            $this->fileBag = $tempFileBag->getAllFileInfoObjects();
            return;
        }
        throw new BadFileDataException('The attempt at adding files to the file loader failed, due to the files not
        being a FileBag Object or an array of SplInfoObjects.');
    }

    /**
     * Process file bag to load into the data manager.
     * A file bag is an array of SplFileInfo objects.
     *
     * @param bool $append set to true, if you want to append data to the manager.
     * @throws \Exception
     */
    public function hydrateManager($append = false)
    {
        if(empty($this->fileBag)){
            throw new Exception("FileBag is empty. Make sure you have initialized the FileLoader and added files.");
        }

        foreach($this->fileBag as $file)
        {
            $mimeType = $file->getExtension();

            $this->checkAndAddDefaultDecoder($mimeType);

            if ($this->isSupportedMimeType($mimeType)){

                $data= $this->getFileContents($file);
                $this->decodedData[] = $this->decoders[$mimeType]->decode($data);

            }else{
                $this->unsupportedFiles[] = $file;
            }
        }

        if ( ! empty($this->unsupportedFiles)){
            $badFiles = implode(", ", $this->unsupportedFiles);
            throw new UnsupportedFilesException('The file(s) '. $badFiles .' are not supported by the available decoders.');
        }

        if ($append){
            $this->addDataToManager();
            return;
        }
        $this->resetManagerAndAddData();
    }

    /**
     * Check to make sure the mime type is ok.
     * @param $type
     * @return bool
     */
    protected function isSupportedMimeType($type)
    {
        return in_array($type, $this->supportedMimeTypes);
    }

    /**
     * Resets the file loader back to initial state.
     */
    public function reset()
    {
        $this->fileBag = array();
        $this->supportedMimeTypes = array();
        $this->unsupportedFiles = array();
        $this->dataToDecode = array();
    }

    /**
     * Returns the contents of the file.
     *
     * @return string the contents of the file
     *
     * @throws \RuntimeException
     */
    protected function getFileContents($file)
    {
        $level = error_reporting(0);
        $content = file_get_contents($file->getPathname());
        error_reporting($level);
        if (false === $content) {
            $error = error_get_last();
            throw new \RuntimeException($error['message']);
        }
        return $content;
    }

    /**
     * This method appends data to an existing manager.
     */
    protected function addDataToManger()
    {
        foreach ($this->decodedData as $data)
        {
            $this->manager->add($data);
        }
    }

    /**
     * This method will start with a fresh manager and then add data to it.
     */
    protected function resetManagerAndAddData()
    {
        $reset = true;
        foreach ($this->decodedData as $data)
        {
            if ($reset === true){
                $this->manager->reset($data);
                $reset = false;
            }
            $this->manager->add($data);
        }
    }

    /**
     * Default decoder class factory method.
     * Checks to make sure we have a default decoder available and if so, adds it as a decoder to the file loader.
     *
     * @param $mimeType
     */
    protected function checkAndAddDefaultDecoder($mimeType)
    {
        $decoderClass = ucfirst($mimeType)."Decoder";
        //var_dump("File:".__DIR__.'/Decoders/'.$decoderClass.'.php', file_exists(__DIR__.'/Decoders/'.$decoderClass.'.php') , ! $this->isSupportedMimeType($mimeType));
        if(file_exists(__DIR__.'/Decoders/'.$decoderClass.'.php') && ! $this->isSupportedMimeType($mimeType)){

            include_once(__DIR__.'/Decoders/'.$decoderClass.'.php');
            $nameSpace = "Michaels\\Manager\\Decoders\\";
            $fqcn = $nameSpace . $decoderClass;
            $decoder = new $fqcn();
            $this->addDecoder($decoder);
        }

    }
}