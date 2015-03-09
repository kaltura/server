<?php
/**
 * @namespace
 */
namespace Test;

use Kaltura\Client\Type\MediaEntryFilterForPlaylist;
use Kaltura\Client\Type\MediaEntryFilter;
use Kaltura\Client\Type\UploadedFileTokenResource;
use Kaltura\Client\Enum\EntryStatus;
use Kaltura\Client\Enum\MediaType;
use Kaltura\Client\Type\MediaEntry;
use Kaltura\Client\Enum\UploadTokenStatus;
use Kaltura\Client\Type\UploadToken;
use Kaltura\Client\Type\MixEntry;
use Kaltura\Client\Enum\EditorType;
use Kaltura\Client\ApiException;
use Exception;

class Zend2ClientTester
{
	const UPLOAD_VIDEO_FILENAME = 'DemoVideo.flv';
	const UPLOAD_IMAGE_FILENAME = 'DemoImage.jpg';
	const ENTRY_NAME = 'Media entry uploaded from Zend Framework 2 client library';
	
	/**
	 * @var \Kaltura\Client\Client
	 */
	protected $_client;
	
	/**
	 * @var int
	 */
	protected $_partnerId;
	
	public function __construct(\Kaltura\Client\Client $client, $partnerId)
	{
		$this->_client = $client;
		$this->_partnerId = $partnerId;
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

		$mixEntry = new MixEntry();
		$mixEntry->name = ".Net Mix";
		$mixEntry->editorType = EditorType::SIMPLE;

		# Request 1
		$mixEntry = $this->_client->getMixingService()->add($mixEntry);

		# Request 2
		$uploadFilePath = dirname(__FILE__) . '/../resources/' . self::UPLOAD_VIDEO_FILENAME;
    	$uploadTokenId = $this->_client->getMediaService()->upload($uploadFilePath);

		$mediaEntry = new MediaEntry();
		$mediaEntry->name = "Media Entry For Mix";
		$mediaEntry->mediaType = MediaType::VIDEO;

		# Request 3
		$mediaEntry = $this->_client->getMediaService()->addFromUploadedFile($mediaEntry, $uploadTokenId);

		# Request 4
		$this->_client->getMixingService()->appendMediaEntry($mixEntry->id, $mediaEntry->id);

		$response = $this->_client->doMultiRequest();

		foreach( $response as $subResponse)
			if($subResponse instanceof ApiException) 
				throw new Exception("Error occurred: " . $subResponse->getMessage());

		# when accessing the response object we will use an index and not the response number (response number - 1)
		$this->assertTrue($response[0] instanceof MixEntry);
		$mixEntry = $response[0];
		
		if(is_null($mixEntry->id))
			throw new \Exception("Failed to add entry within multi request");
	}
	
	public function testSyncFlow()
	{
		$this->_client->getSystemService();
		
		// add upload token
		$uploadToken = new UploadToken();
		$uploadToken->fileName = self::UPLOAD_VIDEO_FILENAME;
		$uploadToken = $this->_client->getUploadTokenService()->add($uploadToken);
		$this->assertTrue(strlen($uploadToken->id) > 0);
    	$this->assertEqual($uploadToken->fileName, self::UPLOAD_VIDEO_FILENAME);
    	$this->assertEqual($uploadToken->status, UploadTokenStatus::PENDING);
    	$this->assertEqual($uploadToken->partnerId, $this->_partnerId);
    	$this->assertEqual($uploadToken->fileSize, null);
    	
    	// add media entry
    	$entry = new MediaEntry();
    	$entry->name = self::ENTRY_NAME;
    	$entry->mediaType = MediaType::VIDEO;
    	$entry = $this->_client->getMediaService()->add($entry);
    	$this->assertTrue(strlen($entry->id) > 0);
    	$this->assertTrue($entry->status === EntryStatus::NO_CONTENT);
    	$this->assertTrue($entry->name === self::ENTRY_NAME);
    	$this->assertTrue($entry->partnerId === $this->_partnerId);
    	
    	// add uploaded token as resource
    	$resource = new UploadedFileTokenResource();
    	$resource->token = $uploadToken->id;
    	$entry = $this->_client->getMediaService()->addContent($entry->id, $resource);
    	$this->assertTrue($entry->status === EntryStatus::IMPORT);
    	
    	// upload file using the upload token
    	$uploadFilePath = dirname(__FILE__) . '/../resources/' . self::UPLOAD_VIDEO_FILENAME;
    	$uploadToken = $this->_client->getUploadTokenService()->upload($uploadToken->id, $uploadFilePath);
    	$this->assertTrue($uploadToken->status === UploadTokenStatus::CLOSED);
    	
    	// get flavor by entry
    	$flavorArray = $this->_client->getFlavorAssetService()->getByEntryId($entry->id);
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
    	$mediaFilter = new MediaEntryFilter();
    	$mediaFilter->idEqual = $entry->id;
    	$mediaFilter->statusNotEqual = EntryStatus::DELETED;
    	$entryCount = $this->_client->getMediaService()->count($mediaFilter);
    	$this->assertTrue($entryCount === 1);
    	
    	// delete media entry
    	$this->_client->getMediaService()->delete($entry->id);
    	
    	sleep(5); // wait for the status to update
    	
    	// count media entries again
		$entryCount = $this->_client->getMediaService()->count($mediaFilter);
    	$this->assertEqual($entryCount, 0);
	}
	
	public function testReturnedArrayObjectUsingPlaylistExecute()
	{
		// add image entry
    	$imageEntry = $this->addImageEntry();
    	
    	// execute playlist from filters
    	$playlistFilter = new MediaEntryFilterForPlaylist();
    	$playlistFilter->idEqual = $imageEntry->id;
    	$filterArray = array();
    	$filterArray[] = $playlistFilter;
    	$playlistExecute = $this->_client->getPlaylistService()->executeFromFilters($filterArray, 10);
    	$this->assertEqual(count($playlistExecute), 1);
    	$firstPlaylistEntry = $playlistExecute[0];
    	$this->assertEqual($firstPlaylistEntry->id, $imageEntry->id);
    	
    	$this->_client->getMediaService()->delete($imageEntry->id);
	}
	
	public function addImageEntry()
	{
		$entry = new MediaEntry();
    	$entry->name = self::ENTRY_NAME;
    	$entry->mediaType = MediaType::IMAGE;
    	$entry = $this->_client->getMediaService()->add($entry);
    	
    	$uploadToken = new UploadToken();
		$uploadToken->fileName = self::UPLOAD_IMAGE_FILENAME;
		$uploadToken = $this->_client->getUploadTokenService()->add($uploadToken);

    	$uploadFilePath = dirname(__FILE__) . '/../resources/' . self::UPLOAD_IMAGE_FILENAME;
    	$uploadToken = $this->_client->getUploadTokenService()->upload($uploadToken->id, $uploadFilePath);
    	
		$resource = new UploadedFileTokenResource();
    	$resource->token = $uploadToken->id;
    	$entry = $this->_client->getMediaService()->addContent($entry->id, $resource);
    	
    	return $entry;
	}
	
	protected function assertTrue($v)
	{
		if ($v !== true)
		{
			$backtrace = debug_backtrace();
			$msg = 'Assert failed on line: ' . $backtrace[0]['line'];
			throw new \Exception($msg);
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
			throw new \Exception($msg);
		}
	}
}