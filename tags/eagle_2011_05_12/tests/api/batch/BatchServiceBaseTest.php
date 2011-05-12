<?php

/**
 * batch service base test case.
 */
abstract class BatchServiceBaseTest extends KalturaApiUnitTestCase
{
	/**
	 * Called when all tests are done
	 * @param int $id
	 * @return int
	 */
	abstract public function testFinished($id);

}
