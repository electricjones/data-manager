<?php
namespace Michaels\Manager\Decoders;

use Michaels\Manager\Contracts\DecoderInterface;

class YmlDecoder extends YamlDecoder implements DecoderInterface
{
    /* Only needed to be able to build a decoder object with any Yaml file extension type.
     * For instance, with Yaml files, the extension could be ".yaml" or ".yml". Thus, this
     * class is needed for ".yml".
     */
}
