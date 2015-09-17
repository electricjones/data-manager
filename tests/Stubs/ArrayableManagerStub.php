<?php
namespace Michaels\Manager\Test\Stubs;

use ArrayAccess;
use Countable;
use IteratorAggregate;
use JsonSerializable;
use Michaels\Manager\Contracts\ManagesItemsInterface;
use Michaels\Manager\Traits\ArrayableTrait;
use Michaels\Manager\Traits\ManagesItemsTrait;

/**
 * Class CustomizedManagerStub
 * @package Stubs
 */
class ArrayableManagerStub implements
    ManagesItemsInterface,
    ArrayAccess,
    Countable,
    IteratorAggregate,
    JsonSerializable
{
    use ManagesItemsTrait, ArrayableTrait;

    public function __construct(array $items = [])
    {
        $this->initManager($items);
    }
}
