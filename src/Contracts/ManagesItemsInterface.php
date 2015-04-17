<?php
namespace Michaels\Manager\Contracts;

/**
 * Manages Data Items
 * @package Michaels\Midas
 */
interface ManagesItemsInterface
{
    /**
     * Adds a single item
     *
     * @param string $alias
     * @param mixed  $item
     *
     * @return $this
     */
    public function add($alias, $item = null);

    /**
     * Get a single item
     *
     * @param      $alias
     * @param null $fallback
     *
     * @return mixed
     */
    public function get($alias, $fallback = null);

    /**
     * Return all items as array
     * @return array
     */
    public function getAll();

    /**
     * Confirm or deny that an item exists
     *
     * @param $alias
     *
     * @return bool
     */
    public function exists($alias);

    /**
     * Confirm or deny that an item exists
     *
     * @param $alias
     *
     * @return bool
     */
    public function has($alias);

    /**
     * Update an item
     *
     * @param      $alias
     * @param null $item
     *
     * @return DataManager
     */
    public function set($alias, $item = null);

    /**
     * Delete an item
     *
     * @param $alias
     *
     * @return bool
     */
    public function remove($alias);

    /**
     * Clear the manager
     * @return $this
     */
    public function clear();

    public function reset(array $items);

    public function toJson();

    public function __toString();
}
