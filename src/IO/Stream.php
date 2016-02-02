<?php

namespace Iber\Phkey\IO;

use Iber\Phkey\Contracts\StreamableInterface;

/**
 * Class Stream
 *
 * Allows watching a stream and getting contents from it
 *
 * @package  Iber\Phkey\IO
 */
class Stream implements StreamableInterface
{
    /**
     * Selected stream
     *
     * @var resource
     */
    protected $stream;

    /**
     * Used for stream watching timeout
     *
     * @var integer
     */
    protected $timeout;

    /**
     * Selected stream
     *
     * @var
     */
    protected $selected = false;

    /**
     * @param $stream
     * @param int $timeout
     */
    public function __construct($stream = STDIN, $timeout = 35000)
    {
        $this->setStream($stream)
            ->setTimeout($timeout);
    }

    /**
     * Sets an active stream
     *
     * @param $stream
     * @throws \InvalidArgumentException
     * @return $this
     */
    public function setStream($stream)
    {
        if ('stream' !== get_resource_type($stream)) {
            throw new \InvalidArgumentException(
                'The passed value isn\'t a valid stream.'
            );
        }

        $this->stream = $stream;
        return $this;
    }

    /**
     * Sets the stream_select() timeout
     *
     * @param $timeout
     * @return $this
     */
    public function setTimeout($timeout)
    {
        $this->timeout = $timeout;
        return $this;
    }

    /**
     * Starts watching the stream
     *
     * The timeout interval is required to
     * stop blocking the thread when there's no data
     */
    public function select()
    {
        $read = [$this->stream];
        $write = null;
        $except = null;

        // if the process gets a system signal before the timeout is ran
        // a warning is thrown therefore we need to suppress the warning
        $this->selected = @stream_select(
            $read,
            $write,
            $except,
            0,
            $this->timeout
        );

        return $this;
    }

    /**
     * Checks if the selected stream watcher has any content
     *
     * @return bool
     */
    public function isAvailable()
    {
        return false !== $this->selected && $this->selected > 0;
    }

    /**
     * Fetches a single character from the stream
     *
     * @return string
     */
    public function getChar()
    {
        return stream_get_contents($this->stream, 1);
    }
}