<?php

/**
 * filesyncImportBatch service base test case.
 */
abstract class FileSyncImportBatchServiceBaseTest extends KalturaApiUnitTestCase
{
	/**
	 * Called when all tests are done
	 * @param int $id
	 * @return int
	 */
	abstract public function testFinished($id);

}
