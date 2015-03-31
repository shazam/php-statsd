<?php

/**
 * @package Statsd
 * @author toni <toni.lopez@shazam.com>
 */

namespace Statsd;

use Exception;
use Monolog\Logger;
use Statsd\Client\Configuration;

/**
 * Library to send stats to statsd.
 *
 * @package Statsd
 */

class Client
{
    /**
     * @var array
     */
    private static $validTypes = array('c', 's', 'g', 'ms');

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
     * @param array $stat
     */
    public function addStat(array $stat)
    {
        try {
            $this->isValidStat($stat);
        } catch (Exception $e) {
            throw new Exception('Stat is not valid: ' . $e->getMessage());
        }

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
            $msg = $namespace . '.' . $this->statToString($stat);

            if (null !== $this->logger) {
                $this->logger->info('Sending metrics: ' . $msg);
            }

            fwrite($socket, $msg);
            unset($this->stats[$key]);
        }

        fclose($socket);
    }

    /**
     * @param array $stat
     */
    private function isValidStat(array $stat)
    {
        if (!isset($stat['namespace']) || !isset($stat['value']) || !isset($stat['type'])) {
            throw new Exception('namespace, type and value are mandatory keys.');
        } elseif (!preg_match('/^[a-zA-Z0-9_]+(\.[a-zA-Z0-9_]+)*$/', $stat['namespace'])) {
            throw new Exception(
                "'$stat[namespace]' does not seem to be a valid prefix. Use a string of "
                . 'alphanumerics and dots, e.g. "stats.infratools.twitterhose".'
            );
        } elseif (!is_numeric($stat['value'])) {
            throw new Exception("Value has to be numeric. Got '$namespace[value]'.");
        } elseif (!in_array($stat['type'], self::$validTypes)) {
            throw new Exception("'$stat[type]' is not a valid type of stat. Use s, c, g, ms.");
        }
    }

    /**
     * @param array $stat
     * @return string
     */
    private function statToString($stat)
    {
        return sprintf('%s:%s|%s', $stat['namespace'], $stat['value'], $stat['type']);
    }
}
