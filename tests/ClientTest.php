<?php

/**
 * @package Statsd\Tests
 * @author toni <toni.lopez@shazam.com>
 */

namespace Statsd\Tests;

use Statsd\Client;
use Statsd\Client\Configuration;
use Exception;

/**
 * @package Statsd\Tests
 */

class ClientTest extends \PHPUnit_Framework_TestCase
{
    public function providerWrongStat()
    {
        return array(
            array('stat' => array()),
            array('stat' => array('namespace' => '', 'value' => '')),
            array('stat' => array('namespace' => 'a.namespace.W£@£$%', 'type' => 'c', 'value' => 23)),
            array('stat' => array('namespace' => 'a.namespace', 'type' => 'something', 'value' => 23)),
            array('stat' => array('namespace' => 'a.namespace', 'type' => 'c', 'value' => 'halal'))
        );
    }

    /**
     * @dataProvider providerWrongStat
     * @expectedException Exception
     */
    public function testAddWrongStat(array $stat)
    {
        $client = new Client(new Configuration());
        $client->addStat($stat);
    }

    public function testSend()
    {
        $configuration = new Configuration();
        $configuration->setHost('127.0.0.1')->setNamespace('base.name')->setPort(123456);

        $client = new Client($configuration);
        $client->addStats(
            array(
                array('namespace' => 'some.namespace', 'value' => 12, 'type' => 'ms'),
                array('namespace' => 'some.namespace2', 'value' => 1, 'type' => 'c')
            )
        );

        $messages = $client->sendStats();

        $expected = array(
            'base.name.some.namespace:12|ms',
            'base.name.some.namespace2:1|c'
        );
        $this->assertSame($messages, $expected);
    }
}
