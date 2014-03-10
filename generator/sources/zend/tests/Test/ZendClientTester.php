<?php

class ZendClientTester
{
	const UPLOAD_VIDEO_FILENAME = 'DemoVideo.flv';
	const UPLOAD_IMAGE_FILENAME = 'DemoImage.jpg';
	const ENTRY_NAME = 'Media entry uploaded from Zend Framework client library';
	
	/**
	 * @var Kaltura_Client_Client
	 */
	protected $_client;
	
	public function __construct(Kaltura_Client_Client $client)
	{
		$this->_client = $client;
	}
	
	public function run()
	{
		$methods = get_class_methods($this);
		foreach($methods as $method)
		{
			if (strpos($method, 'test') === 0)
			{
				try 
				{
					// use the client logger interface to log
					$this->_client->getConfig()->getLogger()->log('Running '.$method);
					$this->$method();
				}
				catch(Exception $ex)
				{
					
					$this->_client->getConfig()->getLogger()->log($method . ' failed with error: ' . $ex->getMessage());
					return;
				}
			}
		}
		echo "\nFinished running client library tests\n";
	}
	
		public function testMultiRequest() {
		
		$this->_client->startMultiRequest();

		$mixEntry = new Kaltura_Client_Type_MixEntry();
		$mixEntry->name = ".Net Mix";
		$mixEntry->editorType = Kaltura_Client_Enum_EditorType::SIMPLE;

		# Request 1
		$mixEntry = $this->_client->mixing->add($mixEntry);

		# Request 2
		$uploadFilePath = dirname(__FILE__) . '/../resources/' . self::UPLOAD_VIDEO_FILENAME;
    	$uploadTokenId = $this->_client->media->upload($uploadFilePath);

		$mediaEntry = new Kaltura_Client_Type_MediaEntry();
		$mediaEntry->name = "Media Entry For Mix";
		$mediaEntry->mediaType = Kaltura_Client_Enum_MediaType::VIDEO;

		# Request 3
		$mediaEntry = $this->_client->media->addFromUploadedFile($mediaEntry, $uploadTokenId);

		# Request 4
		$this->_client->mixing->appendMediaEntry($mixEntry->id, $mediaEntry->id);

		$response = $this->_client->doMultiRequest();

		foreach( $response as $subResponse)
			if($subResponse instanceof Kaltura_Client_Exception) 
				throw new Exception("Error occurred: " . $subResponse->getMessage());

		# when accessing the response object we will use an index and not the response number (response number - 1)
		$this->assertTrue($response[0] instanceof Kaltura_Client_Type_MixEntry);
		$mixEntry = $response[0];
		
		if(is_null($mixEntry->id))
			throw new Exception("Failed to add entry within multi request");
	}
	
	public function testSyncFlow()
	{
		// add upload token
		$uploadToken = new Kaltura_Client_Type_UploadToken();
		$uploadToken->fileName = self::UPLOAD_VIDEO_FILENAME;
		$uploadToken = $this->_client->uploadToken->add($uploadToken);
		$this->assertTrue(strlen($uploadToken->id) > 0);
    	$this->assertEqual($uploadToken->fileName, self::UPLOAD_VIDEO_FILENAME);
    	$this->assertEqual($uploadToken->status, Kaltura_Client_Enum_UploadTokenStatus::PENDING);
    	$this->assertEqual($uploadToken->partnerId, $this->_client->getConfig()->partnerId);
    	$this->assertEqual($uploadToken->fileSize, null);
    	
    	// add media entry
    	$entry = new Kaltura_Client_Type_MediaEntry();
    	$entry->name = self::ENTRY_NAME;
    	$entry->mediaType = Kaltura_Client_Enum_MediaType::VIDEO;
    	$entry = $this->_client->media->add($entry);
    	$this->assertTrue(strlen($entry->id) > 0);
    	$this->assertTrue($entry->status === Kaltura_Client_Enum_EntryStatus::NO_CONTENT);
    	$this->assertTrue($entry->name === self::ENTRY_NAME);
    	$this->assertTrue($entry->partnerId === $this->_client->getConfig()->partnerId);
    	
    	// add uploaded token as resource
    	$resource = new Kaltura_Client_Type_UploadedFileTokenResource();
    	$resource->token = $uploadToken->id;
    	$entry = $this->_client->media->addContent($entry->id, $resource);
    	$this->assertTrue($entry->status === Kaltura_Client_Enum_EntryStatus::IMPORT);
    	
    	// upload file using the upload token
    	$uploadFilePath = dirname(__FILE__) . '/../resources/' . self::UPLOAD_VIDEO_FILENAME;
    	$uploadToken = $this->_client->uploadToken->upload($uploadToken->id, $uploadFilePath);
    	$this->assertTrue($uploadToken->status === Kaltura_Client_Enum_UploadTokenStatus::CLOSED);
    	
    	// get flavor by entry
    	$flavorArray = $this->_client->flavorAsset->getByEntryId($entry->id);
    	$this->assertTrue(count($flavorArray) > 0);
    	$foundSource = false;
    	foreach($flavorArray as $flavor)
    	{
    		if ($flavor->flavorParamsId !== 0)
    			continue;
    			
    		$this->assertTrue($flavor->isOriginal);
    		$this->assertTrue($flavor->entryId === $entry->id);
    		$foundSource = true;
    	}
    	$this->assertTrue($foundSource);
    	
    	// count media entries
    	$mediaFilter = new Kaltura_Client_Type_MediaEntryFilter();
    	$mediaFilter->idEqual = $entry->id;
    	$mediaFilter->statusNotEqual = Kaltura_Client_Enum_EntryStatus::DELETED;
    	$entryCount = $this->_client->media->count($mediaFilter);
    	$this->assertTrue($entryCount == 1);
    	
    	// delete media entry
    	$this->_client->media->delete($entry->id);
    	
    	sleep(5); // wait for the status to update
    	
    	// count media entries again
		$entryCount = $this->_client->media->count($mediaFilter);
    	$this->assertTrue($entryCount == 0);
	}
	
	public function testReturnedArrayObjectUsingPlaylistExecute()
	{
		// add image entry
    	$imageEntry = $this->addImageEntry();
    	
    	// execute playlist from filters
    	$playlistFilter = new Kaltura_Client_Type_MediaEntryFilterForPlaylist();
    	$playlistFilter->idEqual = $imageEntry->id;
    	$filterArray = array();
    	$filterArray[] = $playlistFilter;
    	$playlistExecute = $this->_client->playlist->executeFromFilters($filterArray, 10);
    	$this->assertEqual(count($playlistExecute), 1);
    	$firstPlaylistEntry = $playlistExecute[0];
    	$this->assertEqual($firstPlaylistEntry->id, $imageEntry->id);
    	
    	$this->_client->media->delete($imageEntry->id);
	}
	
	public function testServeUrl()
	{
		$serveUrl = $this->_client->data->serve("12345", 5, true);
		$expectedArray = array(
			'service' => 'data',
			'action' => 'serve',
			'apiVersion' => $this->_client->getApiVersion(),
			'format' => 2,
			'clientTag' => $this->_client->getConfig()->clientTag,
			'entryId' => '12345',
			'version' => 5,
			'forceProxy' => 1,
			'partnerId' => $this->_client->getConfig()->partnerId,
			'ks' => $this->_client->getKs());
		$expected = http_build_query($expectedArray);
		
	    	echo($serveUrl.PHP_EOL);
	    	echo($expected.PHP_EOL);
    	$this->assertTrue(strpos($serveUrl, $expected) !== false);
	}
	
	public function addImageEntry()
	{
		$entry = new Kaltura_Client_Type_MediaEntry();
    	$entry->name = self::ENTRY_NAME;
    	$entry->mediaType = Kaltura_Client_Enum_MediaType::IMAGE;
    	$entry = $this->_client->media->add($entry);
    	
    	$uploadToken = new Kaltura_Client_Type_UploadToken();
		$uploadToken->fileName = self::UPLOAD_IMAGE_FILENAME;
		$uploadToken = $this->_client->uploadToken->add($uploadToken);

    	$uploadFilePath = dirname(__FILE__) . '/../resources/' . self::UPLOAD_IMAGE_FILENAME;
    	$uploadToken = $this->_client->uploadToken->upload($uploadToken->id, $uploadFilePath);
    	
		$resource = new Kaltura_Client_Type_UploadedFileTokenResource();
    	$resource->token = $uploadToken->id;
    	$entry = $this->_client->media->addContent($entry->id, $resource);
    	
    	return $entry;
	}
	
	protected function assertTrue($v)
	{
		if ($v !== true)
		{
			$backtrace = debug_backtrace();
			$msg = 'Assert failed on line: ' . $backtrace[0]['line'];
			throw new Exception($msg);
		}
	}

	protected function assertEqual($actual, $expected)
	{
		if ($actual !== $expected)
		{
			$backtrace = debug_backtrace();
			$msg = sprintf(
				"Assert failed on line: {$backtrace[0]['line']}, expecting [%s] of type [%s], actual is [%s] of type [%s]",
				$expected,
				gettype($expected),
				$actual,
				gettype($actual));
			throw new Exception($msg);
		}
	}
}