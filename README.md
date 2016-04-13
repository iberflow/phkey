# PHKey
PHP based command line key listener. 
![Example](https://iber.lt/assets/images/projects/phkey.gif)
------------

This library provides an API to capture keys from the terminal. It currently supports latin-basic range of characters such as direction keys, function keys (F*), enter, space, insert, delete, backspace, escape, a-z, etc.

## Installation
You can either download this library as a zip, or simply install it via composer:
```
composer require iber/phkey
```

## Limitations
This package only works on UNIX/Linux based systems since the Windows PHP version doesn't not support the readline extension.

## Example

##### Capture all keys
```php
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
```

## License
Licensed under MIT.
