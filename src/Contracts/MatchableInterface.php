<?php

namespace Atatusoft\PhpKeyListener\Contracts;

/**
 * Interface MatchableInterface
 *
 * @package  Atatusoft\PhpKeyListener\Contracts
 */
interface MatchableInterface
{
    /**
     * Returns a human readable key name based on a unicode string
     *
     * @param string $keyCode unicode character
     *
     * @return bool|int|string
     */
    public function getKey($keyCode);

    /**
     * Checks whether a string has only latin characters
     *
     * @param $input
     * @return mixed
     */
    public function isBasicLatin($input);

    /**
     * Returns the escape key code
     *
     * @return mixed
     */
    public function getEscapeKey();
}