<?php
namespace Michaels\Manager\Decoders;
/**
 * A wrapper for the PHP Decoder Module for the data manager file loader.
 *
 * @package Michaels\Manager
 */

use Michaels\Manager\Contracts\DecoderInterface;

class PhpDecoder implements DecoderInterface
{

    /**
     * This is the method, which does the decoding work.
     *
     * @param $data
     * @return array
     */
    public function decode($data)
    {
        return $data;
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
        return ['php'];
    }

}