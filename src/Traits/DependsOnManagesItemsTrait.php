<?php
namespace Michaels\Manager\Traits;

trait DependsOnManagesItemsTrait
{
    abstract public function initManager($items = []);

    abstract public function add($alias, $item = null);

    abstract public function get($alias, $fallback = null);

    abstract public function getAll();

    abstract public function getIfExists($alias);

    abstract public function all();

    abstract public function exists($alias);

    abstract public function has($alias);

    abstract public function set($alias, $item = null);

    abstract public function remove($alias);

    abstract public function clear();

    abstract public function reset($items);

    abstract public function toJson($options = 0);

    abstract public function isEmpty();

    abstract public function __toString();
}
