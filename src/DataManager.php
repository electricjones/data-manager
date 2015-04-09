<?php
namespace Michaels\Manager;

/**
* Manages Basic Items
*
* @package Michaels\Midas
*/
class DataManager
{
    public function add($alias, $item)
    {
        $this->items[$alias] = $item;
        return $this;
    }

    public function get($alias)
    {
        return $this->items[$alias];
    }

    public function getAll()
    {
        return $this->items;
    }
}