<?php

/**
 * @package Statsd
 * @author toni <toni.lopez@shazam.com>
 */

namespace Statsd;

use Monolog\Logger;
use Statsd\StatsdClient\Configuration;

/**
 * Library to send stats to statsd.
 *
 * @package Statsd
 */

class Statsd
{
    /**
     * @const string
     */
    public static $VALID_NAMESPACE_PATTERN = '/^[a-zA-Z0-9_]+(\.[a-zA-Z0-9_]+)*$/';

    /**
     * @var string
     */
    private $namespace;

    /**
     * @var Resource
     */
    private $socket;

    /**
     * @var Logger|null
     */
    private $logger;

    /**
     * @param Configuration $configuration
     * @param Logger|null $logger
     */
    public function __construct(Configuration $configuration, $logger = null)
    {
        $this->namespace = $configuration->getNamespace();

        $socketUrl = sprintf('udp://' . $configuration->getHost());
        $this->socket = fsockopen($socketUrl, $configuration->getPort());

        $this->logger = $logger;
    }

    /**
     * @param string $namespace
     * @param null|int|double $value
     * @throws \Exception Namespace is not valid
     * @throws \Exception Value has to be numeric
     */
    public function sendStat($namespace, $value = 1)
    {
        $this->sanityCheck($namespace, $value);

        $msg = "{$this->namespace}.$namespace:$value|ms";

        if (null !== $this->logger) {
            $this->logger->info('Sending metrics: ' . $msg);
        }

        fwrite($this->socket, $msg);
    }

    /**
     * @param string $namespace
     * @param null|int|double $value
     * @throws Exception Namespace is not valid
     * @throws Exception Value has to be numeric
     */
    private function sanityCheck($namespace, $value)
    {
        if (!preg_match(self::$VALID_NAMESPACE_PATTERN, $namespace)) {
            throw new Exception(
                "'$namespace' does not seem to be a valid prefix. Use a string of "
                . 'alphanumerics and dots, e.g. "stats.infratools.twitterhose".'
            );
        } elseif (!is_numeric($value)) {
            throw new Exception("Value has to be numeric. Got '$value'.");
        }
    }
}
