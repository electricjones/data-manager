<?php
namespace Michaels\Manager\Contracts;

/**
 * Contract for Manager instances that return prepared data
 * @package Michaels\Manager
 */
interface IocContainerInterface
{
    /**
     * Returns the request object with all dependencies
     *
     * @param string $alias
     * @return object
     */
    public function fetch($alias);
}
