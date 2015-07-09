<?php
namespace Michaels\Manager\Traits;

/**
 * Access Deeply nested manager items through magic methods
 *
 * MUST be used with ManagesItemsTrait
 *
 * @implements Michaels\Manager\Contracts\ChainsNestedItemsInterface
 * @package Michaels\Manager
 */
trait ChainsNestedItemsTrait
{
    /**
     * Current level of nesting
     * @var bool|string
     */
    protected $currentLevel = false;

    /**
     * Sets the current level of nesting.
     *
     * @see Michaels\Manager\Contracts\ChainsNestedItemsInterface
     * @param string $name Next level in dot notation to set
     * @return $this
     */
    public function __get($name)
    {
        if ($this->currentLevel === false) {
            $prefix = "";
            $dot = "";
        } else {
            $prefix = $this->currentLevel;
            $dot = ".";
        }

        $this->currentLevel = false;

        return $this->get($prefix . $dot . $name);
    }

    /**
     * Retrieves a value from the manager at the current nest level.
     *
     * @see Michaels\Manager\Contracts\ChainsNestedItemsInterface
     * @param string $name The alias to be retrieved
     * @param array $arguments Not used at present
     * @throws \Michaels\Manager\Exceptions\ItemNotFoundException
     * @return mixed item value
     */
    public function __call($name, $arguments)
    {
        $dot = ($this->currentLevel === false) ? '' : '.';
        $this->currentLevel .= $dot . $name;

        return $this;
    }

    /**
     * Sets an item at the current nest level.
     *
     * @see Michaels\Manager\Contracts\ChainsNestedItemsInterface
     * @param string $key The alias to be retrieved
     * @param mixed $value Value to be set
     * @return $this
     */
    public function __set($key, $value)
    {
        if ($this->currentLevel === false) {
            $prefix = "";
            $dot = "";
        } else {
            $prefix = $this->currentLevel;
            $dot = ".";
        }

        $this->currentLevel = false;

        return $this->add($prefix . $dot . $key, $value);
    }
}
