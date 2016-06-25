<?php
namespace Michaels\Manager;

use ArrayAccess;
use Countable;
use Interop\Container\ContainerInterface;
use IteratorAggregate;
use JsonSerializable;
use Michaels\Manager\Contracts\LoadsFilesInterface;
use Michaels\Manager\Contracts\ManagesItemsInterface;
use Michaels\Manager\Traits\ArrayableTrait;
use Michaels\Manager\Traits\LoadsFilesTrait;
use Michaels\Manager\Traits\ManagesItemsTrait;

/**
 * Manages deeply nested, complex data.
 *
 * This class is perfect for using Manager as a configuration bank. You can load from
 * config files, set and retrieve, and is arrayable.
 *
 * @package Michaels\Manager
 */
class ConfigManager implements
    ManagesItemsInterface,
    ContainerInterface,
    ArrayAccess,
    Countable,
    IteratorAggregate,
    JsonSerializable,
    LoadsFilesInterface
{
    use ManagesItemsTrait, ArrayableTrait, LoadsFilesTrait;

    /**
     * The items stored in the manager
     * @var array $items Items governed by manager
     */
    protected $items;

    /**
     * Build a new manager instance
     * @param array $items
     */
    public function __construct($items = [])
    {
        $this->initManager($items);
    }
}
