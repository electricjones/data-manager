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

    protected $silent = false;

    public function __construct(array $items = [])
    {
        $this->items = $items;
    }

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
     * @param      $alias
     * @param null $fallback
     *
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
            throw new ItemNotFoundException();
        }
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
     *
     * @param      $alias
     * @return mixed|bool
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
     *
     * @param $alias
     *
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
}
