<?php
namespace Michaels\Manager;

/**
 * Manages Basic Items
 * @package Michaels\Midas
 */
interface DataManagerInterface
{
    /**
     * Add an item to the manager

     *
*@param string $alias
     * @param mixed $item
     *
*@return $this
     */
    public function add($alias, $item = null);

    /**
     * Get an item from the manager
     *
     * @param string $alias
     *
     * @return array|bool
     */
    public function get($alias);

    /**
     * Get all the items from the manager
     * @return array
     */
    public function getAll();

    /**
     * Get raw collection from manager
     * @return mixed
     */
    public function getRaw();

    /**
     * Create or overwrite an item
     *
     * @param string $alias
     * @param mixed  $value
     *
     * @return $this
     */
    public function set($alias, $value);

    /**
     * Overwrite all items with an array
     *
     * @param array $items
     *
     * @return $this
     */
    public function reset(array $items = []);

    /**
     * Clear all items from the manager
     * @return $this
     */
    public function clear();

    /**
     * Delete an individual item
     *
     * @param string $alias
     *
     * @return bool
     */
    public function remove($alias);

    /**
     * Check if an item exists in the manager
     *
     * @param string $alias
     *
     * @return bool
     */
    public function exists($alias);

    /**
     * Check if an item exists in the manager
     *
     * @param string $alias
     *
     * @return bool
     */
    public function has($alias);
}
