<?php
namespace Michaels\Manager\Decoders;

use Michaels\Manager\Contract\DecoderInterface;
/**
* SerializationTypeNotSupportedException
* @package Michaels\Manager
*/
class JsonDecoder extends DecoderInterface
{






    /**
     * Hydrate with external data
     *
     * @param $type  string    The type of data to be hydrated into the manager
     * @param $data string     The data to be hydrated into the manager
     * @param bool $append     When true, data will be appended to the current set
     * @return $this
     */
    public function hydrateFrom($type, $data, $append=false)
    {
        $decodedData = $this->prepareData($type, $data);
        if ($append===false){
            $this->reset($decodedData);
        }else{
            $this->add($decodedData);
        }

        return $this;
    }

    /**
     * Checks if the input is really a json string
     * @param $data mixed|null
     * @return bool
     */
    protected function validateJson($data)
    {
        if ($data !== "") {
            return (json_last_error() === JSON_ERROR_NONE);
        }
    }

    /**
     * Decodes JSON data to array
     * @param $data string
     * @return mixed|null
     */
    protected function decodeFromJson($data)
    {
        if (is_string($data)) {
            return json_decode($data, true); // true gives us associative arrays
        }

        return "";
    }

    /**
     * Check to make sure the type input is ok. Currently only for JSON.
     * @param $type
     * @return bool
     */
    protected function isFormatSupported($type)
    {
        $type = strtolower(trim($type));
        $supported = ['json'];

        return in_array($type, $supported);
    }

}