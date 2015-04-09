<?php
namespace Michaels\Manager;

use ArrayAccess;
use Interop\Container\ContainerInterface;

/**
 * Manages Basic Items
 *
 * @package Michaels\Midas
 */
class DataManager implements ArrayAccess, ContainerInterface, DataManagerInterface
{
    /**
     * Arrayable items
     * @var array
     */
    protected $items = [];

    /**
     * Instantiate the Manager with configuration
     *
     * @param array $items
     */
    public function __construct($items = [])
    {
        $this->items = $items;
    }

    /**
     * Add an item to the manager
     * @param string $alias
     * @param mixed $item
     *
     * @return $this
     */
    public function add($alias, $item = null)
    {
        // Are we adding multiple algorithms?
        if (is_array($alias)) {
            foreach ($alias as $key => $value) {
                $this->add($key, $value);
            }
            return $this;
        }

        // No, we are adding a single algorithm
        if ($this->isNamespaced($alias)) {
            $this->addToNamespace($alias, $item);

        } else {
            $this->items[$alias] = $item;
        }

        return $this;
    }

    /**
     * Get an item from the manager
     *
     * @param string $alias
     * @return array|bool
     */
    public function get($alias)
    {
        if ($this->isNamespaced($alias)) {
            return $this->getFromNamespace($alias, $this->items);
        }

        if (! $this->exists($alias)) {
            return false;
        }

        return $this->items[$alias];
    }

    /**
     * Get all the items from the manager
     *
     * @return array
     */
    public function getAll()
    {
        return $this->getAll();
    }

    /**
     * Get raw collection from manager
     * @return mixed
     */
    public function getRaw()
    {
        return $this->items;
    }

    /**
     * Create or overwrite an item
     *
     * @param string $alias
     * @param mixed $value
     * @return $this
     */
    public function set($alias, $value)
    {
        $this->items[$alias] = $value;
        return $this;
    }

    /**
     * Overwrite all items with an array
     *
     * @param array $items
     * @return $this
     */
    public function reset(array $items = [])
    {
        $this->items = $items;
        return $this;
    }

    /**
     * Clear all items from the manager
     *
     * @return $this
     */
    public function clear()
    {
        $this->items = [];
        return $this;
    }

    /**
     * Delete an individual item
     *
     * @param string $alias
     * @return bool
     */
    public function remove($alias)
    {
        if (isset($this->items[$alias])) {
            $removed = $this->items[$alias];
            unset($this->items[$alias]);
        }

        return (isset($removed)) ? $removed : false;
    }

    /**
     * Check if an item exists in the manager
     *
     * @param string $alias
     * @return bool
     */
    public function exists($alias)
    {
        if ($this->isNamespaced($alias)) {
            return (bool) $this->getFromNamespace($alias, $this->items);
        }

        return (isset($this->items[$alias]));
    }

    /**
     * Check if an item exists in the manager
     *
     * @param string $alias
     * @return bool
     */
    public function has($alias)
    {
        return $this->exists($alias);
    }

    /**
     * Check if an alias is a namespace
     *
     * @param string $alias
     * @return bool|int
     */
    protected function isNamespaced($alias)
    {
        return strpos($alias, ".");
    }

    /**
     * Get an item from array dot notation
     *
     * @param string $chain
     * @param array $loc
     * @return array|bool
     */
    protected function getFromNamespace($chain, &$loc)
    {
        foreach (explode('.', $chain) as $step) {
            if (isset($loc[$step])) {
                $loc = &$loc[$step];
            } else {
                return false;
            }
        }
        return $loc;
    }

    /**
     * Add an item to array dot notation
     *
     * @param string $alias
     * @param mixed $algorithm
     */
    protected function addToNamespace($alias, $algorithm)
    {
        $loc = &$this->items;
        foreach (explode('.', $alias) as $step) {
            $loc = &$loc[$step];
        }
        $loc = $algorithm;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Whether a offset exists
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     * @param mixed $offset <p>
     * An offset to check for.
     * </p>
     * @return boolean true on success or false on failure.
     * </p>
     * <p>
     * The return value will be casted to boolean if non-boolean was returned.
     */
    public function offsetExists($offset)
    {
        return $this->exists($offset);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to retrieve
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     * @param mixed $offset <p>
     * The offset to retrieve.
     * </p>
     * @return mixed Can return all value types.
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to set
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     * @param mixed $offset <p>
     * The offset to assign the value to.
     * </p>
     * @param mixed $value <p>
     * The value to set.
     * </p>
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        $this->set($offset, $value);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to unset
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     * @param mixed $offset <p>
     * The offset to unset.
     * </p>
     * @return void
     */
    public function offsetUnset($offset)
    {
        $this->remove($offset);
    }
}
