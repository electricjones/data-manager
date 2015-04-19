<?php
namespace Michaels\Manager\Exceptions;

use Exception;
use Interop\Container\Exception\ContainerException;

/**
 * ItemNotFoundException
 * @package Michaels\Manager
 */
class ItemNotFoundException extends Exception implements ContainerException
{

}
