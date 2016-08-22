<?php
namespace Michaels\Manager\Traits;

use ArrayIterator;

/**
 * Class ArrayableTrait
 * @package Michaels\Manager\Traits
 */
trait ArrayableTrait
{
    use DependsOnManagesItemsTrait;

    /**
     * @implements ArrayAccess
     * @param $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return $this->exists($offset);
    }

    /**
     * @implements ArrayAccess
     * @param $offset
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * @implements ArrayAccess
     * @param $offset
     * @param $value
     */
    public function offsetSet($offset, $value)
    {
        $this->set($offset, $value);
    }

    /**
     * @implements ArrayAccess
     * @param $offset
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
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->getAll();
    }
}
