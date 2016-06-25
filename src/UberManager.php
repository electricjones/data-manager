<?php
namespace Michaels\Manager;

use ArrayAccess;
use Countable;
use Interop\Container\ContainerInterface;
use IteratorAggregate;
use JsonSerializable;
use Michaels\Manager\Contracts\ChainsNestedItemsInterface;
use Michaels\Manager\Contracts\LoadsFilesInterface;
use Michaels\Manager\Contracts\IocManagerInterface;
use Michaels\Manager\Contracts\ManagesItemsInterface;
use Michaels\Manager\Traits\ArrayableTrait;
use Michaels\Manager\Traits\ChainsNestedItemsTrait;
use Michaels\Manager\Traits\LoadsFilesTrait;
use Michaels\Manager\Traits\ManagesIocTrait;
use Michaels\Manager\Traits\ManagesItemsTrait;

/**
 * Manages deeply nested, complex data.
 *
 * This manager class does it all, and is more a proof of concept than anything else.
 * It manages dependencies, loads files, uses magic methods, is arrayable, and all the rest.
 *
 * @package Michaels\Manager
 */
class UberManager implements
    ManagesItemsInterface,
    ChainsNestedItemsInterface,
    IocManagerInterface,
    LoadsFilesInterface,

    // Array Access
    ArrayAccess,
    Countable,
    IteratorAggregate,
    JsonSerializable,

    // Standards
    ContainerInterface

{
    use ManagesItemsTrait, ChainsNestedItemsTrait, ArrayableTrait, ManagesIocTrait, LoadsFilesTrait;

    /**
     * The items stored in the manager
     * @var array $items Items governed by manager
     */
    protected $items;

    /**
     * Build a new manager instance
     * @param array $items
     */
    public function __construct($items = [])
    {
        $this->initManager($items);
    }
}
