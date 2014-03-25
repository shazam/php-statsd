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
$app['Statsd\Statsd'] = $app->share(function ($app) {
    $configuration = new Configuration();
    $configuration->setHost($app['config']['environment']['stats']['client']['host'])
        ->setNamespace($app['config']['environment']['stats']['client']['namespace']);
    return new Statsd($configuration, $app['monolog']);
});

// send stats
$app->after(
    function(Request $request) use ($app, $start) {
        if ($app['config']['environment']['stats']['enable']) {
            $path = str_replace('/', '_', substr($request->getPathInfo(), 1));
            $path = substr($path, strlen($app['config']['environment']['root-point']));
            $path  = empty($path) ? '_' : $path;
            if (isset($app['config']['properties']['stats']['paths'][$path])) {
                $app['Statsd\Statsd']->sendStat('endpoints.' . $path, microtime(true) - $start);
            }
        }
    }
);

$app->run();

```

Configuration
-------------
The constructor of the Statdd object needs:
 * A host to push metrics (use 127.0.0.1 if you have netpipes installed in your box).
 * A port (by default, 8126).
 * A namespace (where all your metrics will be added. Use "." to separate folders.
 * Optionally, a Monolog\Logger object, to log the metrics.

An examlpe of a config file for that client could be:

```yaml
stats:
  enable: true
  client:
    host: 127.0.0.1
    namespace: infratools.twitterhose
```
