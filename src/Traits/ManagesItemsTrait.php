<?php
namespace Michaels\Manager\Traits;

use Michaels\Manager\Exceptions\ItemNotFoundException;

/**
 * Manages complex, nested data
 *
 * @implements Michaels\Manager\Contracts\ManagesItemsInterface
 * @package Michaels\Manager
 */
trait ManagesItemsTrait
{
    /**
     * The items stored in the manager
     * @var array $items Items governed by manager
     */
    protected $items;

    /**
     * Build a new manager instance
     * @param array $items
     */
    public function __construct(array $items = [])
    {
        $this->items = $items;
    }

    /**
     * Initializes a new manager instance.
     *
     * This is useful for implementations that have their own __construct method
     * This is an alias for reset()
     *
     * @param array $items
     */
    public function initManager(array $items = [])
    {
        $this->items = $items;
    }

    /**
     * Adds a single item.
     *
     * Allow for dot notation (one.two.three) and item nesting.
     *
     * @param string $alias Key to be stored
     * @param mixed  $item Value to be stored
     * @return $this
     */
    public function add($alias, $item = null)
    {
        // Are we adding multiple items?
        if (is_array($alias)) {
            foreach ($alias as $key => $value) {
                $this->add($key, $value);
            }
            return $this;
        }

        // No, we are adding a single item
        $loc = &$this->items;
        foreach (explode('.', $alias) as $step) {
            $loc = &$loc[$step];
        }
        $loc = $item;

        return $this;
    }

    /**
     * Get a single item
     *
     * @param string $alias
     * @param null $fallback
     * @throws \Michaels\Manager\Exceptions\ItemNotFoundException If item not found
     * @return mixed
     */
    public function get($alias, $fallback = null)
    {
        // Check for existence
        $exists = $this->exists($alias);

        // The item does exist, return the value
        if ($exists) {
            $loc = &$this->items;
            foreach (explode('.', $alias) as $step) {
                $loc = &$loc[$step];
            }
            return $loc;

        // The item does not exist, but we have a fallback
        } elseif ($fallback !== null) {
            return $fallback;

        // The item does not exist, and there is no fallback
        } else {
            throw new ItemNotFoundException("$alias not found");
        }
    }

    /**
     * Return all items as array
     *
     * @return array
     */
    public function getAll()
    {
        return $this->items;
    }

    /**
     * Confirm or deny that an item exists
     *
     * @param $alias
     * @return bool
     */
    public function exists($alias)
    {
        $loc = &$this->items;
        foreach (explode('.', $alias) as $step) {
            if (!isset($loc[$step])) {
                return false;
            } else {
                $loc = &$loc[$step];
            }
        }
        return true;
    }

    /**
     * Confirm or deny that an item exists
     *
     * @param $alias
     * @return bool
     */
    public function has($alias)
    {
        return $this->exists($alias);
    }

    /**
     * Updates an item
     *
     * @param string $alias
     * @param null $item
     *
     * @return $this
     */
    public function set($alias, $item = null)
    {
        return $this->add($alias, $item);
    }

    /**
     * Deletes an item
     *
     * @param $alias
     * @return void
     */
    public function remove($alias)
    {
        $loc = &$this->items;
        $parts = explode('.', $alias);

        while (count($parts) > 1) {
            $step = array_shift($parts);
            if (isset($loc[$step]) && is_array($loc[$step])) {
                $loc =& $loc[$step];
            }
        }

        unset($loc[array_shift($parts)]);
    }

    /**
     * Clear the manager
     * @return $this
     */
    public function clear()
    {
        $this->items = [];
        return $this;
    }

    /**
     * Reset the manager with an array of items
     *
     * @param array $items
     * @return mixed
     */
    public function reset(array $items)
    {
        $this->items = $items;
    }

    /**
     * Returns json serialized representation of array of items
     * @return string
     */
    public function toJson()
    {
        return json_encode($this->getAll());
    }

    /**
     * Confirm that manager has no items
     * @return boolean
     */
    public function isEmpty()
    {
        return empty($this->items);
    }

    /**
     * When manager instance is used as a string, return json of items
     * @return mixed
     */
    public function __toString()
    {
        return $this->toJson();
    }
}
