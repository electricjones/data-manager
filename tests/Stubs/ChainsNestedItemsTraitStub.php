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
     * Build a new manager instance
     * @param array $items
     */
    public function __construct($items = [])
    {
        $this->initManager($items);
    }

    public function someMethod()
    {
        //do nothing
    }
}
