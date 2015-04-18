<?php
namespace Michaels\Manager;

use Illuminate\Support\Collection;
use Michaels\Manager\Contracts\ManagesItemsInterface;
use Michaels\Manager\Traits\BasicManagerTrait;
use Michaels\Manager\Traits\ManagesItemsTrait;

/**
 * Class CollectionManager
 * @package Michaels\Manager
 */
class CollectionManager extends Collection implements ManagesItemsInterface
{
    use ManagesItemsTrait;
}
