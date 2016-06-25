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
    abstract public function initManager($items = null);

    /**
     * Hydrate with external data, optionally append
     *
     * @param $data string     The data to be hydrated into the manager
     * @param bool $append When true, data will be appended to the current set
     * @return $this
     */
    abstract public function hydrate($data, $append = false);

    /**
     * Adds a single item.
     *
     * Allow for dot notation (one.two.three) and item nesting.
     *
     * @param string $alias Key to be stored
     * @param mixed $item Value to be stored
     * @return $this
     */
    abstract public function add($alias, $item = null);

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
     * Push a value or values onto the end of an array inside manager
     * @param string $alias The level of nested data
     * @param mixed $value The first value to append
     * @param null|mixed $_ Optional other values to apend
     * @return int Number of items pushed
     * @throws ItemNotFoundException If pushing to unset array
     */
    abstract public function push($alias, $value, $_ = null);

    /**
     * Get a single item
     *
     * @param string $alias
     * @param string $fallback Defaults to '_michaels_no_fallback' so null can be a fallback
     * @throws \Michaels\Manager\Exceptions\ItemNotFoundException If item not found
     * @return mixed
     */
    abstract public function get($alias, $fallback = '_michaels_no_fallback');

    /**
     * Return an item if it exist
     * @param $alias
     * @return NoItemFoundMessage
     */
    abstract public function getIfExists($alias);

    /**
     * Return an item if it exist
     * Alias of getIfExists()
     *
     * @param $alias
     * @return NoItemFoundMessage
     */
    abstract public function getIfHas($alias);

    /**
     * Return all items as array
     *
     * @return array
     */
    abstract public function getAll();

    /**
     * Return all items as array
     * Alias of getAll()
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
     * Alias of exists()
     *
     * @param $alias
     * @return bool
     */
    abstract public function has($alias);

    /**
     * Confirm that manager has no items
     * @return boolean
     */
    abstract public function isEmpty();

    /**
     * Deletes an item
     *
     * @param $alias
     * @return $this
     */
    abstract public function remove($alias);

    /**
     * Clear the manager
     * @return $this
     */
    abstract public function clear();

    /**
     * Reset the manager with an array of items
     * Alias of initManager()
     *
     * @param array $items
     * @return mixed
     */
    abstract public function reset($items);

    /**
     * Get the collection of items as JSON.
     *
     * @param  int $options
     * @return string
     */
    abstract public function toJson($options = 0);

    /**
     * When manager instance is used as a string, return json of items
     * @return string
     */
    abstract public function __toString();
}
