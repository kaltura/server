<?php

require_once 'tests/unit_test/bootstrap.php';

/**
 * Static test suite.
 */
class unitTestsSuite extends PHPUnit_Framework_TestSuite {
	
	/**
	 * Constructs the test suite handler.
	 */
	public function __construct() {
		$this->setName ( 'KalturaUnitTestsSuite' );
	
	}
	
	/**
	 * Creates the suite.
	 */
	public static function suite() {
		return new self ();
	}
}

