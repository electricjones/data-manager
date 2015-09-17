<?php
namespace Michaels\Manager\Traits;

use Michaels\Manager\Exceptions\IncorrectDataException;
use Michaels\Manager\Exceptions\ItemNotFoundException;
use Michaels\Manager\Exceptions\ModifyingProtectedValueException;
use Michaels\Manager\Exceptions\NestingUnderNonArrayException;
use Michaels\Manager\Exceptions\SerializationTypeNotSupportedException;
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

        $this->$repo = is_array($items) ? $items : $this->getArrayableItems($items);

        return $this;
    }

    /**
     * Hydrate with external data
     *
     * @param $type  string    The type of data to be hydrated into the manager
     * @param $data string     The data to be hydrated into the manager
     * @return $this
     * @throws \Michaels\Manager\Exceptions\SerializationTypeNotSupportedException
     */
    public function hydrateFrom($type, $data)
    {
        $decodedData = $this->prepareData($type, $data);
        $this->reset($decodedData);
        return $this;
    }

    /**
     * Hydrate with external data, appending to current data
     *
     * @param $type  string    The type of data to be hydrated into the manager
     * @param $data string     The data to be hydrated into the manager
     * @return $this
     * @throws \Michaels\Manager\Exceptions\SerializationTypeNotSupportedException
     *
     */
    public function appendFrom($type, $data)
    {
        $decodedData = $this->prepareData($type, $data);
        $this->add($decodedData);
        return $this;
    }

    /**
     * Validate and decode non-native data
     * @param $type
     * @param $data
     * @return mixed|null
     */
    protected function prepareData($type, $data)
    {
        // we can possibly do some polymorphism for any other serialization types later
        if (!$this->isFormatSupported($type)) {
            throw new SerializationTypeNotSupportedException("$type serialization is not supported.");
        }

        $decodedData = $this->decodeFromJson($data);

        if (!$this->validateJson($decodedData)) {
            throw new IncorrectDataException("The data is not proper JSON");
        }

        return $decodedData;
    }

    /**
     * Adds a single item.
     *
     * Allow for dot notation (one.two.three) and item nesting.
     *
     * @param string $alias Key to be stored
     * @param mixed $item Value to be stored
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
            if (!isset($loc[$step])) {
                return new NoItemFoundMessage($alias);
            } else {
                $loc = &$loc[$step];
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
        return $this->$repo;
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
     * @return string
     */
    public function __toString()
    {
        return $this->toJson();
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
     * Results array of items from Collection or Arrayable.
     *
     * @param  mixed $items
     * @return array
     */
    protected function getArrayableItems($items)
    {
        if ($items instanceof self || $items instanceof ManagesItemsTrait) {
            return $items->getAll();

        } elseif ($items instanceof Traversable) {
            return iterator_to_array($items);
        }

        return (array)$items;
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

    /**
     * Checks if the input is really a json string
     * @param $data mixed|null
     * @return bool
     */
    protected function validateJson($data)
    {
        if ($data !== "") {
            return (json_last_error() === JSON_ERROR_NONE);
        }
    }

    /**
     * Decodes JSON data to array
     * @param $data string
     * @return mixed|null
     */
    protected function decodeFromJson($data)
    {
        if (is_string($data)) {
            return json_decode($data, true); // true gives us associative arrays
        }

        return "";
    }

    /**
     * Check to make sure the type input is ok. Currently only for JSON.
     * @param $type
     * @return bool
     */
    protected function isFormatSupported($type)
    {
        $type = strtolower(trim($type));
        $supported = ['json'];

        return in_array($type, $supported);
    }
}
