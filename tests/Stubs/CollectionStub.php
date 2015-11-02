<?php
namespace Michaels\Manager\Test\Stubs;

use Michaels\Manager\Traits\CollectionTrait;
use Michaels\Manager\Traits\ManagesItemsTrait;

/**
 * Class CustomizedManagerStub
 * @package Stubs
 */
class CollectionStub
{
    use ManagesItemsTrait, CollectionTrait;

    public function __construct($items)
    {
        $this->initManager($items);
    }
}
