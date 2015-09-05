<?php
namespace Michaels\Manager\Contracts;

/**
 * Contract for Manager instances that return prepared data
 * @package Michaels\Manager
 */
interface IocManagerInterface extends IocContainerInterface
{

    /**
     * Initializes IoC Container
     * @param array $components
     * @return mixed
     */
    public function initDI(array $components = []);

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
    public function di($alias, $factory);

    /**
     * Turns a dependency into a singleton.
     * @param $alias
     * @return mixed
     */
    public function share($alias);

    /**
     * Begin a configuration chain for needs(), gets(), prepared()
     * @param $alias
     * @return mixed
     */
//    public function configure($alias);

    /**
     * Declare that a dependency needs another dependency
     *
     * If string, need gets added to constructor in order
     * If callable, need gets added to arguments in order
     * If object, need gets passed to needs() in order, if exists
     *
     * @param $alias
     * @return mixed
     */
//    public function needs($alias);

    /**
     * Runs an object through the callable after creation
     * but before return
     *
     * @param callable $processor
     * @return mixed
     */
//    public function prepared(callable $processor);
}
