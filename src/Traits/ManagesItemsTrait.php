<?php
namespace Michaels\Manager\Traits;

use Michaels\Manager\Contracts\ManagesItemsInterface;
use Michaels\Manager\Exceptions\ItemNotFoundException;
use Michaels\Manager\Exceptions\ModifyingProtectedValueException;
use Michaels\Manager\Exceptions\NestingUnderNonArrayException;
use Michaels\Manager\Messages\NoItemFoundMessage;
use Traversable;

/**
 * Manages complex, nested data
 *
 * @implements Michaels\Manager\Contracts\ManagesItemsInterface
 * @package Michaels\Manager
 */
trait ManagesItemsTrait
{
    /**
     * Name of the property to hold the data items. Internal use only
     * @var string
     */
    protected $nameOfItemsRepository = 'items';
    protected $protectedItems = [];

    /* The user may also set $dataItemsName */

    /**
     * Initializes a new manager instance.
     *
     * This is useful for implementations that have their own __construct method
     * This is an alias for reset()
     *
     * @param array $items
     */
    public function initManager($items = [])
    {
        if (property_exists($this, 'dataItemsName')) {
            $this->setItemsName($this->dataItemsName);
        }

        $repo = $this->getItemsName();
        $this->$repo = is_array($items) ? $items : $this->getArrayableItems($items);
    }

    /**
     * Adds a single item.
     *
     * Allow for dot notation (one.two.three) and item nesting.
     *
     * @param string $alias Key to be stored
     * @param mixed  $item Value to be stored
     * @return $this
     */
    public function add($alias, $item = null)
    {
        $this->checkIfProtected($alias);

        // Are we adding multiple items?
        if (is_array($alias)) {
            foreach ($alias as $key => $value) {
                $this->add($key, $value);
            }
            return $this;
        }

        // No, we are adding a single item
        $repo = $this->getItemsName();
        $loc = &$this->$repo;

        $pieces = explode('.', $alias);
        $currentLevel = 1;
        $nestLevels = count($pieces) - 1;

        foreach ($pieces as $step) {
            // Make sure we are not trying to nest under a non-array. This is gross
            // https://github.com/chrismichaels84/data-manager/issues/6

            // 1. Not at the last level (the one with the desired value),
            // 2. The nest level is already set,
            // 3. and is not an array
            if ($nestLevels > $currentLevel && isset($loc[$step]) && !is_array($loc[$step])) {
                throw new NestingUnderNonArrayException();
            }

            $loc = &$loc[$step];
            $currentLevel++;
        }
        $loc = $item;

        return $this;
    }

    /**
     * Get a single item
     *
     * @param string $alias
     * @param string $fallback Defaults to '_michaels_no_fallback' so null can be a fallback
     * @throws \Michaels\Manager\Exceptions\ItemNotFoundException If item not found
     * @return mixed
     */
    public function get($alias, $fallback = '_michaels_no_fallback')
    {
        $item = $this->getIfExists($alias);

        // The item was not found
        if ($item instanceof NoItemFoundMessage) {
            if ($fallback !== '_michaels_no_fallback') {
                return $fallback;
            } else {
                throw new ItemNotFoundException("$alias not found");
            }
        }

        return $item;
    }

    public function getIfExists($alias)
    {
        $repo = $this->getItemsName();
        $loc = &$this->$repo;
        foreach (explode('.', $alias) as $step) {
            if (!isset($loc[$step])) {
                return new NoItemFoundMessage($alias);
            } else {
                $loc = &$loc[$step];
            }
        }
        return $loc;
    }

    public function getIfHas($alias)
    {
        return $this->getIfExists($alias);
    }

    /**
     * Return all items as array
     *
     * @return array
     */
    public function getAll()
    {
        $repo = $this->getItemsName();
        return $this->$repo;
    }

    /**
     * Return all items as array
     *
     * @return array
     */
    public function all()
    {
        return $this->getAll();
    }

    /**
     * Confirm or deny that an item exists
     *
     * @param $alias
     * @return bool
     */
    public function exists($alias)
    {
        $repo = $this->getItemsName();
        $loc = &$this->$repo;
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
     *
     * @param $alias
     * @return bool
     */
    public function has($alias)
    {
        return $this->exists($alias);
    }

    /**
     * Updates an item
     *
     * @param string $alias
     * @param null $item
     *
     * @return $this
     */
    public function set($alias, $item = null)
    {
        return $this->add($alias, $item);
    }

    /**
     * Deletes an item
     *
     * @param $alias
     * @return void
     */
    public function remove($alias)
    {
        $repo = $this->getItemsName();
        $loc = &$this->$repo;
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
        $repo = $this->getItemsName();
        $this->$repo = [];
        return $this;
    }

    /**
     * Reset the manager with an array of items
     *
     * @param array $items
     * @return mixed
     */
    public function reset($items)
    {
        $this->initManager($items);
    }

    public function protect($item)
    {
        array_push($this->protectedItems, $item);
        return $this;
    }

    protected function checkIfProtected($item)
    {
        $this->performProtectedCheck($item);

        if (!is_string($item)) {
            return;
        }

        $pieces = explode(".", $item);
        $current = $pieces[0];
        $i = 0;

        foreach ($pieces as $piece) {
            $this->performProtectedCheck($current);
            if (isset($pieces[$i+1])) {
                $current .= $current . "." . $pieces[$i+1];
                $i++;
            }
        }
    }

    /**
     * Get the collection of items as JSON.
     *
     * @param  int  $options
     * @return string
     */
    public function toJson($options = 0)
    {
        return json_encode($this->getAll(), $options);
    }

    /**
     * Confirm that manager has no items
     * @return boolean
     */
    public function isEmpty()
    {
        $repo = $this->getItemsName();
        return empty($this->$repo);
    }

    /**
     * Returns the name of the property that holds data items
     * @return string
     */
    public function getItemsName()
    {
        return $this->nameOfItemsRepository;
    }

    /**
     * Sets the name of the property that holds data items
     * @param $nameOfItemsRepository
     * @return $this
     */
    public function setItemsName($nameOfItemsRepository)
    {
        $this->nameOfItemsRepository = $nameOfItemsRepository;
        return $this;
    }

    /**
     * When manager instance is used as a string, return json of items
     * @return mixed
     */
    public function __toString()
    {
        return $this->toJson();
    }

    /**
     * Results array of items from Collection or Arrayable.
     *
     * @param  mixed  $items
     * @return array
     */
    protected function getArrayableItems($items)
    {
        if ($items instanceof self || $items instanceof ManagesItemsTrait) {
            return $items->getAll();

        } elseif ($items instanceof Traversable) {
            return iterator_to_array($items);
        }

        return (array) $items;
    }

    /**
     * @param $item
     */
    protected function performProtectedCheck($item)
    {
// Explicitly protected
        if (in_array($item, $this->protectedItems)) {
            throw new ModifyingProtectedValueException("Cannot access $item because it is protected");
        }
    }
}
