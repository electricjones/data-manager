<?php
namespace Michaels\Manager\Messages;

/**
 * Class NoItemFoundMessage
 * @package Michaels\Manager\Messages
 */
class NoItemFoundMessage
{
    protected $message;

    public function __construct($alias = 'The item requested')
    {
        $this->message = "`$alias` was not found";
    }

    public function getMessage()
    {
        return $this->message;
    }
}
