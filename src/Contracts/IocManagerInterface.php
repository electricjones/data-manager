<?php
namespace Michaels\Manager\Contracts;

/**
 * Contract for Manager instances that manage dependencies
 * @package Michaels\Manager
 */
interface IocManagerInterface extends IocContainerInterface
{

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
}
