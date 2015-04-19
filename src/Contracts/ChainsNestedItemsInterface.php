<?php
namespace Michaels\Manager\Contracts;

/**
 * Class ChainsNestedItemsInterface
 * @package Michaels\Manager\Traits
 */
interface ChainsNestedItemsInterface
{
    public function __get($name);

    public function __call($name, $args);
}
