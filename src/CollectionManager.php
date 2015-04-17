<?php
namespace Michaels\Manager;

use Illuminate\Support\Collection;
use Michaels\Manager\Traits\BasicManagerTrait;

/**
 * Class CollectionManager
 * @package Michaels\Manager
 */
class CollectionManager extends Collection implements ManagerInterface
{
    use BasicManagerTrait;
}
