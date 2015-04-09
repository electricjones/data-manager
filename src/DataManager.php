<?php
namespace Michaels\Manager;

/**
* Manages Basic Items
*
* @package Michaels\Midas
*/
class DataManager
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
    public function add($alias, $item)
    {
        $this->items[$alias] = $item;
        return $this;
    }

    /**
     * Get a single item
     * @param $alias
     *
     * @return mixed
     */
    public function get($alias)
    {
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
}
