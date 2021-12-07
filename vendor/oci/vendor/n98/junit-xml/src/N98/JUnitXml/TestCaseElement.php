<?php

namespace N98\JUnitXml;

class TestCaseElement extends \DOMElement
{
    public function __construct()
    {
        parent::__construct('testcase');
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->setAttribute('name', $name);
    }

    /**
     * @param string $classname
     */
    public function setClassname($classname)
    {
        $this->setAttribute('classname', $classname);
    }

    /**
     * Time in seconds
     *
     * @param float $time
     */
    public function setTime($time)
    {
        $this->setAttribute('time', $time);
    }

    /**
     * @param string $error
     * @param string $type  -> i.e. an exception class name
     * @param string $exceptionMessage i.e a exception message -> $e->getMessage() [Optional]
     * @return \DomElement
     */
    public function addError($error, $type, $exceptionMessage = null)
    {
        $errorElement = $this->ownerDocument->createElement('error', $error);
        $errorElement->setAttribute('type', $type);
        if ($exceptionMessage !== null) {
            $errorElement->setAttribute('message', $exceptionMessage);
        }
        $this->appendChild($errorElement);

        $this->incrementErrorCount();
        $this->parentNode->incrementErrorCount();

        return $errorElement;
    }

    /**
     * @param string $error
     * @param string $type  -> i.e. an exception class name
     * @param string $exceptionMessage i.e a exception message -> $e->getMessage() [Optional]
     * @return \DomElement
     */
    public function addFailure($error, $type, $exceptionMessage = null)
    {
        $failureElement = $this->ownerDocument->createElement('failure', $error);
        $failureElement->setAttribute('type', $type);
        if ($exceptionMessage !== null) {
            $failureElement->setAttribute('message', $exceptionMessage);
        }
        $this->appendChild($failureElement);

        $this->incrementFailureCount();
        $this->parentNode->incrementFailureCount();

        return $failureElement;
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