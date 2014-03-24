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
