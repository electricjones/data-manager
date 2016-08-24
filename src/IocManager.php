<?php
namespace Michaels\Manager;

use Interop\Container\ContainerInterface;
use Michaels\Manager\Contracts\IocManagerInterface;
use Michaels\Manager\Contracts\ManagesItemsInterface;
use Michaels\Manager\Traits\ManagesIocTrait;
use Michaels\Manager\Traits\ManagesItemsTrait;

/**
 * Manages deeply nested, complex data.
 *
 * This concrete class implements ManagesItems and ChainsNestedItems as well as
 * Container interoperability and various array functionality.
 *
 * @package Michaels\Manager
 */
class IocManager implements ManagesItemsInterface, ContainerInterface, IocManagerInterface
{
    use ManagesItemsTrait, ManagesIocTrait {
        ManagesIocTrait::add insteadof ManagesItemsTrait;
        ManagesIocTrait::get insteadof ManagesItemsTrait;
    }

    /**
     * Build a new manager instance
     * @param array $items
     */
    public function __construct(array $items = [])
    {
        $this->initManager($items);
    }
}
