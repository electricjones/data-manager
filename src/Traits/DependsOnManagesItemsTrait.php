<?php
namespace Michaels\Manager\Traits;

trait DependsOnManagesItemsTrait
{
    /**
     * Initializes a new manager instance.
     *
     * This is useful for implementations that have their own __construct method
     * This is an alias for reset()
     *
     * @param array $items
     * @return $this
     */
    abstract public function initManager($items = []);

    /**
     * Adds a single item.
     *
     * Allow for dot notation (one.two.three) and item nesting.
     *
     * @param string $alias Key to be stored
     * @param mixed  $item Value to be stored
     * @return $this
     */
    abstract public function add($alias, $item = null);

    /**
     * Get a single item
     *
     * @param string $alias
     * @param null $fallback
     * @throws \Michaels\Manager\Exceptions\ItemNotFoundException If item not found
     * @return mixed
     */
    abstract public function get($alias, $fallback = null);

    /**
     * Return all items as array
     *
     * @return array
     */
     abstract public function getAll();

    /**
     * Return all items as array
     *
     * @return array
     */
    abstract public function all();

    /**
     * Confirm or deny that an item exists
     *
     * @param $alias
     * @return bool
     */
    abstract public function exists($alias);

    /**
     * Confirm or deny that an item exists
     *
     * @param $alias
     * @return bool
     */
    abstract public function has($alias);

    /**
     * Updates an item
     *
     * @param string $alias
     * @param null $item
     *
     * @return $this
     */
    abstract public function set($alias, $item = null);

    /**
     * Return an item if it exists
     * @param $alias
     * @return \Michaels\Manager\Messages\NoItemFoundMessage|mixed
     */
    abstract public function getIfExists($alias);

    /**
     * Deletes an item
     *
     * @param $alias
     * @return void
     */
    abstract public function remove($alias);

    /**
     * Clear the manager
     * @return $this
     */
    abstract public function clear();

    /**
     * Reset the manager with an array of items
     *
     * @param array $items
     * @return mixed
     */
    abstract public function reset($items);

    /**
     * Returns json serialized representation of array of items
     * @param  int $options
     * @return string
     */
    abstract public function toJson($options = 0);

    /**
     * Confirm that manager has no items
     * @return boolean
     */
    abstract public function isEmpty();

    /**
     * When manager instance is used as a string, return json of items
     * @return mixed
     */
    abstract public function __toString();
}
