<?php

namespace Atatusoft\PhpKeyListener\Tests;

use PHPUnit\Framework\TestCase;

class EnvironmentDetectorTest extends TestCase
{
    public function testSettingEnvironment()
    {
        $detector = new \Atatusoft\PhpKeyListener\Environment\Detector();

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
        $detector = new \Atatusoft\PhpKeyListener\Environment\Detector();

        $detector->setEnvironment('Darwin');
        $this->assertInstanceOf(
            'Atatusoft\\PhpKeyListener\\Environment\\Unix\\Listener',
            $detector->getListenerInstance()
        );

        $detector->setEnvironment('Random');
        $this->assertInstanceOf(
            'Atatusoft\\PhpKeyListener\\Environment\\Unix\\Listener',
            $detector->getListenerInstance(),
            'Not implemented environment should default to Unix'
        );

        $detector->setEnvironment('Linux');
        $this->assertInstanceOf(
            'Atatusoft\\PhpKeyListener\\Environment\\Unix\\Listener',
            $detector->getListenerInstance()
        );

        $detector->setEnvironment('Unix');
        $this->assertInstanceOf(
            'Atatusoft\\PhpKeyListener\\Environment\\Unix\\Listener',
            $detector->getListenerInstance()
        );
    }

}