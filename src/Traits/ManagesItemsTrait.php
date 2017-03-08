<?php
namespace Michaels\Manager\Traits;

use Michaels\Manager\Exceptions\ItemNotFoundException;
use Michaels\Manager\Exceptions\ModifyingProtectedValueException;
use Michaels\Manager\Exceptions\NestingUnderNonArrayException;
use Michaels\Manager\Helpers;
use Michaels\Manager\Messages\NoItemFoundMessage;

/**
 * Manages complex, nested data
 *
 * @implements Michaels\Manager\Contracts\ManagesItemsInterface
 * @package Michaels\Manager
 */
trait ManagesItemsTrait
{
    /**
     * The items stored in the manager
     * @var array $items Items governed by manager
     */
    protected $_items = [];

    /**
     * Name of the property to hold the data items. Internal use only
     * @var string
     */
    protected $nameOfItemsRepository = '_items';

    /** @var array Array of protected aliases */
    protected $protectedItems = [];

    /* The user may also set $dataItemsName */

    /**
     * Initializes a new manager instance.
     *
     * This is useful for implementations that have their own __construct method
     * This is an alias for reset()
     *
     * @param array $items
     * @return $this
     */
    public function initManager($items = null)
    {
        if (property_exists($this, 'dataItemsName')) {
            $this->setItemsName($this->dataItemsName);
        }

        $repo = $this->getItemsName();

        if (!isset($this->$repo)) {
            $this->$repo = [];
        }

        if (is_null($items)) {
            return $this;
        }

        $this->$repo = is_array($items) ? $items : Helpers::getArrayableItems($items);

        return $this;
    }

    /**
     * Hydrate with external data, optionally append
     *
     * @param $data array     The data to be hydrated into the manager
     * @param bool $append When true, data will be appended to the current set
     * @return $this
     */
    public function hydrate($data, $append = false)
    {
        if ($append) {
            $this->addItem($data);
        } else {
            $this->reset($data);
        }

        return $this;
    }

    /**
     * Adds a single item.
     *
     * Allow for dot notation (one.two.three) and item nesting.
     *
     * @param string $alias Key to be stored
     * @param mixed $item Value to be stored
     * @param array $options THIS IS NOT USED HERE
     * @return $this
     */
    public function add($alias, $item = null, array $options = null)
    {
        $this->addItem($alias, $item, $options);
    }

    /**
     * Adds a single item.
     *
     * Allow for dot notation (one.two.three) and item nesting.
     *
     * @param string $alias Key to be stored
     * @param mixed $item Value to be stored
     * @param array $options THIS IS NOT USED HERE
     * @return $this
     */
    protected function addItem($alias, $item = null, array $options = null)
    {
        $this->checkIfProtected($alias);

        // Are we adding multiple items?
        if (is_array($alias)) {
            foreach ($alias as $key => $value) {
                $this->addItem($key, $value);
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
     * Updates an item
     *
     * @param string $alias
     * @param null $item
     *
     * @return $this
     */
    public function set($alias, $item = null)
    {
        return $this->addItem($alias, $item);
    }

    /**
     * Push a value or values onto the end of an array inside manager
     * @param string $alias The level of nested data
     * @param mixed $value The first value to append
     * @param null|mixed $other Optional other values to apend
     * @return int Number of items pushed
     * @throws ItemNotFoundException If pushing to unset array
     */
    public function push($alias, $value, $other = null)
    {
        if (isset($other)) {
            $values = func_get_args();
            array_shift($values);
        } else {
            $values = [$value];
        }

        $array = $this->get($alias);

        if (!is_array($array)) {
            throw new NestingUnderNonArrayException("You may only push items onto an array");
        }

        foreach ($values as $value) {
            array_push($array, $value);
        }
        $this->set($alias, $array);

        return count($values);
    }

    /**
     * Get a single item
     *
     * Note: When editing, update ManagesIocTrait::getRaw()
     *
     * @param string $alias
     * @param string $fallback Defaults to '_michaels_no_fallback' so null can be a fallback
     * @throws \Michaels\Manager\Exceptions\ItemNotFoundException If item not found
     * @return mixed
     */
    public function get($alias, $fallback = '_michaels_no_fallback')
    {
        return $this->getRaw($alias, $fallback);
    }

    /**
     * Get a single item
     *
     * @param string $alias
     * @param string $fallback Defaults to '_michaels_no_fallback' so null can be a fallback
     * @throws \Michaels\Manager\Exceptions\ItemNotFoundException If item not found
     * @return mixed
     */
    public function getRaw($alias, $fallback = '_michaels_no_fallback')
    {
        $item = $this->getIfExists($alias);

        // The item was not found
        if ($item instanceof NoItemFoundMessage) {
            if ($fallback !== '_michaels_no_fallback') {
                $item = $fallback;
            } else {
                throw new ItemNotFoundException("$alias not found");
            }
        }

        return $this->prepareReturnedValue($item);
    }

    /**
     * Return an item if it exist
     * @param $alias
     * @return NoItemFoundMessage
     */
    public function getIfExists($alias)
    {
        $repo = $this->getItemsName();
        $loc = &$this->$repo;
        foreach (explode('.', $alias) as $step) {
            if (array_key_exists($step, $loc)) {
                $loc = &$loc[$step];
            } else {
                return new NoItemFoundMessage($alias);
            }
        }
        return $loc;
    }

    /**
     * Return an item if it exist
     * Alias of getIfExists()
     *
     * @param $alias
     * @return NoItemFoundMessage
     */
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
        return $this->prepareReturnedValue($this->$repo);
    }

    /**
     * Return all items as array
     * Alias of getAll()
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
     * Alias of exists()
     *
     * @param $alias
     * @return bool
     */
    public function has($alias)
    {
        return $this->exists($alias);
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
     * Deletes an item
     *
     * @param $alias
     * @return $this
     */
    public function remove($alias)
    {
        $repo = $this->getItemsName();
        $loc = &$this->$repo;
        $parts = explode('.', $alias);

        while (count($parts) > 1) {
            $step = array_shift($parts);
            if (isset($loc[$step]) && is_array($loc[$step])) {
                $loc = &$loc[$step];
            }
        }

        unset($loc[array_shift($parts)]);
        return $this;
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
     * Alias of initManager()
     *
     * @param array $items
     * @return mixed
     */
    public function reset($items)
    {
        $this->initManager($items);
    }

    /**
     * Guard an alias from being modified
     * @param $item
     * @return $this
     */
    public function protect($item)
    {
        array_push($this->protectedItems, $item);
        return $this;
    }

    /**
     * Merge a set of defaults with the current items
     * @param array $defaults
     * @return $this
     */
    public function loadDefaults(array $defaults)
    {
        $this->mergeDefaults($defaults);
        return $this;
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
     * Get the collection of items as JSON.
     *
     * @param  int $options
     * @return string
     */
    public function toJson($options = 0)
    {
        return json_encode($this->getAll(), $options);
    }

    /**
     * When manager instance is used as a string, return json of items
     * @return string
     */
    public function __toString()
    {
        return $this->toJson();
    }

    /**
     * Prepare the returned value
     * @param $value
     * @return mixed
     */
    protected function prepareReturnedValue($value)
    {
        // Are we looking for Collections?
        if (method_exists($this, 'toCollection')) {
            return $this->toCollection($value);
        }

        // No? Just return the value
        return $value;
    }

    /**
     * Recursively merge defaults array and items array
     * @param array $defaults
     * @param string $level
     */
    protected function mergeDefaults(array $defaults, $level = '')
    {
        foreach ($defaults as $key => $value) {
            if (is_array($value)) {
                $original = $this->getIfExists(ltrim("$level.$key", "."));
                if (is_array($original)) {
                    $this->mergeDefaults($value, "$level.$key");
                } elseif ($original instanceof NoItemFoundMessage) {
                    $this->set(ltrim("$level.$key", "."), $value);
                }
            } else {
                if (!$this->exists(ltrim("$level.$key", "."))) {
                    $this->set(ltrim("$level.$key", "."), $value);
                }
            }
        }
    }

    /**
     * Cycle through the nests to see if an item is protected
     * @param $item
     */
    protected function checkIfProtected($item)
    {
        $this->performProtectedCheck($item);

        if (!is_string($item)) {
            return;
        }

        $prefix = $item;
        while (false !== $pos = strrpos($prefix, '.')) {
            $prefix = substr($item, 0, $pos);
            $this->performProtectedCheck($prefix);
            $prefix = rtrim($prefix, '.');
        }
    }

    /**
     * Throws an exception if item is protected
     * @param $item
     */
    protected function performProtectedCheck($item)
    {
        if (in_array($item, $this->protectedItems)) {
            throw new ModifyingProtectedValueException("Cannot access $item because it is protected");
        }
    }
}
