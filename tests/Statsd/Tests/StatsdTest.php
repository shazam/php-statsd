<?php

/**
 * @package Statsd
 * @subpackage Tests
 * @author toni <toni.lopez@shazam.com>
 */

namespace Statsd\Tests;

use Statsd\Statsd;
use Statsd\StatsdClient\Configuration;

/**
 * @package Statsd
 * @subpackage Tests
 */

class StatsdTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Monolog\Logger
     */
    private $logger;

    /**
     * @var Configuration
     */
    private $configuration;

    public function setUp()
    {
        $this->logger = $this->getMock('\Monolog\Logger');

        $this->configuration = new Configuration();
    }

    /**
     * @return array
     */
    public function providerSentStatException()
    {
        return array(
            array(
                'namespace' => null,
                'timestamp' => null
            ),
            array(
                'namespace' => 'not a valid namespace',
                'timestamp' => null
            ),
            array(
                'namespace' => 'valid.namespace_1',
                'timestamp' => 'not a number'
            ),
            array(
                'namespace' => 'valid.namespace_1',
                'timestamp' => null
            )
        );
    }

    /**
     * @dataProvider providerSentStatException
     * @expectedException \Exception
     * @param string $namespace
     * @param mixed $timestamp
     */
    public function testSendStatException($namespace, $timestamp)
    {
        $statsd = new Statsd($this->configuration, $this->logger);

        $statsd->sendStat($namespace, $timestamp);
    }
}
