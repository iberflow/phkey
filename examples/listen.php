<?php

include __DIR__ . '/../vendor/autoload.php';

use \Iber\Phkey\Events\KeyPressEvent;
use \Iber\Phkey\Environment\Detector;

$detect = new Detector();
$listener = $detect->getListenerInstance();

$eventDispatcher = $listener->getEventDispatcher();

$eventDispatcher->addListener('key:press', function(KeyPressEvent $event) {
    echo $event->getKey(), PHP_EOL;
});

$eventDispatcher->addListener('key:enter', function(KeyPressEvent $event) use ($eventDispatcher) {
    echo 'Key "', $event->getKey(), '" was pressed. Quitting listener.', PHP_EOL;

    // notify the listener to stop
    $eventDispatcher->dispatch('key:stop:listening');
});

$listener->start();