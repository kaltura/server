<?php

/**
 * upload service base test case.
 */
abstract class UploadServiceBaseTest extends KalturaApiTestCase
{
	/**
	 * Called when all tests are done
	 * @param int $id
	 * @return int
	 */
	abstract public function testFinished($id);

}
