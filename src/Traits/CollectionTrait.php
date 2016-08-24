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
 * @method $this addToChain(string $name) From ChainsNestedItemsTrait
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

    /**
     * Converts an array to a collection if value is arrayable and config is set
     * @param $value
     * @return ArrayImitator
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
     * Invokes when calling a method on the Collection API
     *
     * This method simply decides how to handle the method call.
     *   1. The class is using the ChainsNestedItemsTrait and Collection API does NOT contain the method
     *      Let `ChainsNestedItemsTrait` do its thing
     *   2. The Collection API DOES contain the method
     *      Pass the method call along to the third party Collection API
     *   3. The method does not exist on the class or in the Collection API
     *      Throw an Exception
     * @param string $method Name of the method
     * @param array $arguments Arguments passed along
     * @return mixed
     * @throws \BadMethodCallException
     */
    public function __call($method, $arguments)
    {
        /* Decide how to handle this method call */
        if (!$this->collectionApiHasMethod($method)) {
            // Are we using the Nested Items trait?
            if ($this->usingNestedItemsTrait()) {
                // Yes, so we simply add it to the chain
                return $this->addToChain($method); // in ChainsNestedItemsTrait
                // No, so we are calling a method that simply does not exist
            } else {
                throw new \BadMethodCallException(
                    "Call to undefined method. `$method` does not exist in "
                    . get_called_class() . " and it is not part of the Collection API"
                );
            }
        }

        /* Since we are calling a Collection API method, pass it along */
        return $this->passToCollectionApi($method, $arguments);
    }

    /**
     * Checks to see if the Collection API contains a specific method
     * @param string $method name
     * @return bool
     */
    protected function collectionApiHasMethod($method)
    {
        return method_exists(new ArrayImitator(), $method);
    }

    /**
     * Checks to see if the current Manager class is using `ChainsNestedItemsTrait`
     * @return bool
     */
    protected function usingNestedItemsTrait()
    {
        return in_array('Michaels\Manager\Traits\ChainsNestedItemsTrait', class_uses($this));
    }

    /**
     * Passes the method call along to the Collection API (currently Arrayzy)
     * Also checks for any flags that determine how return data should be formatted
     *
     * @param string $method name
     * @param array $arguments to be passed along (including return type flags if exists)
     * @return mixed
     */
    protected function passToCollectionApi($method, $arguments)
    {
        /* Setup the arguments */
        $subject = array_shift($arguments);
        $collectionInstance = $this->toCollection($this->get($subject));

        // Is the last argument one of our flags?
        if (in_array(end($arguments), [
            static::$RETURN_ARRAY,
            static::$RETURN_COLLECTION,
            static::$MODIFY_MANIFEST,
        ])) {
            // Yes, pop it off and set it
            $flag = array_pop($arguments);

        } else {
            // No, leave the arguments alone and flag as an ARRAY by default
            $flag = static::$RETURN_ARRAY;
        }

        /* Perform the Action */
        return $this->callCollectionMethod($method, $arguments, $collectionInstance, $flag, $subject);
    }

    /**
     * Calls the actual method on the Collection Instance (currently Arrayzy)
     *
     * @param string $method name
     * @param array $arguments to be passed along
     * @param CollectionTrait $instance of the Collection
     * @param string $flag corresponding to the properties above
     * @param string $subject Alias of data in Manager
     * @return mixed
     */
    protected function callCollectionMethod($method, $arguments, $instance, $flag, $subject)
    {
        $value = call_user_func_array([$instance, $method], $arguments);

        switch ($flag) {
            case (static::$RETURN_COLLECTION):
                $return = $value;
                break;

            case (static::$MODIFY_MANIFEST):
                $this->set($subject, $value->toArray());
                $return = $this;
                break;

            case (static::$RETURN_ARRAY):
            default:
                $return = $value->toArray();
        }

        return $return;
    }
}
