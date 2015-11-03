<?php
namespace Michaels\Manager\Contracts;

/**
 * Contract for Decoders
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
     * For instance, if you want to decode Yaml files with the extensions ".yml" and ".yaml",
     * then you want to set the return array to ['yaml', 'yml'].
     *
     * @return array
     */
    public function getMimeType();
}
