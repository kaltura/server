<?php

require_once(dirname(__FILE__) . '/../../bootstrap.php');

/**
 * accessControlProfile service test case.
 */
class AccessControlProfileServiceTest extends AccessControlProfileServiceTestBase
{
	/* (non-PHPdoc)
	 * @see AccessControlProfileServiceTestBase::validateAdd()
	 */
	protected function validateAdd(KalturaAccessControlProfile $resultObject)
	{
		$this->assertNotNull($resultObject->id);
		$this->assertNotNull($resultObject->partnerId);
		$this->assertNotNull($resultObject->createdAt);
		$this->assertNotNull($resultObject->updatedAt);
		$this->assertNotNull($resultObject->isDefault);
	}
	
	/* (non-PHPdoc)
	 * @see AccessControlProfileServiceTestBase::validateUpdate()
	 */
	protected function validateUpdate(KalturaAccessControlProfile $resultObject)
	{
		$this->assertNotNull($resultObject->id);
		$this->assertNotNull($resultObject->partnerId);
		$this->assertNotNull($resultObject->createdAt);
		$this->assertNotNull($resultObject->updatedAt);
		$this->assertNotNull($resultObject->isDefault);
		
		$this->assertNotEquals($resultObject->createdAt, $resultObject->updatedAt);
	}

	/**
	 * Tests baseEntry->getContextData
	 * @param KalturaAccessControlProfile $accessControlProfile
	 * @param KalturaEntryContextDataParams $contextDataParams
	 * @param KalturaEntryContextDataResult $contextDataResultReference
	 * @dataProvider provideData
	 */
	public function testGetContext(KalturaAccessControlProfile $accessControlProfile, KalturaEntryContextDataParams $contextDataParams, KalturaEntryContextDataResult $contextDataResultReference)
	{
		$addedAccessControlProfile = $this->client->accessControlProfile->add($accessControlProfile);
		/* @var $addedAccessControlProfile KalturaAccessControlProfile */
		if(method_exists($this, 'assertInstanceOf'))
			$this->assertInstanceOf('KalturaAccessControlProfile', $addedAccessControlProfile);
		else
			$this->assertType('KalturaAccessControlProfile', $addedAccessControlProfile);
		$this->assertNotNull($addedAccessControlProfile->id);
		
		$entry = new KalturaMediaEntry();
		$entry->mediaType = KalturaMediaType::VIDEO;
		$entry->name = 'Access Control Test';
		$entry->accessControlId = $addedAccessControlProfile->id;
		$addedEntry = $this->client->media->add($entry);
		/* @var $addedEntry KalturaMediaEntry */
		if(method_exists($this, 'assertInstanceOf'))
			$this->assertInstanceOf('KalturaMediaEntry', $addedEntry);
		else
			$this->assertType('KalturaMediaEntry', $addedEntry);
		$this->assertNotNull($addedEntry->id);
		
		$testConfig = $this->config->get('config');
		$contextDataParams->ks = $this->client->generateSession($testConfig->secret, '', KalturaSessionType::USER, $testConfig->partnerId);
		$contextDataResult = $this->client->baseEntry->getContextData($addedEntry->id, $contextDataParams);
		/* @var $contextDataResult KalturaEntryContextDataResult */
		$this->assertAPIObjects($contextDataResultReference, $contextDataResult);
	}

	
	/**
	 * @param int $accessControlProfileId
	 * @param string $mediaFilePath
	 * @return KalturaMediaEntry
	 */
	protected function createReadyMediaEntry($accessControlProfileId, $mediaFilePath)
	{
		// creating conversion profile
		$conversionProfile = new KalturaConversionProfile();
		$conversionProfile->name = $this->getName();
		$conversionProfile->flavorParamsIds = 0;
		$addedConversionProfile = $this->client->conversionProfile->add($conversionProfile);
		/* @var $addedConversionProfile KalturaConversionProfile */
		if(method_exists($this, 'assertInstanceOf'))
			$this->assertInstanceOf('KalturaConversionProfile', $addedConversionProfile);
		else
			$this->assertType('KalturaConversionProfile', $addedConversionProfile);
		$this->assertNotNull($addedConversionProfile->id);
		
		// creating entry
		$entry = new KalturaMediaEntry();
		$entry->mediaType = KalturaMediaType::VIDEO;
		$entry->name = $this->getName();
		$entry->accessControlId = $accessControlProfileId;
		$entry->conversionProfileId = $addedConversionProfile->id;
		$addedEntry = $this->client->media->add($entry);
		/* @var $addedEntry KalturaMediaEntry */
		if(method_exists($this, 'assertInstanceOf'))
			$this->assertInstanceOf('KalturaMediaEntry', $addedEntry);
		else
			$this->assertType('KalturaMediaEntry', $addedEntry);
		$this->assertNotNull($addedEntry->id);
		$this->assertEquals($addedEntry->status, KalturaEntryStatus::NO_CONTENT);
		
		// creating upload token
		$uploadToken = new KalturaUploadToken();
		$uploadToken->fileName = basename($mediaFilePath);
		$uploadToken->fileSize = filesize($mediaFilePath);
		$addedUploadToken = $this->client->uploadToken->add($uploadToken);
		/* @var $addedUploadToken KalturaUploadToken */
		if(method_exists($this, 'assertInstanceOf'))
			$this->assertInstanceOf('KalturaUploadToken', $addedUploadToken);
		else
			$this->assertType('KalturaUploadToken', $addedUploadToken);
		$this->assertNotNull($addedUploadToken->id);
		$this->assertEquals($addedUploadToken->status, KalturaUploadTokenStatus::PENDING);
		
		// uploading the media
		$uploadedUploadToken = $this->client->uploadToken->upload($addedUploadToken->id, $mediaFilePath);
		/* @var $uploadedUploadToken KalturaUploadToken */
		if(method_exists($this, 'assertInstanceOf'))
			$this->assertInstanceOf('KalturaUploadToken', $uploadedUploadToken);
		else
			$this->assertType('KalturaUploadToken', $uploadedUploadToken);
		$this->assertEquals($uploadedUploadToken->status, KalturaUploadTokenStatus::FULL_UPLOAD);
		
		// ingesting the uploaded file to entry
		$resource = new KalturaUploadedFileTokenResource();
		$resource->token = $uploadedUploadToken->id;
		$ingestedEntry = $this->client->media->addContent($addedEntry->id, $resource);
		/* @var $ingestedEntry KalturaMediaEntry */
		if(method_exists($this, 'assertInstanceOf'))
			$this->assertInstanceOf('KalturaMediaEntry', $ingestedEntry);
		else
			$this->assertType('KalturaMediaEntry', $ingestedEntry);
		$this->assertNotNull($ingestedEntry->id);
		
		$closedStatuses = array(
			KalturaEntryStatus::READY,
			KalturaEntryStatus::ERROR_IMPORTING,
			KalturaEntryStatus::ERROR_CONVERTING,
			KalturaEntryStatus::DELETED,
			KalturaEntryStatus::INFECTED,
			KalturaEntryStatus::SCAN_FAILURE,
		);
		
		while(!in_array(intval($ingestedEntry->status), $closedStatuses) )
		{
			KalturaLog::debug("Entry [$ingestedEntry->id] status [$ingestedEntry->status] sleeping...");
			sleep(30);
			$ingestedEntry = $this->client->media->get($addedEntry->id);
		}
			
		$this->assertEquals($ingestedEntry->status, KalturaEntryStatus::READY);
		
		return $ingestedEntry;
	}
	
	/**
	 * Tests getFeed
	 * @param KalturaAccessControlProfile $accessControlProfile
	 * @param string $mediaFilePath
	 * @param bool $success
	 * @dataProvider provideData
	 */
	public function testFeed(KalturaAccessControlProfile $accessControlProfile, $mediaFilePath, $success)
	{
		throw new PHPUnit_Framework_SkippedTestError('No need to test feeds, it is currently not affected by access control');
		
		// creating access control profile
		$addedAccessControl = $this->client->accessControlProfile->add($accessControlProfile);
		/* @var $addedAccessControl KalturaAccessControl */
		if(method_exists($this, 'assertInstanceOf'))
			$this->assertInstanceOf('KalturaAccessControlProfile', $addedAccessControl);
		else
			$this->assertType('KalturaAccessControlProfile', $addedAccessControl);
		$this->assertNotNull($addedAccessControl->id);
		
		$addedEntry = $this->createReadyMediaEntry($addedAccessControl->id, $mediaFilePath);
		
		// creating static play list
		$playlist = new KalturaPlaylist();
		$playlist->totalResults = 10;
		$playlist->playlistType = KalturaPlaylistType::STATIC_LIST;
		$playlist->playlistContent = $addedEntry->id;
		$addedPlaylist = $this->client->playlist->add($playlist);
		/* @var $addedPlaylist KalturaPlaylist */
		if(method_exists($this, 'assertInstanceOf'))
			$this->assertInstanceOf('KalturaPlaylist', $addedPlaylist);
		else
			$this->assertType('KalturaPlaylist', $addedPlaylist);
		$this->assertNotNull($addedPlaylist->id);
		
		// creating generic feed
		$feed = new KalturaGenericSyndicationFeed();
		$feed->playlistId = $addedPlaylist->id;
		$addedFeed = $this->client->syndicationFeed->add($feed);
		/* @var $addedFeed KalturaGenericSyndicationFeed */
		if(method_exists($this, 'assertInstanceOf'))
			$this->assertInstanceOf('KalturaGenericSyndicationFeed', $addedFeed);
		else
			$this->assertType('KalturaGenericSyndicationFeed', $addedFeed);
		$this->assertNotNull($addedFeed->id);
		$this->assertNotNull($addedFeed->feedUrl);
		
		$xml = new DOMDocument();
		$xml->load($addedFeed->feedUrl);
		KalturaLog::debug($xml->saveXML());
		
		$xPath = new DOMXPath($xml);
		$elementsList = $xPath->query("//channel/items/item/entryId[string() = '{$addedEntry->id}']");
		$this->assertEquals($success ? 1 : 0, $elementsList->length);
	}
	
	/**
	 * @param string $url
	 * @param string $localFilePath
	 * @param string $headers
	 * @return int http error code
	 */
	protected function cUrl($url, $localFilePath, &$headers, $followLocation = true)
	{
		KalturaLog::debug("Downloading [$url]");
		$headerFilePath = "$localFilePath.header";
		$verboseFilePath = "$localFilePath.log";
		
		$ch = curl_init();

		$chFile = fopen($localFilePath, 'w');
		$chWriteHeader = fopen($headerFilePath, 'w');
		$chStdErr = fopen($verboseFilePath, 'w');
		
		curl_setopt($ch, CURLOPT_URL, $url);
		//curl_setopt($ch, CURLOPT_HEADER, true);
		curl_setopt($ch, CURLOPT_FILE, $chFile);
		curl_setopt($ch, CURLOPT_WRITEHEADER, $chWriteHeader);
		curl_setopt($ch, CURLOPT_STDERR, $chStdErr);
		curl_setopt($ch, CURLOPT_VERBOSE, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, $followLocation);
		
		$ret = curl_exec($ch);
		curl_close($ch);
		
		fclose($chFile);
		fclose($chWriteHeader);
		fclose($chStdErr);
		
		KalturaLog::debug(file_get_contents($verboseFilePath));
		
		$errCode = null;
		$headers = array();
		$headerLines = file($headerFilePath);
		foreach($headerLines as $header)
		{
			if(preg_match('/HTTP\/?[\d.]{0,3} ([\d]{3}) ([^\n\r]+)/', $header, $matches))
			{
				$errCode = $matches[1];
				continue;
			}
			
			$parts = explode(':', $header, 2);
			if(count($parts) != 2)
				continue;
				
			list($name, $value) = $parts;
			$headers[trim(strtolower($name))] = trim($value);
		}
		
		return $errCode;
	}
	
	/**
	 * Tests PS2 thumbnail action
	 * @param KalturaAccessControlProfile $accessControlProfile
	 * @param string $mediaFilePath
	 * @param string $referrer
	 * @param bool $success
	 * @dataProvider provideData
	 */
	public function testThumbnail(KalturaAccessControlProfile $accessControlProfile, $mediaFilePath, $referrer, $success)
	{
//		throw new PHPUnit_Framework_SkippedTestError('Already works');
	
		// creating access control profile
		$addedAccessControl = $this->client->accessControlProfile->add($accessControlProfile);
		/* @var $addedAccessControl KalturaAccessControl */
		if(method_exists($this, 'assertInstanceOf'))
			$this->assertInstanceOf('KalturaAccessControlProfile', $addedAccessControl);
		else
			$this->assertType('KalturaAccessControlProfile', $addedAccessControl);
		$this->assertNotNull($addedAccessControl->id);
		
		$addedEntry = $this->createReadyMediaEntry($addedAccessControl->id, $mediaFilePath);
		KalturaLog::debug(print_r($addedEntry, true));
		
		$thumbLocalPath = dirname(__FILE__) . DIRECTORY_SEPARATOR . $this->getName() . '.jpg';

		$headers = null;
		$errCode = $this->cUrl($addedEntry->thumbnailUrl, $thumbLocalPath, $headers);
		
		$this->assertEquals(200, $errCode, "Thumbnail HTTP request should success. ");
		if($success)
		{
			$this->assertGreaterThan(0, filesize($thumbLocalPath), "File [$thumbLocalPath] is empty. ");	
			$this->assertArrayNotHasKey('x-kaltura', $headers, "Should not raise Kaltura error. ");
		}
		else 
		{
			$this->assertEquals(0, filesize($thumbLocalPath), "Thumbnail should return empty file. ");
			$this->assertArrayHasKey('x-kaltura', $headers, "Should raise Kaltura error. ");
			$this->assertEquals('error-3', $headers['x-kaltura'], "Should raise Kaltura access control restriction error 3. ");
		}
		
		if(file_exists($thumbLocalPath))
			unlink($thumbLocalPath);
	}
	
	/**
	 * Tests PS2 playManifest action
	 * @param KalturaAccessControlProfile $accessControlProfile
	 * @param string $mediaFilePath
	 * @param string $referrer
	 * @param bool $success
	 * @dataProvider provideData
	 */
	public function testPlayManifest(KalturaAccessControlProfile $accessControlProfile, $mediaFilePath, $referrer, $success)
	{
//		throw new PHPUnit_Framework_SkippedTestError('Already works');
	
		// creating access control profile
		$addedAccessControl = $this->client->accessControlProfile->add($accessControlProfile);
		/* @var $addedAccessControl KalturaAccessControl */
		if(method_exists($this, 'assertInstanceOf'))
			$this->assertInstanceOf('KalturaAccessControlProfile', $addedAccessControl);
		else
			$this->assertType('KalturaAccessControlProfile', $addedAccessControl);
		$this->assertNotNull($addedAccessControl->id);
		
		$addedEntry = $this->createReadyMediaEntry($addedAccessControl->id, $mediaFilePath);
		
		$testConfig = $this->config->get('config');
		$playManifestUrl = "http://{$testConfig->serviceUrl}/p/{$addedEntry->partnerId}/sp/{$addedEntry->partnerId}00/playManifest/entryId/{$addedEntry->id}/a/a.f4m";
		$playManifestPath = dirname(__FILE__) . DIRECTORY_SEPARATOR . $this->getName() . '.manifest.xml';
		
		$headers = null;
		$errCode = $this->cUrl($playManifestUrl, $playManifestPath, $headers);
		
		$this->assertEquals(200, $errCode, "CURL should success, ");
	
		if($success)
		{
			$this->assertGreaterThan(0, filesize($playManifestPath), "File [$playManifestPath] is empty");	
			$this->assertArrayNotHasKey('x-kaltura', $headers, "Should not raise Kaltura error, ");
			
			$xml = new DOMDocument();
			$xml->load($playManifestPath);
			KalturaLog::debug($xml->saveXML());
			
			$context = $xml->documentElement;
			
			$xPath = new DOMXPath($xml);
			$xPath->registerNamespace('f4m', $context->namespaceURI); 
			$elementsList = $xPath->query("//f4m:manifest/f4m:id[string() = '{$addedEntry->id}']");
			$this->assertEquals(1, $elementsList->length, 'Manifest node not found');
		}
		else 
		{
			$this->assertEquals(0, filesize($playManifestPath), "play manifest should be empty. ");
			$this->assertArrayHasKey('x-kaltura', $headers, "Should raise Kaltura error. ");
			$this->assertEquals('error-3', $headers['x-kaltura'], "Should raise Kaltura access control restriction error 3. ");
		}
		
		if(file_exists($playManifestPath))
			unlink($playManifestPath);
	}
	
	/**
	 * Tests PS2 raw action
	 * @param KalturaAccessControlProfile $accessControlProfile
	 * @param string $mediaFilePath
	 * @param string $referrer
	 * @param bool $success
	 * @dataProvider provideData
	 */
	public function testRaw(KalturaAccessControlProfile $accessControlProfile, $mediaFilePath, $referrer, $success)
	{
//		throw new PHPUnit_Framework_SkippedTestError('Already works');
	
		// creating access control profile
		$addedAccessControl = $this->client->accessControlProfile->add($accessControlProfile);
		/* @var $addedAccessControl KalturaAccessControl */
		if(method_exists($this, 'assertInstanceOf'))
			$this->assertInstanceOf('KalturaAccessControlProfile', $addedAccessControl);
		else
			$this->assertType('KalturaAccessControlProfile', $addedAccessControl);
		$this->assertNotNull($addedAccessControl->id);
		
		$addedEntry = $this->createReadyMediaEntry($addedAccessControl->id, $mediaFilePath);
		KalturaLog::debug(print_r($addedEntry, true));
		
		$mediaLocalPath = dirname(__FILE__) . DIRECTORY_SEPARATOR . $this->getName() . '.$mediaLocalPath';
		
		$headers = null;
		$errCode = $this->cUrl($addedEntry->downloadUrl, $mediaLocalPath, $headers);
	
		$this->assertEquals(200, $errCode, "Raw action HTTP request should success. ");
		if($success)
		{
			$this->assertGreaterThan(0, filesize($mediaLocalPath), "File [$mediaLocalPath] is empty. ");	
			$this->assertArrayNotHasKey('x-kaltura', $headers, "Should not raise Kaltura error. ");
		}
		else 
		{
			$this->assertEquals(0, filesize($mediaLocalPath), "Raw action should return empty file. ");
			$this->assertArrayHasKey('x-kaltura', $headers, "Should raise Kaltura error. ");
			$this->assertEquals('error-3', $headers['x-kaltura'], "Should raise Kaltura access control restriction error 3. ");
		}
		
		if(file_exists($mediaLocalPath))
			unlink($mediaLocalPath);
	}
	
	/**
	 * Tests PS2 download action
	 * @param KalturaAccessControlProfile $accessControlProfile
	 * @param string $mediaFilePath
	 * @param string $referrer
	 * @param bool $success
	 * @dataProvider provideData
	 */
	public function testDownload(KalturaAccessControlProfile $accessControlProfile, $mediaFilePath, $referrer, $success)
	{
//		throw new PHPUnit_Framework_SkippedTestError('Already works');
		
		// creating access control profile
		$addedAccessControl = $this->client->accessControlProfile->add($accessControlProfile);
		/* @var $addedAccessControl KalturaAccessControl */
		if(method_exists($this, 'assertInstanceOf'))
			$this->assertInstanceOf('KalturaAccessControlProfile', $addedAccessControl);
		else
			$this->assertType('KalturaAccessControlProfile', $addedAccessControl);
		$this->assertNotNull($addedAccessControl->id);
		
		$addedEntry = $this->createReadyMediaEntry($addedAccessControl->id, $mediaFilePath);
		KalturaLog::debug(print_r($addedEntry, true));
		
		$testConfig = $this->config->get('config');
		$downloadUrl = "http://{$testConfig->serviceUrl}/p/{$addedEntry->partnerId}/sp/{$addedEntry->partnerId}00/download/entry_id/{$addedEntry->id}/referrer/$referrer";
		$mediaLocalPath = dirname(__FILE__) . DIRECTORY_SEPARATOR . $this->getName() . '.$mediaLocalPath';
		
		$headers = null;
		$errCode = $this->cUrl($downloadUrl, $mediaLocalPath, $headers, false);
	
		if($success)
		{
			$this->assertEquals(302, $errCode, "Download should success. ");
		
			$this->assertEquals(0, filesize($mediaLocalPath), "File [$mediaLocalPath] should be relocated");	
			$this->assertArrayNotHasKey('x-kaltura', $headers, "Should not raise Kaltura error, ");
		}
		else 
		{
			$this->assertEquals(200, $errCode, "Download should fail. ");
		
			$this->assertEquals(0, filesize($mediaLocalPath), "CURL should be empty, ");
			$this->assertArrayHasKey('x-kaltura', $headers, "Should raise Kaltura error, ");
			$this->assertEquals('error-3', $headers['x-kaltura'], "Should raise Kaltura access control restriction error 3, ");
		}
		
		if(file_exists($mediaLocalPath))
			unlink($mediaLocalPath);
	}
	
	/**
	 * Tests PS2 flvClipper action
	 * @param KalturaAccessControlProfile $accessControlProfile
	 * @param string $mediaFilePath
	 * @param string $referrer
	 * @param bool $success
	 * @dataProvider provideData
	 */
	public function testFlvClipper(KalturaAccessControlProfile $accessControlProfile, $mediaFilePath, $referrer, $success)
	{
//		throw new PHPUnit_Framework_SkippedTestError('Already works');
	
		// creating access control profile
		$addedAccessControl = $this->client->accessControlProfile->add($accessControlProfile);
		/* @var $addedAccessControl KalturaAccessControl */
		if(method_exists($this, 'assertInstanceOf'))
			$this->assertInstanceOf('KalturaAccessControlProfile', $addedAccessControl);
		else
			$this->assertType('KalturaAccessControlProfile', $addedAccessControl);
		$this->assertNotNull($addedAccessControl->id);
		
		$addedEntry = $this->createReadyMediaEntry($addedAccessControl->id, $mediaFilePath);
		
		$mediaLocalPath = dirname(__FILE__) . DIRECTORY_SEPARATOR . $this->getName() . '.flv';
		if(file_exists($mediaLocalPath))
			unlink($mediaLocalPath);
		
		$testConfig = $this->config->get('config');
		$flvClipperUrl = "http://{$testConfig->serviceUrl}/p/{$addedEntry->partnerId}/sp/{$addedEntry->partnerId}00/flvclipper/entry_id/{$addedEntry->id}/referrer/$referrer";
	
		$headers = null;
		$errCode = $this->cUrl($flvClipperUrl, $mediaLocalPath, $headers, false);
	
		if($success)
		{
			$this->assertEquals(302, $errCode, "FLV clipper should success. ");
		
			$this->assertEquals(0, filesize($mediaLocalPath), "File [$mediaLocalPath] should be relocated");	
			$this->assertArrayNotHasKey('x-kaltura', $headers, "Should not raise Kaltura error, ");
		}
		else 
		{
			$this->assertEquals(200, $errCode, "FLV clipper should fail. ");
		
			$this->assertEquals(0, filesize($mediaLocalPath), "CURL should be empty, ");
			$this->assertArrayHasKey('x-kaltura', $headers, "Should raise Kaltura error, ");
			$this->assertEquals('error-3', $headers['x-kaltura'], "Should raise Kaltura access control restriction error 3, ");
		}
		
		if(file_exists($mediaLocalPath))
			unlink($mediaLocalPath);
	}
	
	/**
	 * Tests PS2 serveFlavor action
	 * @param KalturaAccessControlProfile $accessControlProfile
	 * @param string $mediaFilePath
	 * @param string $referrer
	 * @param bool $success
	 * @dataProvider provideData
	 */
	public function testServeFlavor(KalturaAccessControlProfile $accessControlProfile, $mediaFilePath, $referrer, $success)
	{
//		throw new PHPUnit_Framework_SkippedTestError('Already works');
	
		// creating access control profile
		$addedAccessControl = $this->client->accessControlProfile->add($accessControlProfile);
		/* @var $addedAccessControl KalturaAccessControl */
		if(method_exists($this, 'assertInstanceOf'))
			$this->assertInstanceOf('KalturaAccessControlProfile', $addedAccessControl);
		else
			$this->assertType('KalturaAccessControlProfile', $addedAccessControl);
		$this->assertNotNull($addedAccessControl->id);
		
		$addedEntry = $this->createReadyMediaEntry($addedAccessControl->id, $mediaFilePath);
		
		$flavorAssetFilter = new KalturaFlavorAssetFilter();
		$flavorAssetFilter->entryIdEqual = $addedEntry->id;
		$flavorAssetFilter->statusEqual = KalturaFlavorAssetStatus::READY;
		$flavorAssets = $this->client->flavorAsset->listAction($flavorAssetFilter);
		/* @var $flavorAssets KalturaFlavorAssetListResponse */
		
		$this->assertGreaterThan(0, $flavorAssets->totalCount, "Flavor assets not found");
		$this->assertGreaterThan(0, count($flavorAssets->objects), "Flavor assets objects not found");

		$flavorAsset = reset($flavorAssets->objects);
		/* @var $flavorAsset KalturaFlavorAsset */
		
		$mediaLocalPath = dirname(__FILE__) . DIRECTORY_SEPARATOR . $this->getName() . '.flv';
		if(file_exists($mediaLocalPath))
			unlink($mediaLocalPath);
		
		$testConfig = $this->config->get('config');
		$serveFlavorUrl = "http://{$testConfig->serviceUrl}/p/{$addedEntry->partnerId}/sp/{$addedEntry->partnerId}00/serveFlavor/entry_id/{$addedEntry->id}/flavorId/{$flavorAsset->id}/referrer/$referrer";
	
		$headers = null;
		$errCode = $this->cUrl($serveFlavorUrl, $mediaLocalPath, $headers);
	
		$this->assertEquals(200, $errCode, "CURL should success, ");
		if($success)
		{
			$this->assertGreaterThan(0, filesize($mediaLocalPath), "File [$mediaLocalPath] should not be empty");	
			$this->assertArrayNotHasKey('x-kaltura', $headers, "Should not raise Kaltura error, ");
		}
		else 
		{
			$this->assertEquals(0, filesize($mediaLocalPath), "CURL should be empty, ");
			$this->assertArrayHasKey('x-kaltura', $headers, "Should raise Kaltura error, ");
			$this->assertEquals('error-3', $headers['x-kaltura'], "Should raise Kaltura access control restriction error 3, ");
		}
		
		if(file_exists($mediaLocalPath))
			unlink($mediaLocalPath);
	}
	
	/**
	 * Tests backward compatibility
	 * @param KalturaAccessControlProfile $accessControlProfile
	 * @param KalturaAccessControl $reference
	 * @dataProvider provideData
	 */
	public function testMigrate(KalturaAccessControlProfile $accessControlProfile, KalturaAccessControl $reference)
	{
		$resultObject1 = $this->client->accessControlProfile->add($accessControlProfile);
		if(method_exists($this, 'assertInstanceOf'))
			$this->assertInstanceOf('KalturaAccessControlProfile', $resultObject1);
		else
			$this->assertType('KalturaAccessControlProfile', $resultObject1);
		$this->assertNotNull($resultObject1->id);
		$this->assertNotNull($resultObject1->partnerId);
		$this->assertNotNull($resultObject1->createdAt);
		$this->assertNotNull($resultObject1->isDefault);
		
		$resultObject2 = $this->client->accessControl->get($resultObject1->id);
		if(method_exists($this, 'assertInstanceOf'))
			$this->assertInstanceOf('KalturaAccessControl', $resultObject2);
		else
			$this->assertType('KalturaAccessControl', $resultObject2);
		$this->assertAPIObjects($resultObject1, $resultObject2, array('updatedAt', 'rules', 'restrictions', 'containsUnsuportedRestrictions'));
		$this->assertAPIObjects($reference, $resultObject2, array('createdAt', 'updatedAt', 'id', 'deletedAt'));
	}	
}

