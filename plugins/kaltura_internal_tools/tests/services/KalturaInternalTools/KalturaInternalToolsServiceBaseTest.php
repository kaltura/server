<?php

/**
 * KalturaInternalTools service base test case.
 */
abstract class KalturaInternalToolsServiceBaseTest extends KalturaApiUnitTestCase
{
	/**
	 * Called when all tests are done
	 * @param int $id
	 * @return int
	 */
	abstract public function testFinished($id);

}
