<?php
namespace Michaels\Manager\Exceptions;

use Psr\Log\InvalidArgumentException;

/**
 * InvalidItemsObjectException
 *
 * Thrown if you try to initialize the manager with something other than
 * an array() or \Traversable
 *
 * $manager = new Manager(3);
 * $manager = new Manager("michael");
 *
 * @package Michaels\Manager
 */
class InvalidItemsObjectException extends InvalidArgumentException
{

}
