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

$start = microtime(true);

$app = new Silex\Application();

// initialize client
$app['php-statsd'] = $app->share(function ($app) {
    if (!$app['config']['environment']['metrics']) {
        return null;
    }

    $statdsConfig = $app['config']['properties']['statsd'];
    return new Statsd(
        $statdsConfig['host'],
        (int) $statdsConfig['port'],
        $statdsConfig['prefix'],
        $app['monolog']
    );
});

// send stats
$app->after(
    function(Request $request) use ($app, $start) {
        if (null !== $app['statds']) {
            $path = str_replace('/', '_', substr($request->getPathInfo(), 1));
            $app['statsd']->sendStat('endpoints.' . $path, microtime(true) - $start);
        }
    }
);

$app->run();

```

Configuration
-------------
The constructor of the Statdd object needs:
 * A host to push metrics (use 127.0.0.1 if you have netpipes installed in your box).
 * A port (8126).
 * A prefix (where all your metrics will be added. Use "." to separate folders.
 * Optionally, a Monolog\Logger object, to log the metrics.

An examlpe of a config file for that client could be:

```yaml
statsd:
  host: 127.0.0.1
  port: 8126
  prefix: infratools.twitterhose
```
