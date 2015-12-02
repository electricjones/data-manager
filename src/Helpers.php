<?php
namespace Michaels\Manager;
use Michaels\Manager\Contracts\ManagesItemsInterface;
use Michaels\Manager\Traits\ManagesItemsTrait;
use Traversable;

/**
 * Class Helpers
 * @package Michaels\Manager
 */
class Helpers
{
    /**
     * Results array of items from Collection or Arrayable.
     *
     * @param  mixed $items
     * @return array
     */
    public static function getArrayableItems($items)
    {
        if ($items instanceof ManagesItemsTrait || $items instanceof ManagesItemsInterface) {
            return $items->getAll();

        } elseif ($items instanceof Traversable) {
            return iterator_to_array($items);
        }

        return (array)$items;
    }

    /**
     * Returns `true` if value can be used as array or traversed.
     * @param $value
     * @return bool
     */
    public static function isArrayable($value)
    {
        if ($value instanceof ManagesItemsInterface
            || $value instanceof ManagesItemsTrait
            || $value instanceof Traversable
            || is_array($value)
        ) {
            return true;
        }
        return false;
    }
}
