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

    /* Traits cannot declare constants, so we mimic constants with static properties */
    public static $RETURN_ARRAY = "_return_array";
    public static $RETURN_COLLECTION = "_return_collection";
    public static $MODIFY_MANIFEST = "_modify_manifest";

    /**
     * Configuration: do we want to return Collections from get() and getAll()?
     * @var bool
     */
    public $useCollections = true;

    protected $collectionApi = [
        'walk',
        'unique',
        'unshift'
        // ToDo: Add more, all of them should work (where there isn't a conflict)
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
     * @param $method
     * @param $arguments
     * @return mixed
     * @throws \Exception
     */
    public function __call($method, $arguments)
    {
        if (in_array($method, $this->collectionApi)) {
            /* Setup the arguments */
            $subject = array_shift($arguments);
            $collection = $this->toCollection($this->get($subject));

            /* Perform the action and return the value */
            switch (end($arguments)) {
                case (static::$RETURN_COLLECTION):
                    return $this->callArrayzy(
                        $method,
                        $this->removeReturnFlag($arguments),
                        $collection
                    );

                case (static::$MODIFY_MANIFEST):
                    $value = $this->callArrayzy(
                        $method,
                        $this->removeReturnFlag($arguments),
                        $collection
                    );
                    $this->set($subject, $value->toArray());
                    return $this;

                case (static::$RETURN_ARRAY):
                    return $this
                        ->callArrayzy(
                            $method,
                            $this->removeReturnFlag($arguments),
                            $collection
                        )
                        ->toArray();

                default: // No flag was given
                    return $this
                        ->callArrayzy(
                            $method,
                            $arguments,
                            $collection
                        )
                        ->toArray();
            }
        }

        /* ToDo: A better exception */
        /* ToDo: How to handle conflict with ChainsNestedItems */
        throw new \Exception("No method found");
    }

    /**
     * Calls the actual method on the Arrayzy Instance
     * @param $method
     * @param $arguments
     * @param $instance
     * @return mixed
     */
    protected function callArrayzy($method, $arguments, $instance)
    {
        return call_user_func_array([$instance, $method], $arguments);
    }

    /**
     * Removes the user-supplied return value flag
     * @param $arguments
     * @return array
     */
    protected function removeReturnFlag($arguments)
    {
        return array_slice($arguments, 0, -1);
    }
}
