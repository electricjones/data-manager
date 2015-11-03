<?php
namespace Michaels\Manager\Decoders;

use Michaels\Manager\Contracts\DecoderInterface;

/**
 * This is just a simple XML decoder for test purposes.
 *
 * SerializationTypeNotSupportedException
 * @package Michaels\Manager
 */
class CustomXmlDecoder implements DecoderInterface
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
        $xml = simplexml_load_string($data, "SimpleXMLElement", LIBXML_NOCDATA);
        $json = json_encode($xml);
        $this->arrayData = json_decode($json,TRUE);
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
        return ['xml'];
    }

}


