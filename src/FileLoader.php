<?php
namespace Michaels\Manager;

use Michaels\Manager\Contracts\DecoderInterface;
use Michaels\Manager\Bags\FileBag;
use Michaels\Manager\Exceptions\UnsupportedFilesException;
use Michaels\Manager\Exceptions\BadFileDataException;
use Exception;

/**
 * Loads configuration files and converts them to php arrays using Decoders
 * @package Michaels\Manager
 */
class FileLoader
{
    /**
     * The data which will be decoded.
     * @var array
     */
    protected $dataToDecode = [];

    /**
     * The array of supported Mime types, which is defined in each decoder class.
     * @var array
     */
    protected $supportedMimeTypes = [];

    /**
     * A container for the SPLFileInfo objects
     * @var FileBag
     */
    protected $fileBag;

    /**
     * An array holding data about the loaded decoders.
     * @var array
     */
    protected $decoders = [];

    /**
     * Should there be an attempt to load an unsupported file type (determined by the mime types array), then they
     * will be stored in this array for later display in an error message.
     *
     * @var array
     */
    protected $unsupportedFiles = [];

    /**
     * The data array, once it has been processed through a decoder.
     * @var array
     */
    protected $decodedData = [];

    /**
     * Add a file decoder to the FileLoader
     * @param DecoderInterface $decoder
     */
    public function addDecoder(DecoderInterface $decoder)
    {
        $mimeTypes = $decoder->getMimeType();
        if ($this->isSupportedMimeType($mimeTypes[0])) {
            return; // we already have the decoder loaded!
        }
        $this->supportedMimeTypes = array_merge($this->supportedMimeTypes, $mimeTypes);
        foreach ($mimeTypes as $type) {
            $this->decoders[$type] = $decoder;
        }
    }

    /**
     * Add a file bag, or change an array of SplFileInfo Objects to proper objects.
     *
     * @param mixed $files a FileBag or an array of SplFileInfo objects.
     */
    public function addFiles($files)
    {
        if ($files instanceof Filebag) {
            $this->fileBag = $files;
            return;
        }

        if (is_array($files)) {
            $this->fileBag = new FileBag($files);
            return;
        }
        throw new BadFileDataException('The attempt at adding files to the file loader failed, due to the files not
        being a FileBag Object or an array of SplInfoObjects.');
    }

    /**
     * Process the current FileBag and return an array
     * @return array
     * @throws Exception
     */
    public function process()
    {
        return $this->decodedData = $this->decodeFileBagData($this->fileBag);
    }

    /**
     * Process file bag to load into the data manager.
     * A file bag is an array of SplFileInfo objects.
     *
     * @param array|FileBag $fileBag
     * @return array
     * @throws Exception
     */
    public function decodeFileBagData(FileBag $fileBag)
    {
        $decodedData = [];
        $files = $fileBag->getAllFileInfoObjects();
        if (empty($files)) {
            throw new Exception("FileBag is empty. Make sure you have initialized the FileLoader and added files.");
        }

        foreach ($files as $file) {
            $fileData = $this->decodeFile($file);
            if ($fileData) {
                $filename = rtrim($file->getBasename(), '.'.$file->getExtension());
                $namespace = $this->sanitizeNamespace($filename);
                foreach ($fileData as $k => $v) {
                    $decodedData[$namespace][$k] = $v;
                }
            }
        }

        if (!empty($this->unsupportedFiles)) {
            $badFiles = implode(", ", $this->unsupportedFiles);
            throw new UnsupportedFilesException(
                'The file(s) ' . $badFiles . ' are not supported by the available decoders.'
            );
        }

        return $decodedData;
    }

    /**
     * Check to make sure the mime type is ok.
     * @param $type a mime type
     * @return bool
     */
    protected function isSupportedMimeType($type)
    {
        return in_array($type, $this->supportedMimeTypes);
    }

    /**
     * Returns the contents of the file.
     *
     * @return string the contents of the file
     * @throws \RuntimeException
     */
    protected function getFileContents($file)
    {
        if ($file->getExtension() === 'php') {
            $content = include $file->getPathname();
            return $content;
        }

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
     * Default decoder class factory method.
     * Checks to make sure we have a default decoder available and if so, adds it as a decoder to the file loader.
     *
     * @param $mimeType
     */
    protected function checkAndAddDefaultDecoder($mimeType)
    {
        $decoderClass = ucfirst($mimeType) . "Decoder";

        if (file_exists(__DIR__ . '/Decoders/' . $decoderClass . '.php') && !$this->isSupportedMimeType($mimeType)) {
            $nameSpace = "Michaels\\Manager\\Decoders\\";
            $fullQualifiedClassName = $nameSpace . $decoderClass;
            $decoder = new $fullQualifiedClassName();
            $this->addDecoder($decoder);
        }
    }

    /**
     * Decodes a single file using registered decoders
     * @param $file
     * @return null
     */
    protected function decodeFile($file)
    {
        $mimeType = $file->getExtension();

        $this->checkAndAddDefaultDecoder($mimeType);

        if ($this->isSupportedMimeType($mimeType)) {
            $data = $this->getFileContents($file);
            return $this->decoders[$mimeType]->decode($data);
        }

        $this->unsupportedFiles[] = $file->getFilename();
        return false;
    }

    /**
     * Returns currently attached custom decoders
     * @return array
     */
    public function getDecoders()
    {
        return $this->decoders;
    }

    /**
     * Returns currently supported Mime Types
     * @return array
     */
    public function getMimeTypes()
    {
        return $this->supportedMimeTypes;
    }

    public function sanitizeNamespace($ns)
    {
        // dots to underscores
        $ns = str_replace(".", "_", $ns);

        // spaces to underscores
        $ns = str_replace(" ", "_", $ns);

        // dashes to underscores
        $ns = str_replace("-", "_", $ns);

        // Alpha numeric
        $ns = preg_replace("/[^A-Za-z0-9\.\_\- ]/", '', $ns);

        return $ns;
    }
}
