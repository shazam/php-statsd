<?php

/**
 * @package Statsd
 * @subpackage Domain
 * @author toni <toni.lopez@shazam.com>
 */

namespace Statsd\Domain;

use Exception;

/**
 * Domain object that represents a stat to be sent to statsd.
 *
 * @package Statsd
 * @subpackage Domain
 */

class Stat
{
    /**
     * @const string
     */
    const VALID_NAMESPACE_PATTERN = '/^[a-zA-Z0-9_]+(\.[a-zA-Z0-9_]+)*$/';

    /**
     * @const string
     */
    const TIME_MS = 'ms';

    /**
     * @const string
     */
    const COUNT = 'c';

    /**
     * @const string
     */
    const GAUGE = 'g';

    /**
     * @const string
     */
    const SET = 's';

    /**
     * @var string
     */
    private $namespace;

    /**
     * @var string
     */
    private $value;

    /**
     * @var string
     */
    private $type;

    /**
     * @param string $namespace
     * @param string $value
     * @param string|null $type
     */
    public function __construct($namespace, $value, $type = null)
    {
        $this->namespace = $namespace;
        $this->value = $value;
        $this->type = $type === null ? self::TIME_MS : $type;

        $this->sanityCheck();
    }

    /**
     * @throws Exception when namespace is not valid
     * @throws Exception when value is not valid
     */
    private function sanityCheck()
    {
        if (!preg_match(self::VALID_NAMESPACE_PATTERN, $this->namespace)) {
            throw new Exception(
                "'{$this->namespace}' does not seem to be a valid prefix. Use a string of "
                . 'alphanumerics and dots, e.g. "stats.infratools.twitterhose".'
            );
        } elseif (!is_numeric($this->value)) {
            throw new Exception("Value has to be numeric. Got '{$this->value}'.");
        } elseif (!in_array($this->type, array(self::COUNT, self::GAUGE, self::SET, self::TIME_MS))) {
            throw new Exception("'{$this->type}' is not a valid type of stat. Use s, c, g, ms.");
        }
    }

    /**
     * @return string
     */
    public function getNamespace()
    {
        return $this->namespace;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }
}
