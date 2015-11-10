<?php
namespace Michaels\Manager\Traits;

use Arrayzy\MutableArray;
use Michaels\Manager\Helpers;

/**
 * Access Deeply nested manager items through magic methods
 *
 * MUST be used with ManagesItemsTrait
 *
 * @implements Michaels\Manager\Contracts\ChainsNestedItemsInterface
 * @package Michaels\Manager
 */
trait CollectionTrait
{
    use DependsOnManagesItemsTrait;

    /**
     * Configuration: do we want to return Collections from get() and getAll()?
     * @var bool
     */
    public $useCollections = true;

    /**
     * Converts an array to a collection if value is arrayable and config is set
     * @param $value
     * @return MutableArray
     */
    public function toCollection($value)
    {
        if ($this->wantsCollections() && Helpers::isArrayable($value)) {
            return new MutableArray(Helpers::getArrayableItems($value));
        }

        return $value;
    }

    /**
     * Does this instance want collections returned from get() and getAll()?
     * @return bool
     */
    public function wantsCollections()
    {
        return ($this->useCollections === true);
    }
}
