# php-statsd
Simple PHP library to send stats to statsd.

Installation
------------
Add the dependency to your composer.json.

```javascript
{
    "repositories": [
        {
            "url": "git@gitlab.uk.shazamteam.net:infra-tools/php-statsd.git",
            "type": "git"
        }
    ],
    "require": {
        "infratools/php-statsd": "1.*"
    }
}
```

Usage
-----
An example of how to send metrics of how long it takes to load a page.

```php
<?php

use Statsd;

// $config = array(...);
// $path = ...

// initialize client
$configuration = new Statsd\Client\Configuration();
$configuration->setHost($config['host'])
    ->setNamespace($config['namespace']);

$statsClient = new Statsd\Client($configuration);

// send stats
$statsClient->sendStat(
    new Statsd\Domain\Stat(
        'endpoints.' . $path, // that will be your stat namespace
        $executionTime, // calculate it in microseconds
        Statsd\Domain\Stat::TIME_MS
    )
);

```

You can use TIME_MS, COUNT, GAUGE or SET (ms, c, g, s) as type of stats.

Configuration
-------------
 * A host to push metrics (use 127.0.0.1 if you have netpipes installed in your box).
 * A port (by default, 8126).
 * A namespace (where all your metrics will be added. Use "." to separate folders.
 * Optionally, a Monolog\Logger object, to log the metrics.

An example of a config file for that client could be:

```yaml
stats:
  enable: true
  client:
    host: 127.0.0.1
    namespace: infratools.twitterhose
```
