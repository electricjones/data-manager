<?php
namespace Michaels\Manager\Traits;

use ArrayIterator;

/**
 * Class ArrayableTrait
 * @package Michaels\Manager\Traits
 */
trait ArrayableTrait
{
    /**
     * @implements ArrayAccess
     * @param $offset
     * @return
     * @throws \Michaels\Manager\Exceptions\DependencyNotMetException
     */
    public function offsetExists($offset)
    {
        return $this->exists($offset);
    }

    /**
     * @implements ArrayAccess
     * @param $offset
     * @return
     * @throws \Michaels\Manager\Exceptions\DependencyNotMetException
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * @implements ArrayAccess
     * @param $offset
     * @param $value
     * @throws \Michaels\Manager\Exceptions\DependencyNotMetException
     */
    public function offsetSet($offset, $value)
    {
        $this->set($offset, $value);
    }

    /**
     * @implements ArrayAccess
     * @param $offset
     * @throws \Michaels\Manager\Exceptions\DependencyNotMetException
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
        return count($this->getAll());
    }

    /**
     * @implements JSONSerializable
     */
    public function jsonSerialize()
    {
        return $this->getAll();
    }
}
