<?php
namespace Michaels\Manager\Exceptions;

use Exception;
use Interop\Container\Exception\ContainerException;
use Psr\Log\InvalidArgumentException;

/**
 * NestingUnderNonArrayException
 *
 * Thrown if you try to nest a new value under an existing non-array value
 *
 * $manager = new Manager(['one' => 1]);
 * $manager->add("one.two", "two-value");
 *
 * @package Michaels\Manager
 */
class NestingUnderNonArrayException extends InvalidArgumentException
{

}
