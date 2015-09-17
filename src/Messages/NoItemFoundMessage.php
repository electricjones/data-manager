<?php
namespace Michaels\Manager\Messages;

/**
 * Class NoItemFoundMessage
 * @package Michaels\Manager\Messages
 */
class NoItemFoundMessage
{
    /** @var string The not found message */
    protected $message;

    /**
     * Creates a new NoItemFoundMessage
     * @param string $alias
     */
    public function __construct($alias = 'The item requested')
    {
        $this->message = "`$alias` was not found";
    }

    /**
     * Returns the message
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }
}
