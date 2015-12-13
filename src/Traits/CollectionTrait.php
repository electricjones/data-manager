<?php
namespace Michaels\Manager\Traits;

use Arrayzy\ArrayImitator;
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

    protected $collectionApi = [
        'walk',
        'unique',
        'unshift'
        // ToDo: Add more, all of them should work (where there isn't a conflic
    ];

    /**
     * Converts an array to a collection if value is arrayable and config is set
     * @param $value
     * @return static
     */
    public function toCollection($value)
    {
        if ($this->wantsCollections() && Helpers::isArrayable($value)) {
            return new ArrayImitator(Helpers::getArrayableItems($value));
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

    /**
     * Pass along Arrayzy API to Arrayzy ArrayImitator Class
     * @param $name
     * @param $arguments
     * @return mixed
     * @throws \Exception
     */
    public function __call($name, $arguments)
    {
        if (in_array($name, $this->collectionApi)) {
            $subject = array_shift($arguments);
            $collection = $this->toCollection($this->get($subject));
            return call_user_func_array([$collection, $name], $arguments);
        }

        /* ToDo: A better exception */
        /* ToDo: How to handle conflict with ChainsNestedItems */
        throw new \Exception("No method found");
    }
}
