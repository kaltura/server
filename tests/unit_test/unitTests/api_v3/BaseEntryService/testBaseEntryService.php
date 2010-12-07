<?php
	
chdir(dirname(__FILE__));
require_once("../../../bootstrap.php");
chdir(dirname(__FILE__));
	
class BaseEntryServiceUnitTest extends unitTestBase
{

	/**
	 * 
	 * tests BaseEntryService AddFromUploadedFileAction action
	 * @param KalturaBaseEntry $entry
	 * @param string $uploadTokenId
	 * @param KalturaEntryType $type
	 * 
	 * @dataProvider providerTestBaseEntryServiceAddFromUploadedFileAction
	 */
	public function testBaseEntryServiceAddFromUploadedFileAction($entry, $uploadTokenId, $type)
	{
		$entryService = new BaseEntryService();
		$result = $entryService->addFromUploadedFileAction($entry, $uploadTokenId, $type);	
	}
	
	/**
	 * 
	 * Provides the data for the unit test above (using the data file)
	 */
	public function providerTestBaseEntryServiceAddFromUploadedFileAction()
	{
		$inputs = parent::provider(dirname(__FILE__) . "/tests_data/testBaseEntryServiceAddFromUploadedFileAction.data");
		return $inputs;
	}
}
