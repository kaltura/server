<?php

require_once(dirname(__FILE__) . "/../../../bootstrap/bootstrapClient.php");

class MediaServiceUnitTest extends KalturaApiUnitTestCase
{
	/**
	 * 
	 * Tests the media upload action (returns the new token for that file)
	 *
	 */
	public function testUploadAction()
	{
		$testCaseInputs = parent::provider(dirname(__FILE__) . "/testsData/testMediaServiceUploadAction.data");
		
		$dataForSecondTest = array();
		
		foreach ($testCaseInputs as $testCaseInput)
		{
			foreach ($testCaseInput as $inputData)
			{
				$filePath = $inputData->additionalData["value"];		
				$partnerId = $inputData->additionalData["partnerId"];
				$secret = $inputData->additionalData["secret"];

				$client = parent::getClient($partnerId, $secret, 'www.kaltura.com');
				$tokenId = $client->media->upload(realpath($filePath));
     	     				
				//Add the new token for this file upload
				$dataForSecondTest[] = array("tokenId" => $tokenId, "client" => $client);
			}
		}

		return $dataForSecondTest;
	}
	
	/**
	 * 
	 * tests MediaService AddFromUploadedFileAction action
	 * @param array<> $dataForSecondTest
	 * 
	 * @depends testUploadAction
	 */
	public function testMediaServiceAddFromUploadedFileAction(array $dataForSecondTest)
	{
		foreach ($dataForSecondTest as $singleTestCase)
		{
			//Gets the client and token from the dependent method
			$client = $singleTestCase["client"];
			$uploadTokenId = $singleTestCase["tokenId"];
			$mediaEntry = new KalturaMediaEntry();
			
			$mediaEntry->name = "UploadUnitTest"; // a most have properties
			$mediaEntry->mediaType = KalturaMediaType::VIDEO;
			
			//Try to upload the file from the token
			try {
				$result = $client->media->addFromUploadedFile($mediaEntry, $uploadTokenId);
			}
			catch(Exception $e)
			{
				$result = null;
			}

			//If the action succeeded
			$this->assertNotNull($result, "upload failed...");
			
			if($result instanceof KalturaMediaEntry)
			{
				//Checks if the result has an id;
				$this->assertNotNull($result->id, "the result id is null");
			}
		}
	}

	/**
	 * 
	 * the data provider for the get action test
	 * @return array<array<>>
	 */
	public function providerTestMediaServiceGetAction()
	{
		$inputs = parent::provider(dirname(__FILE__) . "/testsData/testMediaServiceGetAction.Data");
		$formattedInput = array();
		foreach ($inputs as $testCaseInputs)
		{
			foreach ($testCaseInputs as $testCaseInput)
			{
				$partnerId = $testCaseInput->additionalData["partnerId"];
				$secret = $testCaseInput->additionalData["secret"];
				$formattedInput[] = array($testCaseInput->dataObject, $partnerId, $secret, "www.kaltura.com");
			}
		}
		
		return $formattedInput;
	}
	
	/**
	 * 
	 * Tests the get action for the media service
	 * @param KalturaMediaEntry $mediaEntry
	 * @param string $partnerId
	 * @param string $secret
	 * @param string $serverPath
	 * 
	 * @dataProvider providerTestMediaServiceGetAction
	 */
	public function testMediaServiceGetAction(KalturaMediaEntry $mediaEntry, $partnerId, $secret, $configServiceUrl)
	{
		$client = $this->getClient($partnerId, $secret, $configServiceUrl);
		$result = $client->media->get($mediaEntry->id);
		$mediaEntry->updatedAt = $result->updatedAt;
		$this->assertEquals($mediaEntry, $result);
	}
}