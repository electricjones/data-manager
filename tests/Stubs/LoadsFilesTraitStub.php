<?php
namespace Michaels\Manager\Test\Stubs;

use Michaels\Manager\Contracts\LoadsFilesInterface;
use Michaels\Manager\Contracts\ManagesItemsInterface;
use Michaels\Manager\Traits\LoadsFilesTrait;
use Michaels\Manager\Traits\ManagesItemsTrait;

/**
 * Class ManagesItemsTraitStub
 * @package Stubs
 */
class LoadsFilesTraitStub implements ManagesItemsInterface, LoadsFilesInterface
{
    use ManagesItemsTrait, LoadsFilesTrait;
}
