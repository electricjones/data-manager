<?php
namespace Michaels\Manager\Traits;

use Michaels\Manager\Messages\NoItemFoundMessage;

/**
 * Manages complex, nested data
 *
 * @implements Michaels\Manager\Contracts\ManagesItemsInterface
 * @package Michaels\Manager
 */
trait ManagesIocTrait
{
    protected $nameOfIocManifest = '_diManifest';

    /**
     * Initializes IoC Container
     * @param array $components
     */
    public function initDI(array $components = [])
    {
        $this->initManager();
        $this->add($this->nameOfIocManifest, $components);
    }

    /**
     * Returns the entire IoC Manifest
     * @return array
     */
    public function getIocManifest()
    {
        $manifest = $this->getIfExists($this->nameOfIocManifest);

        return ($manifest instanceof NoItemFoundMessage) ? [] : $manifest;
    }

    /**
     * Returns the entire IoC Manifest
     * @return array
     */
    public function getIocManifest()
    {
        $manifest = $this->getIfExists($this->nameOfIocManifest);

        return ($manifest instanceof NoItemFoundMessage) ? [] : $manifest;
    }

    /**
     * Returns the request object with all dependencies
     *
     * string      Full class name for a new object each time
     * callable    Factory to create new object (passed manager)
     * object      The exact object to be returned
     *
     * @param string $alias
     * @param string|object|callable $fallback
     * @return object
     * @throws \Exception
     */
    public function fetch($alias, $fallback = null)
    {
        $shared = $this->getIfExists($this->nameOfIocManifest . "singletons.$alias");

        if ($shared instanceof NoItemFoundMessage) {
            // This is not a shared item, new one each time
            return $this->produceDependency($alias);
        } else {
            // This is shared, and object has already been cached
            if (is_object($shared)) {
                return $shared;

                // This is shared, but we must produce and cache it
            } else {
                $object = $this->produceDependency($alias);
                $this->set($this->nameOfIocManifest . "_singletons.$alias", $object);
                return $object;
            }
        }
    }

    /**
     * Adds a dependency to the manager
     *
     * $factory can be a:
     *      string      Full class name for a new object each time
     *      callable    Factory to create new object (passed manager)
     *      object      The exact object to be returned
     *
     * @param string $alias
     * @param callable|string|object $factory
     * @return object
     */
    public function di($alias, $factory)
    {
        $this->set($this->nameOfIocManifest . ".$alias", $factory);
    }

    /**
     * Turns a dependency into a singleton.
     * @param $alias
     * @return mixed
     */
    public function share($alias)
    {
        $this->add($this->nameOfIocManifest . "_singletons.$alias", false);
    }

    /**
     * Returns the name of the property that holds data items
     * @return string
     */
    public function getDiItemsName()
    {
        return $this->nameOfIocManifest;
    }

    /**
     * Sets the name of the property that holds data items
     * @param $nameOfItemsRepository
     * @return $this
     */
    public function setDiItemsName($nameOfItemsRepository)
    {
        $this->nameOfIocManifest = $nameOfItemsRepository;
        return $this;
    }

    /**
     * Produces the object from an alias
     * @param $alias
     * @return mixed
     * @throws \Exception
     * @throws \Michaels\Manager\Exceptions\ItemNotFoundException
     */
    protected function produceDependency($alias)
    {
        $factory = $this->get($this->nameOfIocManifest . ".$alias");

        if (is_string($factory)) {
            return new $factory();

        } elseif (is_callable($factory)) {
            return call_user_func($factory);

        } elseif (is_object($factory)) {
            return $factory;

        } else {
            throw new \Exception("`fetch()` can only return from strings, callables, or objects");
        }
    }
}
