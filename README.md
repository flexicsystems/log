Log Component
========================

The Log component defines an object-oriented layer for the Log handling.

Resources
---------

* [Documentation](https://www.themepoint.de/)
* [Issue reporting](https://github.com/flexicsystems/flexic/issues)

Examples
--------

```php
# Configure a logger

$logger = new \Flexic\Log\Logger('test');

$logger->pushHandler(new \Flexic\Log\Handler\FileHandler('/path/to/log/file/'));

$logger->log(\Flexic\Log\LogLevel::DEBUG, 'Message with {placeholder}', array('placeholder' => 'text'));
```

```php
# Logger functions

$logger->emergency('Message with {placeholder}', array('placeholder' => 'text'));
$logger->alert('Message with {placeholder}', array('placeholder' => 'text'));
$logger->critical('Message with {placeholder}', array('placeholder' => 'text'));
$logger->error('Message with {placeholder}', array('placeholder' => 'text'));
$logger->warning('Message with {placeholder}', array('placeholder' => 'text'));
$logger->notice('Message with {placeholder}', array('placeholder' => 'text'));
$logger->info('Message with {placeholder}', array('placeholder' => 'text'));
$logger->debug('Message with {placeholder}', array('placeholder' => 'text'));
$logger->exception('Message with {placeholder}', array('placeholder' => 'text'));
$logger->log(\Flexic\Log\LogLevel::DEBUG, 'Message with {placeholder}', array('placeholder' => 'text'));
```

```php
# Configure a static logger

$logger = new \Flexic\Log\Logger('test');
$logger->pushHandler(new \Flexic\Log\Handler\FileHandler('/path/to/log/file/'));

Flexic\Log\StaticLogger::setLogger($logger);

Flexic\Log\StaticLogger::log(\Flexic\Log\LogLevel::DEBUG, 'Message with {placeholder}', array('placeholder' => 'text'));
```
