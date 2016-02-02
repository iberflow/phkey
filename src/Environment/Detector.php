<?php

namespace Iber\Phkey\Environment;

use Iber\Phkey\Contracts\StreamableInterface;
use Iber\Phkey\IO\Stream;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * Class Detector
 *
 * Detects the environment the library is ran upon and allows
 * automatic creation of the key listener based on different
 * environment implementations
 *
 * @package  Iber\Phkey\Environment
 */
class Detector
{

    /**
     * @var array
     */
    protected $implemented = [
        'unix'
    ];

    /**
     * @var string
     */
    protected $environment = 'unix';

    /**
     * @param null $environment
     */
    public function __construct($environment = null)
    {
        if (null === $environment) {
            $environment = PHP_OS;
        }

        $this->setEnvironment($environment);
    }

    /**
     * Sets the environment
     *
     * Falls back to Unix if the environment is not implemented
     *
     * @param $environment
     * @return $this
     *
     * @throws \UnexpectedValueException
     */
    public function setEnvironment($environment)
    {
        $environment = strtolower($environment);

        // list of implemented environments
        if (in_array($environment, $this->implemented)) {
            $this->environment = $environment;
            return $this;
        }

        // Linux, Unix and OSX don't differ much, therefore
        // we can use the same implementation
        if (preg_match('/(Linux|Unix|Darwin)/i', $environment, $match)) {
            $this->environment = 'unix';
            return $this;
        }

        // if the OS name begins with Win then
        // it's most likely Windows
        if ('WIN' === strtoupper(substr($environment, 0, 3))) {
            throw new \UnexpectedValueException("Windows isn't supported.");
        }

        // fallback to Unix
        $this->environment = 'unix';

        return $this;
    }

    /**
     * Environment getter
     *
     * @return string
     */
    public function getEnvironment()
    {
        return ucfirst($this->environment);
    }

    /**
     * Automatically instantiates the listener object with dependencies
     * based on environment
     *
     * @param EventDispatcher|null $eventDispatcher
     * @param StreamableInterface|null $stream
     *
     * @return \Iber\Phkey\Contracts\ListenableInterface
     */
    public function getListenerInstance(EventDispatcher $eventDispatcher = null, StreamableInterface $stream = null)
    {
        //instantiate the key matcher object
        $reflection = new \ReflectionClass('\\Iber\\Phkey\\Environment\\' . $this->getEnvironment() . '\\Matcher');
        $matcher = $reflection->newInstance();

        $reflection = new \ReflectionClass('\\Iber\\Phkey\\Environment\\' . $this->getEnvironment() . '\\Listener');

        if (null === $eventDispatcher) {
            $eventDispatcher = new EventDispatcher();
        }

        if (null === $stream) {
            $stream = new Stream();
        }

        $listener = $reflection->newInstance($matcher, $eventDispatcher, $stream);

        return $listener;
    }
}