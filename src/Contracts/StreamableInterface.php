<?php

namespace Atatusoft\PhpKeyListener\Contracts;

/**
 * Interface StreamableInterface
 *
 * @package  Atatusoft\PhpKeyListener\Contracts
 */
interface StreamableInterface
{
    /**
     * Internally selects an active stream
     *
     * @return mixed
     */
    public function select();

    /**
     * Checks if the stream isn't empty and available
     *
     * @return mixed
     */
    public function isAvailable();

    /**
     * Takes a single character from the stream
     *
     * @return mixed
     */
    public function getChar();
}