<?php
namespace Michaels\Manager;

use Exception;
use Interop\Container\Exception\ContainerException;

/**
 * Class ItemNotFoundException
 * @package Michaels\Manager
 */
class ItemNotFoundException extends Exception implements ContainerException
{

}