<?php

/**
 * virusScanBatch service base test case.
 */
abstract class VirusScanBatchServiceBaseTest extends KalturaApiUnitTestCase
{
	/**
	 * Called when all tests are done
	 * @param int $id
	 * @return int
	 */
	abstract public function testFinished($id);

}
