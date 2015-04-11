<?php
namespace Michaels\Manager;

/**
* Manages Data Items
*
* @package Michaels\Midas
*/
class DataManager implements DataManagerInterface
{
    /**
     * @var array $items Items governed by manager
     */
    protected $items;

    /**
     * Adds a single item
     *
     * @param string $alias
     * @param mixed $item
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
        $this->items[$alias] = $item;

        return $this;
    }

    /**
     * Get a single item
     *
     * @param      $alias
     * @param null $fallback
     *
     * @return mixed
     * @throws ItemNotFoundException
     */
    public function get($alias, $fallback = null)
    {
        $exists = $this->exists($alias);
        if (!$exists && !is_null($fallback)) {
            return $fallback;
        } elseif (!$exists) {
            throw new ItemNotFoundException();
        }

        return $this->items[$alias];
    }

    /**
     * Return all items as array
     * @return array
     */
    public function getAll()
    {
        return $this->items;
    }

    /**
     * Confirm or deny that an item exists
     * @param $alias
     *
     * @return bool
     */
    public function exists($alias)
    {
        return (isset($this->items[$alias]));
    }

    /**
     * Confirm or deny that an item exists
     * @param $alias
     *
     * @return bool
     */
    public function has($alias)
    {
        return $this->exists($alias);
    }

    /**
     * Update an item
     * @param      $alias
     * @param null $item
     *
     * @return DataManager
     */
    public function set($alias, $item = null)
    {
        return $this->add($alias, $item);
    }

    /**
     * Delete an item
     * @param $alias
     *
     * @return void
     */
    public function remove($alias)
    {
        if ($this->exists($alias)) {
            unset($this->items[$alias]);
        }
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
}
