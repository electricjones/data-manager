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
     * @return $this
     */
    public function initManager($items = null);

    /**
     * Hydrate with external data, optionally append
     *
     * @param $data string     The data to be hydrated into the manager
     * @param bool $append When true, data will be appended to the current set
     * @return $this
     */
    public function hydrate($data, $append = false);

    /**
     * Adds a single item.
     *
     * Allow for dot notation (one.two.three) and item nesting.
     *
     * @param string $alias Key to be stored
     * @param mixed $item Value to be stored
     * @param array $options Only used for some extra features
     * @return $this
     */
    public function add($alias, $item = null, array $options = null);

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
     * Push a value or values onto the end of an array inside manager
     * @param string $alias The level of nested data
     * @param mixed $value The first value to append
     * @param null|mixed $other Optional other values to amend
     * @return int Number of items pushed
     * @throws ItemNotFoundException If pushing to unset array
     */
    public function push($alias, $value, $other = null);

    /**
     * Get a single item
     *
     * @param string $alias
     * @param string $fallback Defaults to '_michaels_no_fallback' so null can be a fallback
     * @throws \Michaels\Manager\Exceptions\ItemNotFoundException If item not found
     * @return mixed
     */
    public function get($alias, $fallback = '_michaels_no_fallback');

    /**
     * Return an item if it exist
     * @param $alias
     * @return NoItemFoundMessage
     */
    public function getIfExists($alias);

    /**
     * Return an item if it exist
     * Alias of getIfExists()
     *
     * @param $alias
     * @return NoItemFoundMessage
     */
    public function getIfHas($alias);

    /**
     * Return all items as array
     *
     * @return array
     */
    public function getAll();

    /**
     * Return all items as array
     * Alias of getAll()
     * @return array
     */
    public function all();

    /**
     * Confirm or deny that an item exists
     *
     * @param $alias
     * @return bool
     */
    public function exists($alias);

    /**
     * Confirm or deny that an item exists
     * Alias of exists()
     *
     * @param $alias
     * @return bool
     */
    public function has($alias);

    /**
     * Confirm that manager has no items
     * @return boolean
     */
    public function isEmpty();

    /**
     * Deletes an item
     *
     * @param $alias
     * @return $this
     */
    public function remove($alias);

    /**
     * Clear the manager
     * @return $this
     */
    public function clear();

    /**
     * Reset the manager with an array of items
     * Alias of initManager()
     *
     * @param array $items
     * @return mixed
     */
    public function reset($items);

    /**
     * Get the collection of items as JSON.
     *
     * @param  int $options
     * @return string
     */
    public function toJson($options = 0);

    /**
     * When manager instance is used as a string, return json of items
     * @return string
     */
    public function __toString();
}
