<?php
namespace Michaels\Manager;

use ArrayAccess;
use Countable;
use Interop\Container\ContainerInterface;
use IteratorAggregate;
use JsonSerializable;
use Michaels\Manager\Contracts\ChainsNestedItemsInterface;
use Michaels\Manager\Contracts\ManagesItemsInterface;
use Michaels\Manager\Traits\ArrayableTrait;
use Michaels\Manager\Traits\ChainsNestedItemsTrait;
use Michaels\Manager\Traits\ManagesItemsTrait;

/**
 * Manages deeply nested, complex data.
 *
 * This concrete class implements ManagesItems and ChainsNestedItems as well as
 * Container interoperability and various array functionality.
 *
 * @package Michaels\Manager
 */
class Manager implements
    ManagesItemsInterface,
    ChainsNestedItemsInterface,
    ContainerInterface,
    ArrayAccess,
    Countable,
    IteratorAggregate,
    JsonSerializable
{
    use ManagesItemsTrait, ChainsNestedItemsTrait, ArrayableTrait;

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
