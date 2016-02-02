<?php

namespace Iber\Phkey\Tests;

class EnvironmentDetectorTest extends \PHPUnit_Framework_TestCase
{
    public function testSettingEnvironment()
    {
        $detector = new \Iber\Phkey\Environment\Detector();

        $detector->setEnvironment('Darwin');
        $this->assertSame('Unix', $detector->getEnvironment());

        $detector->setEnvironment('Random');
        $this->assertSame('Unix',
            $detector->getEnvironment(),
            'Not implemented environment should default to Unix'
        );

        $detector->setEnvironment('Linux');
        $this->assertSame('Unix', $detector->getEnvironment());

        $detector->setEnvironment('Unix');
        $this->assertSame('Unix', $detector->getEnvironment());
    }

    public function testInstanceBasedOnEnvironment()
    {
        $detector = new \Iber\Phkey\Environment\Detector();

        $detector->setEnvironment('Darwin');
        $this->assertInstanceOf(
            'Iber\\Phkey\\Environment\\Unix\\Listener',
            $detector->getListenerInstance()
        );

        $detector->setEnvironment('Random');
        $this->assertInstanceOf(
            'Iber\\Phkey\\Environment\\Unix\\Listener',
            $detector->getListenerInstance(),
            'Not implemented environment should default to Unix'
        );

        $detector->setEnvironment('Linux');
        $this->assertInstanceOf(
            'Iber\\Phkey\\Environment\\Unix\\Listener',
            $detector->getListenerInstance()
        );

        $detector->setEnvironment('Unix');
        $this->assertInstanceOf(
            'Iber\\Phkey\\Environment\\Unix\\Listener',
            $detector->getListenerInstance()
        );
    }

}