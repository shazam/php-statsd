<?php

/**
 * Copyright 2014 Shazam Entertainment Limited
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may not use this 
 * file except in compliance with the License.
 *
 * You may obtain a copy of the License at http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software distributed under 
 * the License is distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR 
 * CONDITIONS OF ANY KIND, either express or implied. See the License for the specific 
 * language governing permissions and limitations under the License.
 *
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

    /**
     * @return $messages
     */
    public function sendStats()
    {
        $namespace = $this->configuration->getNamespace();

        $socketUrl = sprintf('udp://' . $this->configuration->getHost());

        $messages = array();
        foreach ($this->stats as $key => $stat) {
            $msg = $namespace . '.' . $this->statToString($stat);
            $messages[] = $msg;

            if (null !== $this->logger) {
                $this->logger->info('Sending metrics: ' . $msg);
            }

            $socket = fsockopen($socketUrl, $this->configuration->getPort());
            fwrite($socket, $msg);
            fclose($socket);
            unset($this->stats[$key]);
        }

        return $messages;
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
