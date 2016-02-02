<?php

namespace Iber\Phkey\Events;

use \Symfony\Component\EventDispatcher\Event;

/**
 * Class KeyPressEvent
 *
 * @package  Iber\Phkey\Events
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