<?php
namespace Michaels\Manager\Exceptions;

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
class NestingUnderNonArrayException extends \InvalidArgumentException
{

}
