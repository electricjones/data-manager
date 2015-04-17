<?php
namespace Michaels\Manager\Contracts;

interface PreparedItemManagerInterface
{
    /**
     * Returns a prepared result from the manager.
     *
     * fetch() is responsible for creating the response, validating the response,
     * and throwing InvalidItem or ItemNotFound exceptions when needed.
     *
     * @param $alias
     *
     * @return mixed
     */
    public function fetch($alias);
}
