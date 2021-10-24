<?php

require_once __DIR__ . '/../vendor/autoload.php';

use \N98\JUnitXml\Document;

class TestUnitTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Document
     */
    protected $document;

    protected function setUp(): void
    {
        $this->document = new Document();
    }

    protected function tearDown(): void
    {
        unset($this->document);
    }

    /**
     * @test
     */
    public function addTestSuite()
    {
        $suite = $this->document->addTestSuite();
        $timeStamp = new \DateTime();
        $suite->setName('My Test Suite');
        $suite->setTimestamp($timeStamp);
        $suite->setTime(0.344244);

        $testCase = $suite->addTestCase();

        $xmlString = $this->document->saveXML();
        $xml = simplexml_load_string($xmlString);

        // There are currently no errors and failures
        $this->assertEquals('0', $xml->testsuite[0]->testcase[0]['errors']);
        $this->assertEquals('0', $xml->testsuite[0]->testcase[0]['failures']);

        $testCase->addError('My error 1', 'Exception');
        $testCase->addError('My error 2', 'Exception');
        $testCase->addError('My error 3', 'Exception');
        $testCase->addError('My error 4', 'Exception');
        $testCase->addFailure('My failure 1', 'Exception');
        $testCase->addFailure('My failure 2', 'Exception');

        $xmlString = $this->document->saveXML();
        $xml = simplexml_load_string($xmlString);

        $this->assertEquals($timeStamp->format(\DateTime::ISO8601), $xml->testsuite[0]['timestamp']);
        $this->assertEquals('0.344244', $xml->testsuite[0]['time']);
        $this->assertEquals('4', $xml->testsuite[0]->testcase[0]['errors']);
        $this->assertEquals('4', $xml->testsuite[0]['errors']);
        $this->assertEquals('2', $xml->testsuite[0]->testcase[0]['failures']);
        $this->assertEquals('2', $xml->testsuite[0]['failures']);
        $this->assertEquals('My error 2', (string) $xml->testsuite[0]->testcase[0]->error[1]);

        // Add another test case and see if testsuite error counter will be increased
        $testCase2 = $suite->addTestCase();
        $testCase2->addError('My error 1.1', 'Exception');
        $testCase2->addFailure('My failure 1.1', 'Exception');

        $xmlString = $this->document->saveXML();
        $xml = simplexml_load_string($xmlString);

        $this->assertEquals('1', $xml->testsuite[0]->testcase[1]['errors']);
        $this->assertEquals('5', $xml->testsuite[0]['errors']);
        $this->assertEquals('1', $xml->testsuite[0]->testcase[1]['failures']);
        $this->assertEquals('3', $xml->testsuite[0]['failures']);
    }
}