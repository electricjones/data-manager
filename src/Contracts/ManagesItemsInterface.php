<?php
namespace Michaels\Manager\Contracts;

/**
 * API Methods for managing items
 *
 * See src/Traits/ManagesItemsTrait.php for implementation example
 */
interface ManagesItemsInterface
{
    /**
     * Initializes a new manager instance.
     *
     * This is useful for implementations that have their own __construct method
     * This is an alias for reset()
     *
     * @param array $items
     */
    public function initManager(array $items = []);

    /**
     * Adds a single item.
     *
     * Allow for dot notation (one.two.three) and item nesting.
     *
     * @param string $alias Key to be stored
     * @param mixed  $item Value to be stored
     * @return $this
     */
    public function add($alias, $item = null);

    /**
     * Get a single item
     *
     * @param string $alias
     * @param null $fallback
     * @throws \Michaels\Manager\Exceptions\ItemNotFoundException If item not found
     * @return mixed
     */
    public function get($alias, $fallback = null);

    /**
     * Return all items as array
     *
     * @return array
     */
    public function getAll();

    /**
     * Confirm or deny that an item exists
     *
     * @param $alias
     * @return bool
     */
    public function exists($alias);

    /**
     * Confirm or deny that an item exists
     *
     * @param $alias
     * @return bool
     */
    public function has($alias);

    /**
     * Updates an item
     *
     * @param string $alias
     * @param null $item
     *
     * @return $this
     */
    public function set($alias, $item = null);

    /**
     * Deletes an item
     *
     * @param $alias
     * @return void
     */
    public function remove($alias);

    /**
     * Clear the manager
     * @return $this
     */
    public function clear();

    /**
     * Reset the manager with an array of items
     *
     * @param array $items
     * @return mixed
     */
    public function reset(array $items);

    /**
     * Returns json serialized representation of array of items
     * @return string
     */
    public function toJson();

    /**
     * Confirm that manager has no items
     * @return boolean
     */
    public function isEmpty();

    /**
     * When manager instance is used as a string, return json of items
     * @return mixed
     */
    public function __toString();
}
