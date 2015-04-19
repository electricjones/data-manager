<?php
namespace Michaels\Manager\Contracts;

/**
 * Contract for Manager instances that return prepared data
 * @package Michaels\Manager
 */
interface PreparedItemManagerInterface
{
    /**
     * Returns a prepared result from the manager.
     *
     * fetch() is responsible for creating the response, validating the response,
     * and throwing InvalidItem or ItemNotFound exceptions when needed.
     *
     * @param $alias
     * @return mixed
     */
    public function fetch($alias);
}
