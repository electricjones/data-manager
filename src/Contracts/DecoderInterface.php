<?php
namespace Michaels\Manager\Decoders;

/**
 * SerializationTypeNotSupportedException
 * @package Michaels\Manager
 */
interface DecoderInterface
{
    /**
     * @param $data
     * @return array
     */
    public function decode($data);


}