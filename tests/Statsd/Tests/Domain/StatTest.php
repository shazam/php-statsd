<?php

/**
 * @package Statsd\Tests
 * @subpackage Domain
 * @author toni <toni.lopez@shazam.com>
 */

namespace Statsd\Tests;

use Statsd\Domain\Stat;

/**
 * @package Statsd\Tests
 * @subpackage Domain
 */

class StatTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @return array
     */
    public function providerSanityCheckError()
    {
        return array(
            'Namespace is null' => array(
                'namespace' => null,
                'value' => null,
                'type' => null
            ),
            'Namespace is not following pattern' => array(
                'namespace' => 'not a valid namespace',
                'value' => null,
                'type' => null
            ),
            'Value is not numeric' => array(
                'namespace' => 'valid.namespace_1',
                'value' => 'not a number',
                'type' => null
            ),
            'Value is null' => array(
                'namespace' => 'valid.namespace_1',
                'value' => null,
                'type' => null
            ),
            'Type is not a valid one' => array(
                'namespace' => 'valid.namespace_1',
                'value' => 12.3,
                'type' => 'seconds'
            )
        );
    }

    /**
     * @dataProvider providerSanityCheckError
     * @expectedException \Exception
     * @param mixed $namespace
     * @param mixed $value
     * @param mixed $type
     */
    public function testSanityCheckFails($namespace, $value, $type)
    {
        new Stat($namespace, $value, $type);
    }

    /**
     * @return array
     */
    public function providerGetNamespace()
    {
        return array(
            'Namespace no dots' => array('namespace' => 'namespace'),
            'Namespace with dot' => array('namespace' => 'name.space'),
            'Namespace with dots' => array('namespace' => 'name.space.valid'),
            'Namespace with number' => array('namespace' => 'name.space.valid2')
        );
    }

    /**
     * @dataProvider providerGetNamespace
     * @param mixed $namespace
     */
    public function testGetNamespace($namespace)
    {
        $stat = new Stat($namespace, 1);

        $this->assertSame(
            $stat->getNamespace(),
            $namespace,
            'Unexpected namespace from stat.'
        );
    }

    /**
     * @return array
     */
    public function providerGetValue()
    {
        return array(
            'Integer' => array('value' => 12),
            'Zero' => array('value' => 0),
            'Negative integer' => array('value' => -23),
            'Float' => array('value' => 2.3),
            'Negative float' => array('value' => -23.1),
            'Positive number' => array('value' => "+3")
        );
    }

    /**
     * @dataProvider providerGetValue
     * @param mixed $value
     */
    public function testGetValue($value)
    {
        $stat = new Stat('namespace', $value);

        $this->assertSame(
            $stat->getValue(),
            $value,
            'Unexpected value from stat.'
        );
    }

    /**
     * @return array
     */
    public function providerGetType()
    {
        return array(
            'Time ms' => array('type' => Stat::TIME_MS),
            'Gauge' => array('type' => Stat::GAUGE),
            'Set' => array('type' => Stat::SET),
            'Count' => array('type' => Stat::COUNT)
        );
    }

    /**
     * @dataProvider providerGetType
     * @param string $type
     */
    public function testGetType($type)
    {
        $stat = new Stat('namespace', 1, $type);

        $this->assertSame(
            $stat->getType(),
            $type,
            'Unexpected type from stat.'
        );
    }

    public function testGetDefaultType()
    {
        $stat = new Stat('namespace', 1);

        $this->assertSame(
            $stat->getType(),
            Stat::TIME_MS,
            'Unexpected default type from stat.'
        );
    }

    public function testToString()
    {
        $stat = new Stat('name.space', 23.4, Stat::TIME_MS);
        $this->assertSame(
            (string) $stat,
            'name.space:23.4|ms',
            'Unexpected string cast of the stat.'
        );
    }
}
