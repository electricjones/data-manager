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
    use DependsOnManagesItemsTrait;

    /**
     * Current level of nesting
     * @var bool|string
     */
    protected $currentLevel = false;

    /**
     * Deletes item at the current level of nesting (and below)
     * @return mixed
     */
    public function drop()
    {
        return $this->remove($this->currentLevel);
    }

    /**
     * Sets the current level of nesting.
     *
     * @see Michaels\Manager\Contracts\ChainsNestedItemsInterface
     * @param string $name Next level in dot notation to set
     * @return $this
     */
    public function __get($name)
    {
        $prefix = $this->buildPrefix();

        return $this->get($prefix . $name);
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
        $prefix = $this->buildPrefix();

        return $this->add($prefix . $key, $value);
    }

    /**
     * Creates a prefix
     * @return array
     */
    protected function buildPrefix()
    {

        if ($this->currentLevel === false) {
            return "";
        } else {
            $prefix = $this->currentLevel;
            $this->currentLevel = false;

            return $prefix . ".";
        }
    }
}
