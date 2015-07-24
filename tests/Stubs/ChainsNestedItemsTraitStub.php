<?php
namespace Michaels\Manager\Test\Stubs;

use Michaels\Manager\Contracts\ManagesItemsInterface;
use Michaels\Manager\Traits\ChainsNestedItemsTrait;
use Michaels\Manager\Traits\ManagesItemsTrait;

/**
 * Class ChainsNestedItemsTraitStub
 * @package Stubs
 */
class ChainsNestedItemsTraitStub implements ManagesItemsInterface
{
    use ManagesItemsTrait, ChainsNestedItemsTrait;

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
