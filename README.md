# Graphite PHP Client
A simple PHP library that allows you to send metrics to Graphite.

Installation
------------
Add the dependency to your composer.json.

```javascript
{
    "repositories": [
        {
            "url": "git@gitlab.uk.shazamteam.net:infratools/graphite-client.git",
            "type": "git"
        }
    ],
    "require": {
        "infratools/graphiteClient": "*"
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
$app['Utils\GraphiteClient'] = $app->share(function ($app) {
    if (!$app['config']['environment']['metrics']) {
        return null;
    }

    $graphiteConfig = $app['config']['properties']['graphite'];
    return new GraphiteClient(
        $graphiteConfig['host'],
        (int) $graphiteConfig['port'],
        $graphiteConfig['prefix'],
        $app['monolog']
    );
});

// send metrics
$app->after(
    function(Request $request) use ($app, $start) {
        $lastTweet = $app['Redis\TweetLoader']->getTweets(1, $app['Redis\TweetLoader']->getLastPost());
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s', strtotime($lastTweet[0]['time'])) . ' GMT');

        if (null !== $app['Utils\GraphiteClient']) {
            $path = str_replace('/', '_', substr($request->getPathInfo(), 1));
            $app['Utils\GraphiteClient']->addTriple('endpoints.' . $path, microtime(true) - $start);
        }
    }
);

$app->run();

```

The constructor of the GraphiteClient object needs:
 * A host to push metrics (use 127.0.0.1 if you have netpipes installed in your box).
 * A port (8126).
 * A prefix (where all your metrics will be added. Use "." to separate folders.
 * Optionally, a Monolog\Logger object, to log the metrics.

An examlpe of a config file for that client could be:

```yaml
graphite:
  host: 127.0.0.1
  port: 8126
  prefix: infratools.twitterhose
```
