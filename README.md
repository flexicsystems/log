Cookie Component
========================

The Log component defines an object-oriented layer for the Log handling.

Resources
---------

* [Documentation](https://www.themepoint.de/)
* [Issue reporting](https://github.com/flexicsystems/flexic/issues)

Predefined
--------

```php
# Loglevels

Flexic\Log\Logger::NONE; # 0
Flexic\Log\Logger::DEBUG; # 100
Flexic\Log\Logger::INFO; # 200
Flexic\Log\Logger::NOTICE; # 250
Flexic\Log\Logger::WARNING; # 300
Flexic\Log\Logger::ERROR; # 400
Flexic\Log\Logger::CRITICAL; # 500
Flexic\Log\Logger::ALERT; # 550
Flexic\Log\Logger::EMERGENCY; # 600
Flexic\Log\Logger::EXCEPTION; # 700
```

```
Format markers

#:LEVEL:#       ->  Loglevel of message
#:MESSAGE:#     ->  Message
#:CONTEXT:#     ->  Context
#:COUNT:#       ->  Count of log message in this session
#:SESSION:#     ->  Id of session
#:TIME:#        ->  Formatted Timestamp
#:TIME_INT:#    ->  Timestamp as int
```

Examples
--------

```php
Flexic\Log\Logger::log('Message to log', Flexic\Log\Logger::DEBUG, '/var/log/log.log', array('isFailed', 'isError'));

# Output [/var/log/log.log]: [DEBUG] Message to log { isFailed | isError }
```

```php
try {
    ...
} catch (Exception $e) {
    Flexic\Log\Logger::logException($e, '/var/log/exceptions.log', array('isFailed', 'isError'));
}

# Output [/var/log/exceptions.log]: [EXCEPTION] Exception message { isFailed | isError }
```

```php
# Get the log count of current session

Flexic\Log\Logger::getCount();
```

```php
# Add own loglevel

Flexic\Log\Logger::$levels[800] = 'CUSTOM_LEVEL';
```

```php
# Change defaults

Flexic\Log\Logger::$format = '[#:LEVEL:#] #:MESSAGE:# { #:CONTEXT:# }';
Flexic\Log\Logger::$logLevel = Flexic\Log\Logger::DEBUG;
Flexic\Log\Logger::$fallbackFile = '/var/log/log.log';
Flexic\Log\Logger::$contextSeparator = ' / ';
Flexic\Log\Logger::$timeFormat = 'Y/m/d';
Flexic\Log\Logger::$emptyLineAfter = true;
```
