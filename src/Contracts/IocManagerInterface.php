<?php
namespace Michaels\Manager\Contracts;

/**
 * Contract for Manager instances that manage dependencies
 * @package Michaels\Manager
 */
interface IocManagerInterface extends IocContainerInterface
{

    /**
     * Initializes IoC Container
     * @param array $components
     * @return mixed
     */
    public function initDi(array $components = []);

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
     * @return $this
     */
    public function di($alias, $factory);

    /**
     * Turns a dependency into a singleton.
     * @param $alias
     * @return mixed
     */
    public function share($alias);

    /**
     * Add a pipeline to to the que for a specific item
     * @param $alias
     * @param $pipeline
     * @return void
     */
    public function setup($alias, $pipeline);

    /**
     * Returns the entire IoC Manifest
     * @return array
     */
    public function getIocManifest();
}
