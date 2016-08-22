<?php
namespace Michaels\Manager;

use Interop\Container\ContainerInterface;
use Michaels\Manager\Contracts\ManagesItemsInterface;
use Michaels\Manager\Traits\ManagesItemsTrait;

/**
 * Manages deeply nested, complex data.
 *
 * A basic manager class with no pizazz. Simply manages complex data.
 * NOTE: this is not arrayable.
 *
 * @package Michaels\Manager
 */
class BasicManager implements
    ManagesItemsInterface,
    ContainerInterface
{
    use ManagesItemsTrait;

    /**
     * Build a new manager instance
     * @param array $items
     */
    public function __construct($items = [])
    {
        $this->initManager($items);
    }
}
