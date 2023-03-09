<?php

namespace Atatusoft\PhpKeyListener\Contracts;

/**
 * Interface ListenableInterface
 *
 * @package  Atatusoft\PhpKeyListener\Contracts
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