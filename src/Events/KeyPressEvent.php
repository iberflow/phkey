<?php

namespace Atatusoft\PhpKeyListener\Events;

use \Symfony\Component\EventDispatcher\Event;

/**
 * Class KeyPressEvent
 *
 * @package  Atatusoft\PhpKeyListener\Events
 */
class KeyPressEvent extends Event
{
    /**
     * Key that was pressed
     *
     * @var string
     */
    protected $key;

    /**
     * @param $key
     */
    public function __construct($key) 
    {
        $this->key = $key;
    }

    /**
     * Key getter
     *
     * @return string
     */
    public function getKey() 
    {
        return $this->key;
    }
}