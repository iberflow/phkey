<?php

namespace Iber\Phkey\Environment\Unix;

use Iber\Phkey\Contracts\ListenableInterface;
use Iber\Phkey\Contracts\StreamableInterface;
use Iber\Phkey\Events\KeyPressEvent;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * Class Listener
 *
 * Allows listening for keyboard keys and bind events to those keys
 *
 * @package  Iber\Phkey\Environment\Unix
 */
class Listener implements ListenableInterface
{

    /**
     * This is the amount of time we stop waiting for the rest
     * of the key code sequence characters and mark the character
     * as the escape key
     *
     * The timespan between characters when a special char is
     * pressed is around 0.00001
     *
     * In order to capture the escape key, we need to make sure
     * that there aren't any characters following the escape key
     */
    const ESCAPE_TIMEOUT = 0.001;

    /**
     * Object responsible for key matching
     *
     * @type \Iber\Phkey\Contracts\MatchableInterface
     */
    protected $matcher;

    /**
     * Used to keep the listener active
     *
     * @var boolean
     */
    protected $isListening = true;

    /**
     * Event dispacher
     *
     * @var \Symfony\Component\EventDispatcher\EventDispatcher
     */
    protected $eventDispatcher;

    /**
     * @var \Iber\Phkey\Contracts\StreamableInterface
     */
    protected $stream;

    /**
     * Marks special char sequence
     *
     * Most unicode special characters that come through the terminal
     * are symbol sequences that need to be concatenated to get
     * the full key code
     *
     * @var bool
     */
    protected $charSequenceEnabled = false;

    /**
     * Used for marking when the escape key was pressed
     *
     * @var null|number
     */
    protected $escapePressedAt = null;

    /**
     * Stores a key code that is currently being parsed
     *
     * @var null
     */
    protected $currentKey = null;

    /**
     * Initializes the listener
     *
     * @param Matcher $matcher
     * @param EventDispatcher $eventDispatcher
     * @param StreamableInterface $stream
     */
    public function __construct(Matcher $matcher, EventDispatcher $eventDispatcher, StreamableInterface $stream)
    {
        $this->matcher = $matcher;
        $this->eventDispatcher = $eventDispatcher;
        $this->stream = $stream;
    }

    /**
     * @return EventDispatcher
     */
    public function getEventDispatcher()
    {
        return $this->eventDispatcher;
    }

    /**
     * Initiates the listener
     */
    public function start()
    {
        $this->eventDispatcher->addListener(
            'key:stop:listening',
            function () {
                $this->isListening = false;
            }
        );

        $this->resetListener()
            ->overrideReadlineHandler()
            ->runLoop()
            ->restoreReadlineHandler();
    }

    /**
     * Starts capturing keys
     *
     * Reads every single character in STDIN one by one
     * and figures out which keys were pressed
     *
     * Supports special keys such as arrows, function keys etc.
     * by combining separate characters into unicode characters
     *
     * @todo add multi-language support
     *       or not since even js doesn't support it(?)
     *       '/[\x00-\x7F]/' basic latin range including special keys
     *
     * @return $this
     */
    protected function runLoop()
    {
        while ($this->isListening) {
            $this->stream->select();

            // special key sequence has started
            if (true === $this->charSequenceEnabled && null !== $this->escapePressedAt) {
                if ($this->escapeKeyHasExpired()) {
                    $this->disableKeySequence();

                    $this->setCurrentKey($this->matcher->getKey($this->matcher->getEscapeKey()));

                    // run key listeners, if one of the key callbacks returns true
                    // break the loop and restore default terminal functionality
                    $this->dispatchKeyPressEvents($this->getCurrentKey());
                }
            }

            // proceed if the stream isn't empty
            if ($this->stream->isAvailable()) {
                $char = $this->stream->getChar();

                // start of the special key sequence
                // mark the sequence start and setup the timer for the escape key
                if ($this->matcher->getEscapeKey() === $char) {
                    $this->enableKeySequence();
                    $this->setCurrentKey($char);
                } else {
                    $this->escapePressedAt = null;

                    if ($this->charSequenceEnabled) {
                        // if special key was pressed, concatenate the current
                        // escape character with the next characters that come
                        // in through the stream
                        $this->concatCurrentKey($char);

                        $mapped = $this->matcher->getKey($this->getCurrentKey());
                        // check and get which arrow key was pressed
                        if ($mapped) {
                            $this->setCurrentKey($mapped);
                            $this->disableKeySequence();
                        } else {
                            // we skip this iteration because the sequence isn't
                            // finished yet and we don't need to run the key
                            // listeners
                            continue;
                        }
                    } else {
                        // something out of basic latin charset so we ignore it
                        if ($this->matcher->isBasicLatin($char)) {
                            continue;
                        } else {
                            // normal keyboard key was pressed
                            $this->setCurrentKey($this->matcher->getKey($char) ?: $char);
                        }
                    }

                    // run key listeners, if one of the key callbacks returns true
                    // break the loop and restore default terminal functionality
                    $this->dispatchKeyPressEvents($this->getCurrentKey());
                }
            }
        }

        return $this;
    }

    /**
     * @return bool
     */
    protected function escapeKeyHasExpired()
    {
        return (microtime(true) - $this->escapePressedAt) > self::ESCAPE_TIMEOUT;
    }

    /**
     * @return null
     */
    protected function getCurrentKey()
    {
        return $this->currentKey;
    }

    /**
     * @param $key
     * @return $this
     */
    protected function setCurrentKey($key)
    {
        $this->currentKey = $key;

        return $this;
    }

    /**
     * @param $char
     * @return $this
     */
    protected function concatCurrentKey($char)
    {
        $this->currentKey .= $char;

        return $this;
    }

    /**
     * Resets the listener properties
     */
    protected function resetListener()
    {
        $this->currentKey = null;
        $this->escapePressedAt = null;
        $this->charSequenceEnabled = false;
        $this->isListening = true;

        return $this;
    }

    /**
     * @return $this
     */
    protected function enableKeySequence()
    {
        $this->charSequenceEnabled = true;
        $this->escapePressedAt = microtime(true);

        return $this;
    }

    /**
     * @return $this
     */
    protected function disableKeySequence()
    {
        $this->escapePressedAt = null;
        $this->charSequenceEnabled = false;

        return $this;
    }

    /**
     * @param $key
     * @return $this
     */
    public function dispatchKeyPressEvents($key)
    {
        $event = new KeyPressEvent($key);
        $this->eventDispatcher->dispatch('key:press', $event);
        $this->eventDispatcher->dispatch('key:' . $key, $event);

        return $this;
    }

    /**
     * Overrides the default readline handler
     *
     * By default STDIN is read when the Enter key is pressed
     * we can override it by registering an empty closure as
     * the readline handler
     *
     * @return $this
     */
    protected function overrideReadlineHandler()
    {
        readline_callback_handler_install(
            '',
            function () {

            }
        );

        return $this;
    }

    /**
     * Restore the default readline handler
     *
     * @return $this
     */
    protected function restoreReadlineHandler()
    {
        readline_callback_handler_remove();

        return $this;
    }

}