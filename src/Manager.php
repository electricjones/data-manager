<?php
namespace Michaels\Manager;

use ArrayIterator;
use ArrayAccess;
use Countable;
use IteratorAggregate;
use JsonSerializable;
use Interop\Container\ContainerInterface;
use Michaels\Manager\Contracts\ChainsNestedItemsInterface;
use Michaels\Manager\Contracts\ManagesItemsInterface;
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
    use ManagesItemsTrait, ChainsNestedItemsTrait;

    /**
     * @implements ArrayAccess
     */
    public function offsetExists($offset)
    {
        return $this->exists($offset);
    }

    /**
     * @implements ArrayAccess
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * @implements ArrayAccess
     */
    public function offsetSet($offset, $value)
    {
        $this->set($offset, $value);
    }

    /**
     * @implements ArrayAccess
     */
    public function offsetUnset($offset)
    {
        $this->remove($offset);
    }

    /**
     * @implements IteratorAggregate
     */
    public function getIterator()
    {
        return new ArrayIterator($this->getAll());
    }

    /**
     * @implements Countable
     */
    public function count()
    {
        return count($this->items);
    }

    /**
     * @implements JSONSerializable
     */
    public function jsonSerialize()
    {
        return $this->items;
    }
}
