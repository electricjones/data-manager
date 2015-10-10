<?php
namespace Michaels\Manager\Decoders;

use Michaels\Manager\Contracts\DecoderInterface;
use Michaels\Manager\Exceptions\JsonDecodingFailedException;
/**
 * A standard Json Decoder Module for the data manager file loader.
 *
 * @package Michaels\Manager
 */
class JsonDecoder implements DecoderInterface
{

    private $arrayData;

    /**
     * Decodes JSON data to array
     *
     * @param $data string
     * @return array
     * @throws JsonDecodingFailedException
     */
    public function decode($data)
    {
        if (is_string($data)) {

            $this->arrayData = json_decode($data, true); // true gives us associative arrays

            if($this->isValidJson()) {

                return $this->arrayData;
            }
        }

        throw new JsonDecodingFailedException('The data provided was not proper JSON');
    }


    /**
     * @return string
     */
    public function getMimeType()
    {
        return ['json'];
    }


    /**
     * Checks if the input is really a json string and if the PHP Json decoding was successful.
     *
     * @return boolean
     */
    protected function isValidJson()
    {
        return (json_last_error() === JSON_ERROR_NONE);
    }




}