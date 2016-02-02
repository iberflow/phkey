<?php

/**
 * Converts unicode characters into a string
 *
 * PHP doesn't support unicode character syntax out of the box
 * but JSON does and it's a lot faster than mb_convert_encoding
 *
 * @param string $code unicode characters
 *
 * @package  Iber\Phkey
 *
 * @return string
 */
function unicode_to_string($code)
{
    return json_decode('"' . $code . '"');
}