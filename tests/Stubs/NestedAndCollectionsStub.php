<?php
namespace Michaels\Manager\Test\Stubs;
use Michaels\Manager\Contracts\ManagesItemsInterface;
use Michaels\Manager\Traits\ChainsNestedItemsTrait;
use Michaels\Manager\Traits\CollectionTrait;
use Michaels\Manager\Traits\ManagesItemsTrait;

/**
 * Class NestedAndCollectionsStub
 * @package Michaels\Manager\Test\Stubs
 */
class NestedCollectionsStub implements ManagesItemsInterface
{
    use ManagesItemsTrait, ChainsNestedItemsTrait, CollectionTrait {
        CollectionTrait::__call insteadof ChainsNestedItemsTrait;
    }

    public function __construct(array $items = null)
    {
        $this->initManager($items);
    }
}
