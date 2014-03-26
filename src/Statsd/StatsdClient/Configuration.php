<?php

/**
 * @package Statsd
 * @subpackage StatsdClient
 * @author toni <toni.lopez@shazam.com>
 */

namespace Statsd\StatsdClient;

use Exception;

/**
 * Represents a configuration for a Statsd client.
 *
 * @package Statsd
 * @subpackage StatsdClient
 */

class Configuration
{
    /**
     * @const int
     */
    const DEFAULT_PORT = 8126;

    /**
     * @const string
     */
    const VALID_NAMESPACE_PATTERN = '/^[a-zA-Z0-9]+(\.[a-zA-Z0-9]+)*$/';

    /**
     * @var string
     */
    private $host;

    /**
     * @var string
     */
    private $port;

    /**
     * @var string
     */
    private $namespace;

    /**
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * @return string
     */
    public function getNamespace()
    {
        return $this->namespace;
    }

    /**
     * @return string
     */
    public function getPort()
    {
        return $this->port === null ? self::DEFAULT_PORT : $this->port;
    }

    /**
     * @param string $host
     * @return $this
     * @throws Exception when host is not a valid host or IP
     */
    public function setHost($host)
    {
        if (!is_string($host)
            || filter_var("http://$host", FILTER_VALIDATE_URL) === false
            && filter_var($host, FILTER_VALIDATE_IP) === false
        ) {
            throw new Exception("'$host' does not seem to be a valid host or IP.");
        }

        $this->host = $host;

        return $this;
    }

    /**
     * @param int $port
     * @return $this
     * @throws Exception when port is not an integer
     */
    public function setPort($port)
    {
        if (!is_int($port) || $port < 1) {
            throw new Exception("'$port' has to be an integer.");
        }

        $this->port = $port;

        return $this;
    }

    /**
     * @param string $namespace
     * @return $this
     * @throws Exception when namespace does not follow pattern
     */
    public function setNamespace($namespace)
    {
        if (!is_string($namespace)
            || !preg_match(self::VALID_NAMESPACE_PATTERN, $namespace)
        ) {
            throw new \Exception(
                "'$namespace' does not seem to be a valid namespace. Use a string of "
                . 'alphanumerics and dots, e.g. "stats.infratools.twitterhose".'
            );
        }

        $this->namespace = $namespace;

        return $this;
    }
}
