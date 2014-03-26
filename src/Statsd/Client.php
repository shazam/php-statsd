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
     * @var Configuration
     */
    private $configuration;

    /**
     * @var Logger|null
     */
    private $logger;

    /**
     * @var array
     */
    private $stats = array();

    /**
     * @param Configuration $configuration
     * @param Logger|null $logger
     */
    public function __construct(Configuration $configuration, $logger = null)
    {
        $this->configuration = $configuration;
        $this->logger = $logger;
    }

    /**
     * @param Stat $stat
     */
    public function addStat(Stat $stat)
    {
        $this->stats[] = $stat;
    }

    /**
     * @param array $stats
     */
    public function addStats(array $stats)
    {
        foreach ($stats as $stat) {
            $this->addStat($stat);
        }
    }

    public function sendStats()
    {
        $namespace = $this->configuration->getNamespace();

        $socketUrl = sprintf('udp://' . $this->configuration->getHost());
        $socket = fsockopen($socketUrl, $this->configuration->getPort());

        foreach ($this->stats as $key => $stat) {
            $msg = $namespace . '.' . (string) $stat;

            if (null !== $this->logger) {
                $this->logger->info('Sending metrics: ' . $msg);
            }

            fwrite($socket, $msg);
            unset($this->stats[$key]);
        }

        fclose($socket);
    }
}
