<?php
namespace Michaels\Manager\Traits;

use Michaels\Manager\Contracts\IocContainerInterface;
use Michaels\Manager\Exceptions\ItemNotFoundException;
use Michaels\Manager\Messages\NoItemFoundMessage;

/**
 * Manages complex, nested data
 *
 * @implements Michaels\Manager\Contracts\ManagesItemsInterface
 * @package Michaels\Manager
 */
trait ManagesIocTrait
{
    use DependsOnManagesItemsTrait;

    /** @var string Name of the ioc manifest */
    protected $nameOfIocManifest = '_diManifest';

    /**
     * Initializes IoC Container
     * @param array $components
     * @return void;
     */
    public function initDi(array $components = [])
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
     * Returns the request object with all dependencies
     *
     * string      Full class name for a new object each time
     * callable    Factory to create new object (passed manager)
     * object      The exact object to be returned
     *
     * @param string $alias
     * @param string|mixed $fallback
     * @return object
     * @throws \Exception
     */
    public function fetch($alias, $fallback = '_michaels_no_fallback')
    {
        $shared = $this->getIfExists($this->nameOfIocManifest . "._singletons.$alias");

        if ($shared instanceof NoItemFoundMessage) {
            // This is not a shared item. We want a new one each time
            return $this->produceDependency($alias, $fallback);
        } else {
            // This is shared, and object has already been cached
            if (is_object($shared)) {
                return $shared;

                // This is shared, but we must produce and cache it
            } else {
                $object = $this->produceDependency($alias, $fallback);
                $this->set($this->nameOfIocManifest . "._singletons.$alias", $object);
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
     * @param array $declared
     * @return void
     */
    public function di($alias, $factory, array $declared = null)
    {
        $this->set($this->nameOfIocManifest . ".$alias", $factory);

        // Setup any declared dependencies
        if ($declared) {
            $this->set($this->nameOfIocManifest . "._declarations.$alias", $declared);
        }
    }

    /**
     * Turns a dependency into a singleton.
     * @param $alias
     * @return mixed
     */
    public function share($alias)
    {
        $this->add($this->nameOfIocManifest . "._singletons.$alias", true);
    }

    public function setup($alias, $pipeline)
    {
        $this->add($this->nameOfIocManifest . "._pipelines.$alias", $pipeline);
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
     * @param string $alias
     * @param mixed|string $fallback
     * @return mixed
     * @throws ItemNotFoundException
     * @throws \Exception
     */
    protected function produceDependency($alias, $fallback = '_michaels_no_fallback')
    {
        /* Get the registered factory (string, closure, object, container, NoItemFoundMessage) */
        $factory = $this->getIfExists($this->nameOfIocManifest . ".$alias");

        /* Manage not founds and fallback */
        if ($factory instanceof NoItemFoundMessage) {
            if ($fallback !== '_michaels_no_fallback') {
                return $fallback;
            } else {
                throw new ItemNotFoundException("$alias not found");
            }
        }

        /* Get any declared dependencies */
        $declared = $this->getIfExists($this->nameOfIocManifest . "._declarations.$alias");
        $dependencies = [];

        // Now setup those dependencies into an array
        if (!$declared instanceof NoItemFoundMessage) {
            $dependencies = array_map(function(&$value) use ($alias) {
                if (is_string($value) && $this->exists($this->nameOfIocManifest . ".$alias")) {
                    return $this->fetch($value);
                }
                return $value;
            }, $declared);
        }

        /* Produce the object itself */
        if ($factory instanceof IocContainerInterface) {
            $object = $factory->fetch($alias);

        } elseif (is_string($factory)) {
            $class = new \ReflectionClass($factory);
            $object = $class->newInstanceArgs($dependencies);

        } elseif (is_callable($factory)) {
            array_unshift($dependencies, $this);
            $object = call_user_func_array($factory, $dependencies);

        } elseif (is_object($factory)) {
            $object = $factory;

            if (method_exists($object, "needs")) {
                call_user_func_array([$object, 'needs'], $dependencies);
            }

        } else {
            throw new \Exception("`fetch()` can only return from strings, callables, or objects");
        }

        /* Run the object through the pipeline, if desired */
        $pipeline = $this->getIfExists($this->nameOfIocManifest . "._pipelines.$alias");

        if (!$pipeline instanceof NoItemFoundMessage) {
            /** @var \Closure $pipeline */
            $object = $pipeline($object, $this);
        }

        /* Return the final object */
        return $object;
    }
}
