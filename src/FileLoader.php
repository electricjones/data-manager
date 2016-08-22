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
        if ($files instanceof FileBag) {
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
     * @param bool $ns
     * @return array
     */
    public function process($ns = true)
    {
        return $this->decodedData = $this->decodeFileBagData($this->fileBag, $ns);
    }

    /**
     * Process file bag to load into the data manager.
     * A file bag is an array of SplFileInfo objects.
     *
     * @param array|FileBag $fileBag
     * @param bool $ns
     * @return array
     * @throws Exception
     */
    public function decodeFileBagData(FileBag $fileBag, $ns = true)
    {
        $decodedData = [];
        $files = $fileBag->getAllFileInfoObjects();
        if (empty($files)) {
            throw new Exception("FileBag is empty. Make sure you have initialized the FileLoader and added files.");
        }

        foreach ($files as $file) {
            list($namespace, $file) = $this->getFilesNamespace($file);

            // Decode the actual file and save data
            $fileData = $this->decodeFile($file);
            if (is_array($fileData)) {
                foreach ($fileData as $k => $v) {
                    if ($ns === true) {
                        $decodedData[$namespace][$k] = $v;
                    } else {
                        $decodedData[$k] = $v;
                    }
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
     * @param string $type A mime type
     * @return bool
     */
    protected function isSupportedMimeType($type)
    {
        return in_array($type, $this->supportedMimeTypes);
    }

    /**
     * Returns the contents of the file.
     *
     * @param \SplFileInfo $file
     * @return string the contents of the file
     */
    public function getFileContents(\SplFileInfo $file)
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
     * @param \SplFileInfo $file
     * @return array|bool
     */
    protected function decodeFile(\SplFileInfo $file)
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

    /**
     * Cleans up a file name for use as a namespace
     * @param $ns
     * @return mixed
     */
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

    /**
     * Gets or creates the file's namespace
     * @param $file
     * @return array with the namespace and fileobject
     */
    protected function getFilesNamespace($file)
    {
        $namespace = null;

        if (is_array($file)) {
            // We are using a custom namespace
            $namespace = $this->sanitizeNamespace($file[1]);
            $file = $file[0];
            return [$namespace, $file];

        } else {
            // We are namespacing using the file's name
            $filename = rtrim($file->getBasename(), '.' . $file->getExtension());
            $namespace = $this->sanitizeNamespace($filename);
            return [$namespace, $file];
        }
    }
}
