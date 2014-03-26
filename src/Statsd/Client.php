<?php

/**
 * @package Statsd
 * @author toni <toni.lopez@shazam.com>
 */

namespace Statsd;

use Monolog\Logger;
use Statsd\Client\Configuration;
use Statsd\Domain\Stat;

/**
 * Library to send stats to statsd.
 *
 * @package Statsd
 */

class Client
{
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
     * @param Stat $stat
     */
    public function sendStat(Stat $stat)
    {
        $msg = sprintf(
            "%s:%s|%s",
            $this->namespace . '.' . $stat->getNamespace(),
            $stat->getValue(),
            $stat->getType()
        );

        if (null !== $this->logger) {
            $this->logger->info('Sending metrics: ' . $msg);
        }

        fwrite($this->socket, $msg);
    }
}
