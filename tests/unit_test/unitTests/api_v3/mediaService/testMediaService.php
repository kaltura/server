<?php
	
chdir(dirname(__FILE__));
require_once("../../../bootstrap.php");
chdir(dirname(__FILE__));
	
class MediaServiceUnitTest extends unitTestBase
{
	/**
	 * 
	 * Tests the media upload action (returns the new token for that file)
	 * @param file $fileData
	 * @dataProvider providerTestUploadAction
	 */
	public function testUploadAction($fileData, $partnerId, $secret)
	{
		$config = new KalturaConfiguration((int)$partnerId);
//		$config->serviceUrl = 'http://www.kaltura.com';
		$client = new KalturaClient($config);
		$ks = $client->session->start($secret, null, KalturaSessionType::ADMIN, (int)$partnerId, null, null);
		$client->setKs($ks);
		
		$tokenId = $client->media->upload($fileData);
		
//		$mediaService = new MediaService();
//		$tokenId = $mediaService->uploadAction($fileData);
//		$mediaService->
		
		return $tokenId;
	}
	
	/**
	 * 
	 * Provides the data for the upload action test (testUploadAction)
	 * @return array<array<>> - the input array for the upload test
	 */
	public function providerTestUploadAction()
	{
		$inputsTestCase = parent::provider(dirname(__FILE__) . "/tests_data/testMediaServiceUploadAction.data");
		
		$inputs = array();
		
		foreach ($inputsTestCase as $inputTestCase)
		{
			foreach ($inputTestCase as $inputData)
			{
	// here we create a fileData object of the following:
//			  [name] => MyFile.txt (comes from the browser, so treat as tainted)
//            [type] => text/plain  (not sure where it gets this from - assume the browser, so treat as tainted)
//            [tmp_name] => /tmp/php/php1h4j1o (could be anywhere on your system, depending on your config settings, but the user has no control, so this isn't tainted)
//            [error] => UPLOAD_ERR_OK  (= 0)
//            [size] => 123   (the size in bytes)
				
				$inputPath = $inputData->additionalData["value"];		
				
				$inputs[] = array( 
								array(
									  "name" => $inputPath,
									  "type" => 'plain',
								  	  "tmp_name" => $inputPath,
            					  	  "error" => UPLOAD_ERR_OK, // = 0
								  	  "size" => filesize($inputPath)
									 ), $inputData->additionalData["partnerId"],
									 	$inputData->additionalData["secret"] 
							     );
			}
		}
		
		return $inputs;
	}
	
	/**
	 * 
	 * tests MediaService AddFromUploadedFileAction action
	 * @param MediaEntry $entry
	 * @param string $uploadTokenId
	 * 
	 * @dataProvider providerTestMediaServiceAddFromUploadedFileAction
	 * @depends testUploadAction
	 */
	public function testMediaServiceAddFromUploadedFileAction(KalturaBaseEntry $entry, $uploadTokenId)
	{
		$mediaService = new MediaService();
		$result = $mediaService->addFromUploadedFileAction($entry, $uploadTokenId);	
	}
	
	/**
	 * 
	 * Provides the data for the unit test above (using the data file)
	 */
	public function providerTestediaServiceAddFromUploadedFileAction()
	{
		$inputs = parent::provider(dirname(__FILE__) . "/tests_data/testMediaServiceAddFromUploadedFileAction.data");
		return $inputs;
	}
}
