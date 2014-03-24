<?php

/**
 * @package Tests\Infratools\TwitterHose\Test\Integration\Cases
 * @subpackage Utils
 * @author toni <toni.lopez@shazam.com>
 */

namespace Tests\Infratools\TwitterHose\Integration\Cases\Utils;

use Infratools\TwitterHose\Utils\GraphiteClient;

/**
 * @package Tests\Infratools\TwitterHose\Test\Integration\Cases
 * @subpackage Utils
 */

class GraphiteClientTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Monolog\Logger
     */
    private $log;

    public function setUp()
    {
        $this->log = $this->getMock('\Monolog\Logger');
    }

    /**
     * Cases:
     * - Hostname is not a string
     * - Hostname is not a valid URL
     * - Port has to be an integer
     * - Invalid prefix
     * @return array
     */
    public function providerCanConstructException()
    {
        return array(
            array(
                'host' => 12,
                'port' => 100,
                'prefix' => 'stats.twitterhose'
            ),
            array(
                'host' => 'that is not an url',
                'port' => 100,
                'prefix' => 'stats.twitterhose'
            ),
            array(
                'host' => 'valid.hostname.net',
                'port' => '3',
                'prefix' => 'stats.twitterhose'
            ),
            array(
                'host' => 'valid.hostname.net',
                'port' => 2,
                'prefix' => 'stats.twitter-hose'
            )
        );
    }

    /**
     * @param mixed $host
     * @param mixed $port
     * @param mixed $prefix
     * @dataProvider providerCanConstructException
     * @expectedException \Exception
     */
    public function testCanConstructException($host, $port, $prefix)
    {
        new GraphiteClient($host, $port, $prefix, $this->log);
    }

    /**
     * Cases:
     * - Hostname
     * - IP
     * - No subsections
     * @return array
     */
    public function providerCanConstruct()
    {
        return array(
            array(
                'host' => 'google.com',
                'port' => 100,
                'prefix' => 'stats.twitterhose'
            ),
            array(
                'host' => '192.168.1.1',
                'port' => 100,
                'prefix' => 'stats.twitterhose'
            ),
            array(
                'host' => '192.168.1.1',
                'port' => 100,
                'prefix' => 'stats'
            )
        );
    }

    /**
     * @param mixed $host
     * @param mixed $port
     * @param mixed $prefix
     * @dataProvider providerCanConstruct
     */
    public function testCanConstruct($host, $port, $prefix)
    {
        new GraphiteClient($host, $port, $prefix, $this->log);
    }

    /**
     * Cases:
     * - namespace is not a string
     * - namespace is not valid
     * - timestamp not a number
     * - timestamp is null
     * @return array
     */
    public function providerAddTripleException()
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
     * @dataProvider providerAddTripleException
     * @expectedException \Exception
     * @param string $namespace
     * @param mixed $timestamp
     */
    public function testAddTripleException($namespace, $timestamp)
    {
        $graphiteClient = new GraphiteClient('this.is.a.host', 12, 'stats.twitterhose', $this->log);

        $graphiteClient->addTriple($namespace, $timestamp);
    }
}
