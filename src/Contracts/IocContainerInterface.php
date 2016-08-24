<?php
namespace Michaels\Manager\Contracts;

/**
 * Contract for Manager instances that return prepared data
 * Does NOT include methods for managing dependencies
 *
 * @package Michaels\Manager
 */
interface IocContainerInterface
{
    /**
     * Returns the request object with all dependencies
     *
     * @param string $alias
     * @return mixed
     */
    public function get($alias);
}
