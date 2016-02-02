<?php

namespace Iber\Phkey\Environment\Unix;

use Iber\Phkey\Contracts\MatchableInterface;

/**
 * Class Matcher
 *
 * Responsible for mapping keys to terminal input
 *
 * @package  Iber\Phkey\Environment\Unix
 */
class Matcher implements MatchableInterface
{

    /**
     * Most unicode controls begin with \u001b
     * which is the escape key as well
     */
    protected $escape = '\u001b';

    /**
     * Plain text char map
     *
     * @type array
     */
    protected $text = [
        'enter' => "\n",
        'space' => ' ',
        'tab'   => "\t"
    ];

    /**
     * Ascii char map
     *
     * @type array
     */
    protected $ascii = [
        'backspace' => 127 //\u0008 doesn't work
    ];

    /**
     * Unicode char map
     *
     * @type array
     */
    protected $unicode = [
        // main keys
        'escape'   => '\u001b',
        'delete'   => '\u001b[3~',

        // arrow keys
        'up'       => '\u001b[A',
        'down'     => '\u001b[B',
        'right'    => '\u001b[C',
        'left'     => '\u001b[D',

        // function keys
        'f1'       => '\u001bOP',
        'f2'       => '\u001bOQ',
        'f3'       => '\u001bOR',
        'f4'       => '\u001bOS',
        'f5'       => '\u001b[15~',
        'f6'       => '\u001b[17~',
        'f7'       => '\u001b[18~',
        'f8'       => '\u001b[19~',
        'f9'       => '\u001b[20~',
        'f10'      => '\u001b[21~',
        'f11'      => '\u001b[23~',
        'f12'      => '\u001b[24~',
        'f13'      => '\u001b[25~',
        'f14'      => '\u001b[26~',
        'f15'      => '\u001b[28~',
        'f16'      => '\u001b[29~',
        'f17'      => '\u001b[31~',
        'f18'      => '\u001b[32~',
        'f19'      => '\u001b[33~',
        'ff20'     => '\u001b[34~',

        // other keys
        'do'       => '\u001b[29~',
        'find'     => '\u001b[1~',
        'help'     => '\u001b[28~',
        'insert'   => '\u001b[2~',
        'end'      => '\u001b[F',
        'home'     => '\u001b[H',
        'next'     => '\u001b[6~',
        'previous' => '\u001b[5~',
        'select'   => '\u001b[44~',
    ];

    /**
     * Fetches a human readable key representation
     * based on a unicode/ascii/plain text charset
     *
     * @param string $input unicode string
     *
     * @return bool|int|string
     */
    public function getKey($input)
    {
        // try matching the key with plain text
        foreach ($this->text as $key => $code) {
            if ($code === $input) {
                return $key;
            }
        }

        // try matching the key with ascii
        foreach ($this->ascii as $key => $code) {
            if (chr($code) === $input) {
                return $key;
            }
        }

        // try matching the key with unicode
        foreach ($this->unicode as $key => $code) {
            if (unicode_to_string($code) === $input) {
                return $key;
            }
        }

        return false;
    }

    /**
     * Checks whether a string has only latin characters
     *
     * @param $input
     * @return int
     */
    public function isBasicLatin($input) 
    {
        return preg_match('/[^\x00-\x7F]+/', $input, $matches);
    }

    /**
     * Get the encoded escape key
     *
     * @return string
     */
    public function getEscapeKey()
    {
        return unicode_to_string($this->escape);
    }
}