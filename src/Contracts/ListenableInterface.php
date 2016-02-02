<?php

namespace Iber\Phkey\Contracts;

/**
 * Interface ListenableInterface
 *
 * @package  Iber\Phkey\Contracts
 */
interface ListenableInterface
{
    /**
     * Starts the listener loop
     *
     * @return string
     */
    public function start();

    /**
     * @return \Symfony\Component\EventDispatcher\EventDispatcher
     */
    public function getEventDispatcher();

}