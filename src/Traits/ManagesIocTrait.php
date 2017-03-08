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

    /**
     * Returns the request object with all dependencies
     *
     * Overrides the `get()` method on ManagesItemsTrait
     * Use getRaw() to return the raw value
     *
     * string      Full class name for a new object each time
     * callable    Factory to create new object (passed manager)
     * object      The exact object to be returned
     *
     * @param string $alias
     * @param string|mixed $fallback
     * @return mixed
     * @throws \Exception
     */
    public function get($alias, $fallback = '_michaels_no_fallback')
    {
        // If this is a link, just go back to the master
        $link = $this->getIfExists("$alias");
        if (is_string($link) && strpos($link, '_michaels_link_') !== false) {
            return $this->get(str_replace('_michaels_link_', '', $link));
        }

        // Otherwise, continue
        $shared = $this->getIfExists("_singletons.$alias");

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
                $this->set("_singletons.$alias", $object);
                return $object;
            }
        }
    }

    /**
     * Alias of get() for backwards comparability
     *
     * @param string $alias
     * @param string|mixed $fallback
     * @return mixed
     * @throws \Exception
     */
    public function fetch($alias, $fallback = '_michaels_no_fallback')
    {
        return $this->get($alias, $fallback);
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
     * @return $this
     */
    public function add($alias, $factory = null, array $declared = null)
    {
        // Setup links, if necessary
        if (is_array($alias)) {
            $links = $alias;
            $alias = $alias[0];
            unset($links[0]);
        }

        $this->set("$alias", $factory);

        // Setup any declared dependencies
        if ($declared) {
            $this->set("_declarations.$alias", $declared);
        }

        // Add Links
        if (!empty($links)) {
            foreach ($links as $link) {
                $this->set("$link", "_michaels_link_$alias");
            }
        }

        return $this;
    }

    /**
     * Turns a dependency into a singleton.
     * @param $alias
     * @return mixed
     */
    public function share($alias)
    {
        $this->set("_singletons.$alias", true);
        return $this;
    }

    /**
     * Add a pipeline to to the que
     * @param $alias
     * @param $pipeline
     * @return $this
     */
    public function setup($alias, $pipeline)
    {
        $this->set("_pipelines.$alias", $pipeline);
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
        $factory = $this->getIfExists("$alias");

        /* Manage not founds and fallback */
        if ($factory instanceof NoItemFoundMessage) {
            if ($fallback !== '_michaels_no_fallback') {
                return $fallback;
            } elseif (class_exists($alias)) {
                return new $alias;
            } else {
                throw new ItemNotFoundException("$alias not found");
            }
        }

        /* Get any declared dependencies */
        $declared = $this->getIfExists("_declarations.$alias");
        $dependencies = [];

        // Now setup those dependencies into an array
        if (!$declared instanceof NoItemFoundMessage) {
            $dependencies = array_map(function(&$value) use ($alias) {
                if (is_string($value) && $this->exists("$alias")) {
                    return $this->get($value);
                }
                return $value;
            }, $declared);
        }

        /* Produce the object itself */
        if ($factory instanceof IocContainerInterface) {
            $object = $factory->get($alias);

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
            throw new \Exception("`get()` can only return from strings, callables, or objects");
        }

        /* Run the object through the pipeline, if desired */
        $pipeline = $this->getIfExists("_pipelines.$alias");

        if (!$pipeline instanceof NoItemFoundMessage) {
            /** @var \Closure $pipeline */
            $object = $pipeline($object, $this);
        }

        /* Return the final object */
        return $object;
    }
}
