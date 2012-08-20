<?php

require_once(dirname(__FILE__) . '/../../bootstrap.php');

/**
 * accessControl service test case.
 */
class AccessControlServiceTest extends AccessControlServiceTestBase
{
	/* (non-PHPdoc)
	 * @see AccessControlServiceTestBase::validateAdd()
	 */
	protected function validateAdd(KalturaAccessControl $resultObject)
	{
		$this->assertNotNull($resultObject->id);
		$this->assertNotNull($resultObject->partnerId);
		$this->assertNotNull($resultObject->createdAt);
		$this->assertNotNull($resultObject->isDefault);
	}


	/**
	 * Tests baseEntry->getContextData
	 * @param KalturaAccessControl $accessControl
	 * @param KalturaEntryContextDataParams $contextDataParams
	 * @param KalturaEntryContextDataResult $contextDataResultReference
	 * @dataProvider provideData
	 */
	public function testGetContext(KalturaAccessControl $accessControl, KalturaEntryContextDataParams $contextDataParams, KalturaEntryContextDataResult $contextDataResultReference)
	{
//		throw new PHPUnit_Framework_SkippedTestError('Already works');
		
		// creating access control profile
		$addedAccessControl = $this->client->accessControl->add($accessControl);
		/* @var $addedAccessControl KalturaAccessControl */
		if(method_exists($this, 'assertNotInstanceOf'))
			$this->assertNotInstanceOf('KalturaAccessControl', $addedAccessControl);
		else
			$this->assertNotType('KalturaAccessControl', get_class($addedAccessControl));
		$this->assertNotNull($addedAccessControl->id);
		
		// creating entry
		$entry = new KalturaMediaEntry();
		$entry->mediaType = KalturaMediaType::VIDEO;
		$entry->name = $this->getName();
		$entry->accessControlId = $addedAccessControl->id;
		$addedEntry = $this->client->media->add($entry);
		/* @var $addedEntry KalturaMediaEntry */
		if(method_exists($this, 'assertNotInstanceOf'))
			$this->assertNotInstanceOf('KalturaMediaEntry', $addedEntry);
		else
			$this->assertNotType('KalturaMediaEntry', get_class($addedEntry));
		$this->assertNotNull($addedEntry->id);
		
		$testConfig = $this->config->get('config');
		$contextDataParams->ks = $this->client->generateSession($testConfig->secret, '', KalturaSessionType::USER, $testConfig->partnerId);
		$contextDataResult = $this->client->baseEntry->getContextData($addedEntry->id, $contextDataParams);
		/* @var $contextDataResult KalturaEntryContextDataResult */
		$this->assertAPIObjects($contextDataResultReference, $contextDataResult);
		
		$this->client->accessControl->delete($addedAccessControl->id);
	}
	
	/**
	 * @param int $accessControlId
	 * @param string $mediaFilePath
	 * @return KalturaMediaEntry
	 */
	protected function createReadyMediaEntry($accessControlId, $mediaFilePath)
	{
		// creating conversion profile
		$conversionProfile = new KalturaConversionProfile();
		$conversionProfile->name = $this->getName();
		$conversionProfile->flavorParamsIds = 0;
		$addedConversionProfile = $this->client->conversionProfile->add($conversionProfile);
		/* @var $addedConversionProfile KalturaConversionProfile */
		if(method_exists($this, 'assertNotInstanceOf'))
			$this->assertNotInstanceOf('KalturaConversionProfile', $addedConversionProfile);
		else
			$this->assertNotType('KalturaConversionProfile', get_class($addedConversionProfile));
		$this->assertNotNull($addedConversionProfile->id);
		
		// creating entry
		$entry = new KalturaMediaEntry();
		$entry->mediaType = KalturaMediaType::VIDEO;
		$entry->name = $this->getName();
		$entry->accessControlId = $accessControlId;
		$entry->conversionProfileId = $addedConversionProfile->id;
		$addedEntry = $this->client->media->add($entry);
		/* @var $addedEntry KalturaMediaEntry */
		if(method_exists($this, 'assertNotInstanceOf'))
			$this->assertNotInstanceOf('KalturaMediaEntry', $addedEntry);
		else
			$this->assertNotType('KalturaMediaEntry', get_class($addedEntry));
		$this->assertNotNull($addedEntry->id);
		$this->assertEquals($addedEntry->status, KalturaEntryStatus::NO_CONTENT);
		
		// creating upload token
		$uploadToken = new KalturaUploadToken();
		$uploadToken->fileName = basename($mediaFilePath);
		$uploadToken->fileSize = filesize($mediaFilePath);
		$addedUploadToken = $this->client->uploadToken->add($uploadToken);
		/* @var $addedUploadToken KalturaUploadToken */
		if(method_exists($this, 'assertNotInstanceOf'))
			$this->assertNotInstanceOf('KalturaUploadToken', $addedUploadToken);
		else
			$this->assertNotType('KalturaUploadToken', get_class($addedUploadToken));
		$this->assertNotNull($addedUploadToken->id);
		$this->assertEquals($addedUploadToken->status, KalturaUploadTokenStatus::PENDING);
		
		// uploading the media
		$uploadedUploadToken = $this->client->uploadToken->upload($addedUploadToken->id, $mediaFilePath);
		/* @var $uploadedUploadToken KalturaUploadToken */
		if(method_exists($this, 'assertNotInstanceOf'))
			$this->assertNotInstanceOf('KalturaUploadToken', $uploadedUploadToken);
		else
			$this->assertNotType('KalturaUploadToken', get_class($uploadedUploadToken));
		$this->assertEquals($uploadedUploadToken->status, KalturaUploadTokenStatus::FULL_UPLOAD);
		
		// ingesting the uploaded file to entry
		$resource = new KalturaUploadedFileTokenResource();
		$resource->token = $uploadedUploadToken->id;
		$ingestedEntry = $this->client->media->addContent($addedEntry->id, $resource);
		/* @var $ingestedEntry KalturaMediaEntry */
		if(method_exists($this, 'assertNotInstanceOf'))
			$this->assertNotInstanceOf('KalturaMediaEntry', $ingestedEntry);
		else
			$this->assertNotType('KalturaMediaEntry', get_class($ingestedEntry));
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
	 * @param KalturaAccessControl $accessControl
	 * @param string $mediaFilePath
	 * @param bool $success
	 * @dataProvider provideData
	 */
	public function testFeed(KalturaAccessControl $accessControl, $mediaFilePath, $success)
	{
		throw new PHPUnit_Framework_SkippedTestError('No need to test feeds, it is currently not affected by access control');
		
		// creating access control profile
		$addedAccessControl = $this->client->accessControl->add($accessControl);
		/* @var $addedAccessControl KalturaAccessControl */
		if(method_exists($this, 'assertNotInstanceOf'))
			$this->assertNotInstanceOf('KalturaAccessControl', $addedAccessControl);
		else
			$this->assertNotType('KalturaAccessControl', get_class($addedAccessControl));
		$this->assertNotNull($addedAccessControl->id);
		
		$addedEntry = $this->createReadyMediaEntry($addedAccessControl->id, $mediaFilePath);
		
		// creating static play list
		$playlist = new KalturaPlaylist();
		$playlist->totalResults = 10;
		$playlist->playlistType = KalturaPlaylistType::STATIC_LIST;
		$playlist->playlistContent = $addedEntry->id;
		$addedPlaylist = $this->client->playlist->add($playlist);
		/* @var $addedPlaylist KalturaPlaylist */
		if(method_exists($this, 'assertNotInstanceOf'))
			$this->assertNotInstanceOf('KalturaPlaylist', $addedPlaylist);
		else
			$this->assertNotType('KalturaPlaylist', get_class($addedPlaylist));
		$this->assertNotNull($addedPlaylist->id);
		
		// creating generic feed
		$feed = new KalturaGenericSyndicationFeed();
		$feed->playlistId = $addedPlaylist->id;
		$addedFeed = $this->client->syndicationFeed->add($feed);
		/* @var $addedFeed KalturaGenericSyndicationFeed */
		if(method_exists($this, 'assertNotInstanceOf'))
			$this->assertNotInstanceOf('KalturaGenericSyndicationFeed', $addedFeed);
		else
			$this->assertNotType('KalturaGenericSyndicationFeed', get_class($addedFeed));
		$this->assertNotNull($addedFeed->id);
		$this->assertNotNull($addedFeed->feedUrl);
		
		$xml = new DOMDocument();
		$xml->load($addedFeed->feedUrl);
		KalturaLog::debug($xml->saveXML());
		
		$xPath = new DOMXPath($xml);
		$elementsList = $xPath->query("//channel/items/item/entryId[string() = '{$addedEntry->id}']");
		$this->assertEquals($success ? 1 : 0, $elementsList->length);
		
		$this->client->accessControl->delete($addedAccessControl->id);
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
	 * @param KalturaAccessControl $accessControl
	 * @param string $mediaFilePath
	 * @param string $referrer
	 * @param bool $success
	 * @dataProvider provideData
	 */
	public function testThumbnail(KalturaAccessControl $accessControl, $mediaFilePath, $referrer, $success)
	{
//		throw new PHPUnit_Framework_SkippedTestError('Already works');
		
		// creating access control profile
		$addedAccessControl = $this->client->accessControl->add($accessControl);
		/* @var $addedAccessControl KalturaAccessControl */
		if(method_exists($this, 'assertNotInstanceOf'))
			$this->assertNotInstanceOf('KalturaAccessControl', $addedAccessControl);
		else
			$this->assertNotType('KalturaAccessControl', get_class($addedAccessControl));
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
		
		$this->client->accessControl->delete($addedAccessControl->id);
	}
	
	/**
	 * Tests PS2 playManifest action
	 * @param KalturaAccessControl $accessControl
	 * @param string $mediaFilePath
	 * @param string $referrer
	 * @param bool $success
	 * @dataProvider provideData
	 */
	public function testPlayManifest(KalturaAccessControl $accessControl, $mediaFilePath, $referrer, $success)
	{
//		throw new PHPUnit_Framework_SkippedTestError('Already works');
		
		// creating access control profile
		$addedAccessControl = $this->client->accessControl->add($accessControl);
		/* @var $addedAccessControl KalturaAccessControl */
		if(method_exists($this, 'assertNotInstanceOf'))
			$this->assertNotInstanceOf('KalturaAccessControl', $addedAccessControl);
		else
			$this->assertNotType('KalturaAccessControl', get_class($addedAccessControl));
		$this->assertNotNull($addedAccessControl->id);
		
		$addedEntry = $this->createReadyMediaEntry($addedAccessControl->id, $mediaFilePath);
		
		$testConfig = $this->config->get('config');
		$playManifestUrl = "http://{$testConfig->serviceUrl}/p/{$addedEntry->partnerId}/sp/{$addedEntry->partnerId}00/playManifest/entryId/{$addedEntry->id}/a/a.f4m";
		$playManifestPath = dirname(__FILE__) . DIRECTORY_SEPARATOR . $this->getName() . '.manifest.xml';
		
		$headers = null;
		$errCode = $this->cUrl($playManifestUrl, $playManifestPath, $headers);
		
		$this->assertEquals(200, $errCode, "Play manifest HTTP request should success. ");
	
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
			$this->assertEquals(0, filesize($playManifestPath), "Manifest should be empty, ");
			$this->assertArrayHasKey('x-kaltura', $headers, "Should raise Kaltura error, ");
			$this->assertEquals('error-3', $headers['x-kaltura'], "Should raise Kaltura access control restriction error 3, ");
		}
		
		if(file_exists($playManifestPath))
			unlink($playManifestPath);
		
		$this->client->accessControl->delete($addedAccessControl->id);
	}
	
	/**
	 * Tests PS2 raw action
	 * @param KalturaAccessControl $accessControl
	 * @param string $mediaFilePath
	 * @param string $referrer
	 * @param bool $success
	 * @dataProvider provideData
	 */
	public function testRaw(KalturaAccessControl $accessControl, $mediaFilePath, $referrer, $success)
	{
//		throw new PHPUnit_Framework_SkippedTestError('Already works');
		
		// creating access control profile
		$addedAccessControl = $this->client->accessControl->add($accessControl);
		/* @var $addedAccessControl KalturaAccessControl */
		if(method_exists($this, 'assertNotInstanceOf'))
			$this->assertNotInstanceOf('KalturaAccessControl', $addedAccessControl);
		else
			$this->assertNotType('KalturaAccessControl', get_class($addedAccessControl));
		$this->assertNotNull($addedAccessControl->id);
		
		$addedEntry = $this->createReadyMediaEntry($addedAccessControl->id, $mediaFilePath);
		KalturaLog::debug(print_r($addedEntry, true));
		
		$mediaLocalPath = dirname(__FILE__) . DIRECTORY_SEPARATOR . $this->getName() . '.$mediaLocalPath';
		
		$headers = null;
		$errCode = $this->cUrl($addedEntry->downloadUrl, $mediaLocalPath, $headers);
	
		$this->assertEquals(200, $errCode, "Raw HTTP request should success. ");
		if($success)
		{
			$this->assertGreaterThan(0, filesize($mediaLocalPath), "File [$mediaLocalPath] is empty. ");	
			$this->assertArrayNotHasKey('x-kaltura', $headers, "Should not raise Kaltura error. ");
		}
		else 
		{
			$this->assertEquals(0, filesize($mediaLocalPath), "Raw action file should be empty. ");
			$this->assertArrayHasKey('x-kaltura', $headers, "Should raise Kaltura error. ");
			$this->assertEquals('error-3', $headers['x-kaltura'], "Should raise Kaltura access control restriction error 3. ");
		}
		
		if(file_exists($mediaLocalPath))
			unlink($mediaLocalPath);
		
		$this->client->accessControl->delete($addedAccessControl->id);
	}
	
	/**
	 * Tests PS2 download action
	 * @param KalturaAccessControl $accessControl
	 * @param string $mediaFilePath
	 * @param string $referrer
	 * @param bool $success
	 * @dataProvider provideData
	 */
	public function testDownload(KalturaAccessControl $accessControl, $mediaFilePath, $referrer, $success)
	{
//		throw new PHPUnit_Framework_SkippedTestError('Already works');
		
		// creating access control profile
		$addedAccessControl = $this->client->accessControl->add($accessControl);
		/* @var $addedAccessControl KalturaAccessControl */
		if(method_exists($this, 'assertNotInstanceOf'))
			$this->assertNotInstanceOf('KalturaAccessControl', $addedAccessControl);
		else
			$this->assertNotType('KalturaAccessControl', get_class($addedAccessControl));
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
			$this->assertEquals(302, $errCode, "Download HTTP request should success. ");
		
			$this->assertEquals(0, filesize($mediaLocalPath), "File [$mediaLocalPath] should be relocated. ");	
			$this->assertArrayNotHasKey('x-kaltura', $headers, "Should not raise Kaltura error. ");
		}
		else 
		{
			$this->assertEquals(200, $errCode, "Download HTTP request should success. ");
		
			$this->assertEquals(0, filesize($mediaLocalPath), "Downloaded file should be empty. ");
			$this->assertArrayHasKey('x-kaltura', $headers, "Should raise Kaltura error. ");
			$this->assertEquals('error-3', $headers['x-kaltura'], "Should raise Kaltura access control restriction error 3. ");
		}
		
		if(file_exists($mediaLocalPath))
			unlink($mediaLocalPath);
			
		$this->client->accessControl->delete($addedAccessControl->id);
	}
	
	/**
	 * Tests PS2 flvClipper action
	 * @param KalturaAccessControl $accessControl
	 * @param string $mediaFilePath
	 * @param string $referrer
	 * @param bool $success
	 * @dataProvider provideData
	 */
	public function testFlvClipper(KalturaAccessControl $accessControl, $mediaFilePath, $referrer, $success)
	{
//		throw new PHPUnit_Framework_SkippedTestError('Already works');
		
		// creating access control profile
		$addedAccessControl = $this->client->accessControl->add($accessControl);
		/* @var $addedAccessControl KalturaAccessControl */
		if(method_exists($this, 'assertNotInstanceOf'))
			$this->assertNotInstanceOf('KalturaAccessControl', $addedAccessControl);
		else
			$this->assertNotType('KalturaAccessControl', get_class($addedAccessControl));
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
			$this->assertEquals(302, $errCode, "FLV clipper http request should success. ");
		
			$this->assertEquals(0, filesize($mediaLocalPath), "File [$mediaLocalPath] should be relocated. ");	
			$this->assertArrayNotHasKey('x-kaltura', $headers, "Should not raise Kaltura error, ");
		}
		else 
		{
			$this->assertEquals(200, $errCode, "FLV clipper http request should success. ");
		
			$this->assertEquals(0, filesize($mediaLocalPath), "CURL should be empty. ");
			$this->assertArrayHasKey('x-kaltura', $headers, "Should raise Kaltura error. ");
			$this->assertEquals('error-3', $headers['x-kaltura'], "Should raise Kaltura access control restriction error 3. ");
		}
		
		if(file_exists($mediaLocalPath))
			unlink($mediaLocalPath);
		
		$this->client->accessControl->delete($addedAccessControl->id);
	}
	
	/**
	 * Tests PS2 serveFlavor action
	 * @param KalturaAccessControl $accessControl
	 * @param string $mediaFilePath
	 * @param string $referrer
	 * @param bool $success
	 * @dataProvider provideData
	 */
	public function testServeFlavor(KalturaAccessControl $accessControl, $mediaFilePath, $referrer, $success)
	{
//		throw new PHPUnit_Framework_SkippedTestError('Already works');
		
		// creating access control profile
		$addedAccessControl = $this->client->accessControl->add($accessControl);
		/* @var $addedAccessControl KalturaAccessControl */
		if(method_exists($this, 'assertNotInstanceOf'))
			$this->assertNotInstanceOf('KalturaAccessControl', $addedAccessControl);
		else
			$this->assertNotType('KalturaAccessControl', get_class($addedAccessControl));
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
		
		$this->client->accessControl->delete($addedAccessControl->id);
	}
	
	/**
	 * Tests backward compatibility
	 * @param KalturaAccessControl $accessControl
	 * @param KalturaAccessControlProfile $reference
	 * @dataProvider provideData
	 */
	public function testMigrate(KalturaAccessControl $accessControl, KalturaAccessControlProfile $reference)
	{
		$resultObject1 = $this->client->accessControl->add($accessControl);
		if(method_exists($this, 'assertNotInstanceOf'))
			$this->assertNotInstanceOf('KalturaAccessControl', $resultObject1);
		else
			$this->assertNotType('KalturaAccessControl', get_class($resultObject1));
		$this->assertNotNull($resultObject1->id);
		$this->assertNotNull($resultObject1->partnerId);
		$this->assertNotNull($resultObject1->createdAt);
		$this->assertNotNull($resultObject1->isDefault);
		
		$resultObject2 = $this->client->accessControlProfile->get($resultObject1->id);
		if(method_exists($this, 'assertNotInstanceOf'))
			$this->assertNotInstanceOf('KalturaAccessControlProfile', $resultObject2);
		else
			$this->assertNotType('KalturaAccessControlProfile', get_class($resultObject2));
		$this->assertAPIObjects($resultObject1, $resultObject2, array('updatedAt', 'rules', 'restrictions', 'containsUnsuportedRestrictions'));
		$this->assertAPIObjects($reference, $resultObject2, array('createdAt', 'updatedAt', 'id', 'deletedAt'));
		
		$this->client->accessControl->delete($resultObject1->id);
	}
}

