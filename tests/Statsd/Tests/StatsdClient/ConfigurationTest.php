<?php

/**
 * @package Statsd\Tests
 * @subpackage StatsdClient
 * @author toni <toni.lopez@shazam.com>
 */

namespace Statsd\Tests\StatsdClient;

use Statsd\StatsdClient\Configuration;

/**
 * @package Statsd\Tests
 * @subpackage StatsdClient
 */

class ConfigurationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @return array
     */
    public function providerWrongHost()
    {
        return array(
            'Host is not a string' => array('host' => 12),
            'Host is not a valid URL' => array('host' => 'not a url')
        );
    }

    /**
     * @dataProvider providerWrongHost
     * @expectedException \Exception
     * @param mixed $host
     */
    public function testSetWrongHost($host)
    {
        $configuration = new Configuration();
        $configuration->setHost($host);
    }

    /**
     * @return array
     */
    public function providerHost()
    {
        return array(
            'Host is an IP' => array('host' => '127.0.0.1'),
            'Host is a URL' => array('host' => 'shazam.com'),
            'Host is a localhost' => array('host' => 'localhost')
        );
    }

    /**
     * @dataProvider providerHost
     * @param mixed $host
     */
    public function testSetHost($host)
    {
        $configuration = new Configuration();
        $configuration->setHost($host);

        $this->assertSame($host, $configuration->getHost(), 'Unexpected host.');
    }

    /**
     * @return array
     */
    public function providerWrongPort()
    {
        return array(
            'Port is a stirng' => array('port' => 'port'),
            'Port is a double' => array('port' => 4.5),
            'Port is 0' => array('port' => 4.5),
            'Port is a negative int' => array('port' => -2)
        );
    }

    /**
     * @dataProvider providerWrongPort
     * @expectedException \Exception
     * @param mixed $port
     */
    public function testSetWrongPort($port)
    {
        $configuration = new Configuration();
        $configuration->setPort($port);
    }

    public function testSetPort()
    {
        $port = 9999;
        $configuration = new Configuration();
        $configuration->setPort($port);

        $this->assertSame($port, $configuration->getPort(), 'Unexpected port.');
    }

    public function testGetDefaultPort()
    {
        $configuration = new Configuration();

        $this->assertSame(
            Configuration::DEFAULT_PORT,
            $configuration->getPort(),
            'Unexpected port.'
        );
    }

    /**
     * @return array
     */
    public function providerWrongNamespace()
    {
        return array(
            'Namespace is not a string' => array('namespace' => 123),
            'Namespace is not a valid URL' => array('namespace' => 'name/space')
        );
    }

    /**
     * @dataProvider providerWrongNamespace
     * @expectedException \Exception
     * @param mixed $namespace
     */
    public function testSetWrongNamespace($namespace)
    {
        $configuration = new Configuration();
        $configuration->setNamespace($namespace);
    }

    /**
     * @return array
     */
    public function providerNamespace()
    {
        return array(
            'Valid namespace with dots' => array('namespace' => 'name.space'),
            'Valid namespace without dots' => array('namespace' => 'namespace')
        );
    }

    /**
     * @dataProvider providerNamespace
     * @param mixed $namespace
     */
    public function testSeNamespace($namespace)
    {
        $configuration = new Configuration();
        $configuration->setNamespace($namespace);

        $this->assertSame(
            $namespace,
            $configuration->getNamespace(),
            'Unexpected namespace.'
        );
    }
}
