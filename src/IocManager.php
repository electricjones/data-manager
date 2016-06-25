<?php
namespace Michaels\Manager;

use Interop\Container\ContainerInterface;
use Michaels\Manager\Contracts\ManagesIocInterface;
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
class IocManager implements ManagesItemsInterface, ContainerInterface, ManagesIocInterface
{
    use ManagesItemsTrait, ManagesIocTrait;

    /**
     * Build a new manager instance
     * @param array $di
     * @param array $items
     */
    public function __construct(array $di = [], array $items = [])
    {
        $this->initManager($items);
        $this->initDi($di);
    }
}
