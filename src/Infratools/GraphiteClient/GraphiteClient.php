<?php

/**
 * @package Infratools\GraphiteClient
 * @author toni <toni.lopez@shazam.com>
 */

namespace Infratools\GraphiteClient;

use \Monolog\Logger;

/**
 * Client to write metrics into Graphite.
 *
 * @package Infratools\GraphiteClient
 */

class GraphiteClient
{
    /**
     * @const string
     */
    const VALID_NAMESPACE_PATTERN = '/^[a-zA-Z0-9_]+(\.[a-zA-Z0-9_]+)*$/';

    /**
     * @var string
     */
    private $prefix;

    /**
     * @var Resource
     */
    private $socket;

    /**
     * @var Logger|null
     */
    private $log;

    /**
     * @param string $host
     * @param string $port
     * @param string $prefix
     * @param Logger|null $log
     * @throws \Exception The port has to be an integer
     * @throws \Exception The host has to be a valid URL or IP
     * @throws \Exception The prefix is not valid
     */
    public function __construct($host, $port, $prefix, $log = null)
    {
        if (!is_int($port)) {
            throw new \Exception("'$port' has to be an integer.");
        } elseif (!is_string($host)
            || filter_var("http://$host", FILTER_VALIDATE_URL) === false
            && filter_var($host, FILTER_VALIDATE_IP) === false
        ) {
            throw new \Exception("'$host' does not seem to be a valid host or IP.");
        } elseif (!preg_match('/^[a-zA-Z0-9]+(\.[a-zA-Z0-9]+)*$/', $prefix)) {
            throw new \Exception(
                "'$prefix' does not seem to be a valid prefix. Use a string of "
                    . 'alphanumerics and dots, e.g. "stats.infratools.twitterhose".'
            );
        }

        $this->prefix = $prefix;
        $this->socket = fsockopen("udp://$host", $port);
        $this->log = $log;

        register_shutdown_function(array(&$this), 'close');
    }

    /**
     * @param string $namespace
     * @param null|int|double $value
     * @throws \Exception Namespace is not valid
     * @throws \Exception Value has to be numeric
     */
    public function addTriple($namespace, $value = 1)
    {
        if (!preg_match(self::VALID_NAMESPACE_PATTERN, $namespace)) {
            throw new \Exception(
                "'$namespace' does not seem to be a valid prefix. Use a string of "
                    . 'alphanumerics and dots, e.g. "stats.infratools.twitterhose".'
            );
        } elseif (!is_numeric($value)) {
            throw new \Exception("Value has to be numeric. Got '$value'.");
        }

        $msg = "{$this->prefix}.$namespace:$value|ms";

        if (null !== $this->log) {
            $this->log->info('Sending metrics: ' . $msg);
        }

        fwrite($this->socket, $msg);
    }

    public function close()
    {
        fclose($this->socket);
    }
}
