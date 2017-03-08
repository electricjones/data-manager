<?php
namespace Michaels\Manager\Test\Stubs;

use Michaels\Manager\Contracts\IocManagerInterface;
use Michaels\Manager\Traits\ManagesIocTrait;
use Michaels\Manager\Traits\ManagesItemsTrait;

/**
 * Class CustomizedManagerStub
 * @package Stubs
 */
class IocManagerStub implements IocManagerInterface
{
    use ManagesItemsTrait, ManagesIocTrait {
        ManagesIocTrait::add insteadof ManagesItemsTrait;
        ManagesIocTrait::get insteadof ManagesItemsTrait;
    }
}
