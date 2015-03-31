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
}
