<?php

namespace N98\JUnitXml;

class TestSuiteElement extends \DOMElement
{
    public function __construct()
    {
        parent::__construct('testsuite');
    }

    /**
     * Full class name of the test for non-aggregated testsuite documents.
     * Class name without the package for aggregated testsuites documents
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->setAttribute('name', $name);
    }

    /**
     * Time of test executing.
     *
     * @param \DateTime $timestamp
     */
    public function setTimestamp(\DateTime $timestamp)
    {
        $this->setAttribute('timestamp', $timestamp->format(\DateTime::ISO8601));
    }

    /**
     * Test time duration in seconds
     *
     * @param float $duration
     */
    public function setTime($duration)
    {
        $this->setAttribute('time', $duration);
    }

    /**
     * Hostname where tests was executed
     *
     * @param string $hostname
     */
    public function setHostname($hostname)
    {
        $this->setAttribute('hostname', $hostname);
    }

    /**
     * @return TestCaseElement
     */
    public function addTestCase()
    {
        $testCaseElement = new TestCaseElement();
        $this->appendChild($testCaseElement);

        $testCaseElement->setAttribute('errors', '0');
        $testCaseElement->setAttribute('failures', '0');

        $this->incrementTestCount();

        return $testCaseElement;
    }

    private function incrementTestCount()
    {
        if ($this->hasAttribute('tests')) {
            $this->setAttribute('tests', intval($this->getAttribute('tests')) + 1);
        } else {
            $this->setAttribute('tests', 1);
        }
    }

    /**
     * Increments failure counter in test suite
     */
    public function incrementFailureCount()
    {
        if ($this->hasAttribute('failures')) {
            $this->setAttribute('failures', intval($this->getAttribute('failures')) + 1);
        } else {
            $this->setAttribute('failures', 1);
        }
    }

    /**
     * Increments error counter in test suite
     */
    public function incrementErrorCount()
    {
        if ($this->hasAttribute('errors')) {
            $this->setAttribute('errors', intval($this->getAttribute('errors')) + 1);
        } else {
            $this->setAttribute('errors', 1);
        }
    }
}