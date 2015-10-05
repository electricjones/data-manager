<?php
namespace Michaels\Manager\Contracts;

/**
 *
 * @package Michaels\Manager
 */

interface DecoderInterface
{
    /**
     * This is the method, which does the decoding work.
     *
     * @param $data
     * @return array
     */
    public function decode($data);


    /**
     * The data returned is the actual file extensions supported for the files to decode.
     * Make sure the first array element is the prefix of the decoder class.
     * i.e. if array is ['yaml', 'yml'] the decoder class must be "YamlDecoder"
     *
     * @return array
     */
    public function getMimeType();


}