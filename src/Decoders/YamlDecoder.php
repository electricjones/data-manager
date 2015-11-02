<?php
namespace Michaels\Manager\Decoders;

use Michaels\Manager\Contracts\DecoderInterface;
use Symfony\Component\Yaml\Parser;

/**
 * SerializationTypeNotSupportedException
 * @package Michaels\Manager
 */
class YamlDecoder implements DecoderInterface
{
    private $arrayData = [];

    /**
     * This is the method, which does the decoding work.
     *
     * @param $data
     * @return array
     */
    public function decode($data)
    {
        $parser = new Parser();
        $this->arrayData = $parser->parse($data);
        return $this->arrayData;
    }


    /**
     * The data returned is the actual file extensions supported for the files to decode.
     * For instance, if you want to decode Yaml files with the extensions ".yml" and ".yaml",
     * then you want to set the return array to ['yaml', 'yml'].
     *
     * @return array
     */
    public function getMimeType()
    {
        return ['yaml','yml'];
    }

}

