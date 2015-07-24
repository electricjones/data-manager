<?php
namespace Michaels\Manager\Traits;

use Michaels\Manager\Contracts\ManagesItemsInterface;
use Michaels\Manager\Exceptions\DependencyNotMetException;
use Symfony\Component\Config\Definition\Exception\Exception;

/**
 * Ensures that a trait that depends on
 * ManagesItemsTrait implements ManagesItemsInterface
 */
trait DependsOnManagesItemsTrait
{
    /** @var null|bool Does current object implement ManagesItemsTrait (for caching)  */
    protected $canManageItems = null;

    /**
     * Throws exception if ManagesItemsInterface not satisfied
     * @return bool
     * @throws DependencyNotMetException
     */
    protected function ensureCanManageItems()
    {
        if (!is_null($this->canManageItems)) {
            return $this->canManageItems;
        } else {
            if ($this instanceof ManagesItemsInterface) {
                return $this->canManageItems = true;
            } else {
                throw new DependencyNotMetException(
                    get_called_class() . " must implement `ManagesItemsInterface` to use `CollectionTrait` or `ChainsNestedItemsTrait`"
                );
            }
        }
    }
}
