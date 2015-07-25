<?php
namespace Michaels\Manager\Exceptions;

use Exception;
use Interop\Container\Exception\ContainerException;
use Psr\Log\InvalidArgumentException;

/**
 * ModifyingProtectedValueException
 *
 * Thrown if you try modify a protected item
 *
 * $manager = new Manager(['one' => 1]);
 * $manager->protect("one");
 * $manager->set("one", "two");
 *
 * @package Michaels\Manager
 */
class ModifyingProtectedValueException extends InvalidArgumentException
{

}
