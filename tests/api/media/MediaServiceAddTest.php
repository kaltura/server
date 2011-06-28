<?php
require_once(dirname(__FILE__) . '/../../bootstrap.php');

/**
 * media service add test case.
 */
class MediaServiceAddTest extends KalturaApiTestCase
{
	static protected $category = null;
	
	protected static function getCategory()
	{
		if(!self::$category)
			self::$category = 'tests>test_' . date('d_m_H_i');
			
		return self::$category;
	}
	
	public function testAddVideoUrlResource()
	{
		$entry					= new KalturaMediaEntry();
		$entry->mediaType		= KalturaMediaType::VIDEO;
		$entry->name			= 'VideoUrlResource';
		$entry->description		= 'Expected statuses: no content, import, converting, ready';
		$entry->categories		= self::getCategory();
		
		$resource				= new KalturaUrlResource();
		$resource->url			= 'http://sites.google.com/site/demokmc/Home/spot.whats.mov';

		$resultEntry = $this->client->media->add($entry);
		$this->assertType('KalturaMediaEntry', $resultEntry, 'Created entry of wrong type');
		$this->assertNotNull($resultEntry->id, 'Created entry without id');
		$this->assertEquals(KalturaEntryStatus::NO_CONTENT, $resultEntry->status, 'Created entry with wrong status');
		
		$resultEntry = $this->client->media->addContent($resultEntry->id, $resource);
		$this->assertType('KalturaMediaEntry', $resultEntry, 'Ingested entry of wrong type');
		$this->assertEquals(KalturaEntryStatus::IMPORT, $resultEntry->status, "Ingested entry of wrong status [{$resultEntry->status}] entry id [{$resultEntry->id}]");
	}
	
	public function testAddVideoUrlResourceNoConvert()
	{
		$entry						= new KalturaMediaEntry();
		$entry->conversionProfileId	= -1;
		$entry->mediaType			= KalturaMediaType::VIDEO;
		$entry->name				= 'VideoUrlResource - No Convert';
		$entry->description			= 'Expected statuses: no content, import, pending';
		$entry->categories			= self::getCategory();
		
		$resource					= new KalturaUrlResource();
		$resource->url				= 'http://sites.google.com/site/demokmc/Home/spot.whats.mov';

		$resultEntry = $this->client->media->add($entry);
		$this->assertType('KalturaMediaEntry', $resultEntry, 'Created entry of wrong type');
		$this->assertNotNull($resultEntry->id, 'Created entry without id');
		$this->assertEquals(KalturaEntryStatus::NO_CONTENT, $resultEntry->status, 'Created entry with wrong status');
		
		$resultEntry = $this->client->media->addContent($resultEntry->id, $resource);
		$this->assertType('KalturaMediaEntry', $resultEntry, 'Ingested entry of wrong type');
		$this->assertEquals(KalturaEntryStatus::IMPORT, $resultEntry->status, "Ingested entry of wrong status [{$resultEntry->status}] entry id [{$resultEntry->id}]");
	}
	
	public function testAddVideoUrlResourceFromTemplate()
	{
		$entry						= new KalturaMediaEntry();
		$entry->conversionProfileId	= 1197623;
		$entry->mediaType			= KalturaMediaType::VIDEO;
		$entry->name				= 'VideoUrlResource - From Template';
		
		$resource					= new KalturaUrlResource();
		$resource->url				= 'http://sites.google.com/site/demokmc/Home/spot.whats.mov';

		$resultEntry = $this->client->media->add($entry);
		$this->assertType('KalturaMediaEntry', $resultEntry, 'Created entry of wrong type');
		$this->assertNotNull($resultEntry->id, 'Created entry without id');
		$this->assertEquals(KalturaEntryStatus::NO_CONTENT, $resultEntry->status, 'Created entry with wrong status');
		
		$resultEntry = $this->client->media->addContent($resultEntry->id, $resource);
		$this->assertType('KalturaMediaEntry', $resultEntry, 'Ingested entry of wrong type');
		$this->assertEquals(KalturaEntryStatus::IMPORT, $resultEntry->status, "Ingested entry of wrong status [{$resultEntry->status}] entry id [{$resultEntry->id}]");
	}
	
	public function testAddVideoUrlResourceFromMetadataTemplate()
	{
		$entry						= new KalturaMediaEntry();
		$entry->conversionProfileId	= 1197626;
		$entry->mediaType			= KalturaMediaType::VIDEO;
		$entry->name				= 'VideoUrlResource - From Metadata Template';
		
		$resource					= new KalturaUrlResource();
		$resource->url				= 'http://sites.google.com/site/demokmc/Home/spot.whats.mov';

		$resultEntry = $this->client->media->add($entry);
		$this->assertType('KalturaMediaEntry', $resultEntry, 'Created entry of wrong type');
		$this->assertNotNull($resultEntry->id, 'Created entry without id');
		$this->assertEquals(KalturaEntryStatus::NO_CONTENT, $resultEntry->status, 'Created entry with wrong status');
		
		$resultEntry = $this->client->media->addContent($resultEntry->id, $resource);
		$this->assertType('KalturaMediaEntry', $resultEntry, 'Ingested entry of wrong type');
		$this->assertEquals(KalturaEntryStatus::IMPORT, $resultEntry->status, "Ingested entry of wrong status [{$resultEntry->status}] entry id [{$resultEntry->id}]");
	}
	
	public function testAddImageUrlResourceFromTemplate()
	{
		$entry						= new KalturaMediaEntry();
		$entry->conversionProfileId	= 1197625;
		$entry->mediaType			= KalturaMediaType::IMAGE;
		$entry->name				= 'ImageUrlResource - From Template';
		
		$resource					= new KalturaUrlResource();
		$resource->url				= 'http://cdnbakmi.kaltura.com/p/547921/sp/54792100/flvclipper/entry_id/1_g001t1ae/version/100000';

		$resultEntry = $this->client->media->add($entry);
		$this->assertType('KalturaMediaEntry', $resultEntry, 'Created entry of wrong type');
		$this->assertNotNull($resultEntry->id, 'Created entry without id');
		$this->assertEquals(KalturaEntryStatus::NO_CONTENT, $resultEntry->status, 'Created entry with wrong status');
		
		$resultEntry = $this->client->media->addContent($resultEntry->id, $resource);
		$this->assertType('KalturaMediaEntry', $resultEntry, 'Ingested entry of wrong type');
		$this->assertEquals(KalturaEntryStatus::READY, $resultEntry->status, "Ingested entry of wrong status [{$resultEntry->status}] entry id [{$resultEntry->id}]");
	}
	
	public function testAddVideoRemoteStorageResource()
	{
		$entry							= new KalturaMediaEntry();
		$entry->mediaType				= KalturaMediaType::VIDEO;
		$entry->name					= 'VideoRemoteStorageResource';
		$entry->description				= 'Expected statuses: no content, importing, converting, ready';
		$entry->categories				= self::getCategory();
		
		$resource						= new KalturaRemoteStorageResource();
		$resource->url					= 'http://sites.google.com/site/demokmc/Home/spot.whats.mov';
		$resource->storageProfileId		= 92;

		$resultEntry = $this->client->media->add($entry);
		$this->assertType('KalturaMediaEntry', $resultEntry, 'Created entry of wrong type');
		$this->assertNotNull($resultEntry->id, 'Created entry without id');
		$this->assertEquals(KalturaEntryStatus::NO_CONTENT, $resultEntry->status, 'Created entry with wrong status');
		
		$resultEntry = $this->client->media->addContent($resultEntry->id, $resource);
		$this->assertType('KalturaMediaEntry', $resultEntry, 'Ingested entry of wrong type');
		$this->assertEquals(KalturaEntryStatus::IMPORT, $resultEntry->status, "Ingested entry of wrong status [{$resultEntry->status}] entry id [{$resultEntry->id}]");
	}
	
	public function testAddVideoFileSyncResource()
	{
		$entry							= new KalturaMediaEntry();
		$entry->mediaType				= KalturaMediaType::VIDEO;
		$entry->name					= 'VideoFileSyncResource';
		$entry->description				= 'Expected statuses: no content, converting, ready';
		$entry->categories				= self::getCategory();
		
		$resource						= new KalturaFileSyncResource();
		$resource->fileSyncObjectType	= 4;
		$resource->objectSubType		= 1;
		$resource->objectId				= '0_f5trqmyv';

		$resultEntry = $this->client->media->add($entry);
		$this->assertType('KalturaMediaEntry', $resultEntry, 'Created entry of wrong type');
		$this->assertNotNull($resultEntry->id, 'Created entry without id');
		$this->assertEquals(KalturaEntryStatus::NO_CONTENT, $resultEntry->status, 'Created entry with wrong status');
		
		$resultEntry = $this->client->media->addContent($resultEntry->id, $resource);
		$this->assertType('KalturaMediaEntry', $resultEntry, 'Ingested entry of wrong type');
		$this->assertEquals(KalturaEntryStatus::PRECONVERT, $resultEntry->status, "Ingested entry of wrong status [{$resultEntry->status}] entry id [{$resultEntry->id}]");
	}
	
	public function testAddVideoLocalFileResource()
	{
		$entry							= new KalturaMediaEntry();
		$entry->mediaType				= KalturaMediaType::VIDEO;
		$entry->name					= 'VideoLocalFileResource';
		$entry->description				= 'Expected statuses: no content, converting, ready';
		$entry->categories				= self::getCategory();
		
		$resource						= new KalturaLocalFileResource();
		$resource->localFilePath		= '/web/content/zbale/myTest.mov';

		$resultEntry = $this->client->media->add($entry);
		$this->assertType('KalturaMediaEntry', $resultEntry, 'Created entry of wrong type');
		$this->assertNotNull($resultEntry->id, 'Created entry without id');
		$this->assertEquals(KalturaEntryStatus::NO_CONTENT, $resultEntry->status, 'Created entry with wrong status');
		
		$resultEntry = $this->client->media->addContent($resultEntry->id, $resource);
		$this->assertType('KalturaMediaEntry', $resultEntry, 'Ingested entry of wrong type');
		$this->assertEquals(KalturaEntryStatus::PRECONVERT, $resultEntry->status, "Ingested entry of wrong status [{$resultEntry->status}] entry id [{$resultEntry->id}]");
	}
	
	public function testAddVideoWebcamTokenResource()
	{
		$entry							= new KalturaMediaEntry();
		$entry->mediaType				= KalturaMediaType::VIDEO;
		$entry->name					= 'VideoWebcamTokenResource';
		$entry->description				= 'Expected statuses: no content, ready';
		$entry->categories				= self::getCategory();
		
		$resource						= new KalturaWebcamTokenResource();
		$resource->token				= '191414FD-C27C-A713-C146-E83871C0EF91';

		$resultEntry = $this->client->media->add($entry);
		$this->assertType('KalturaMediaEntry', $resultEntry, 'Created entry of wrong type');
		$this->assertNotNull($resultEntry->id, 'Created entry without id');
		$this->assertEquals(KalturaEntryStatus::NO_CONTENT, $resultEntry->status, 'Created entry with wrong status');
		
		$resultEntry = $this->client->media->addContent($resultEntry->id, $resource);
		$this->assertType('KalturaMediaEntry', $resultEntry, 'Ingested entry of wrong type');
		$this->assertEquals(KalturaEntryStatus::READY, $resultEntry->status, "Ingested entry of wrong status [{$resultEntry->status}] entry id [{$resultEntry->id}]");
	}
	
	public function testAddVideoEntryResource()
	{
		$entry							= new KalturaMediaEntry();
		$entry->mediaType				= KalturaMediaType::VIDEO;
		$entry->name					= 'VideoEntryResource';
		$entry->description				= 'Expected statuses: no content, converting, ready';
		$entry->categories				= self::getCategory();
		
		$resource						= new KalturaEntryResource();
		$resource->entryId				= '0_hrq0ye5f';
		$resource->flavorParamsId		= 0;

		$resultEntry = $this->client->media->add($entry);
		$this->assertType('KalturaMediaEntry', $resultEntry, 'Created entry of wrong type');
		$this->assertNotNull($resultEntry->id, 'Created entry without id');
		$this->assertEquals(KalturaEntryStatus::NO_CONTENT, $resultEntry->status, 'Created entry with wrong status');
		
		$resultEntry = $this->client->media->addContent($resultEntry->id, $resource);
		$this->assertType('KalturaMediaEntry', $resultEntry, 'Ingested entry of wrong type');
		$this->assertEquals(KalturaEntryStatus::PRECONVERT, $resultEntry->status, "Ingested entry of wrong status [{$resultEntry->status}] entry id [{$resultEntry->id}]");
	}
	
	public function testAddVideoAssetResource()
	{
		$entry							= new KalturaMediaEntry();
		$entry->mediaType				= KalturaMediaType::VIDEO;
		$entry->name					= 'VideoAssetResource';
		$entry->description				= 'Expected statuses: no content, converting, ready';
		$entry->categories				= self::getCategory();
		
		$resource						= new KalturaAssetResource();
		$resource->assetId				= '0_wpn7xktl';

		$resultEntry = $this->client->media->add($entry);
		$this->assertType('KalturaMediaEntry', $resultEntry, 'Created entry of wrong type');
		$this->assertNotNull($resultEntry->id, 'Created entry without id');
		$this->assertEquals(KalturaEntryStatus::NO_CONTENT, $resultEntry->status, 'Created entry with wrong status');
		
		$resultEntry = $this->client->media->addContent($resultEntry->id, $resource);
		$this->assertType('KalturaMediaEntry', $resultEntry, 'Ingested entry of wrong type');
		$this->assertEquals(KalturaEntryStatus::PRECONVERT, $resultEntry->status, "Ingested entry of wrong status [{$resultEntry->status}] entry id [{$resultEntry->id}]");
	}
	
	public function testAddVideoUploadedFileTokenResource()
	{
		$file							= 'C:\Documents and Settings\Tan-Tan\My Documents\My Videos\sample.1.flv';
		
		$entry							= new KalturaMediaEntry();
		$entry->mediaType				= KalturaMediaType::VIDEO;
		$entry->name					= 'VideoUploadedFileTokenResource';
		$entry->description				= 'Expected statuses: no content, importing, converting, ready';
		$entry->categories				= self::getCategory();
		
		$uploadToken					= new KalturaUploadToken();
		$uploadToken->fileName			= basename($file);
		$uploadToken->fileSize			= filesize($file);
		
		$resource						= new KalturaUploadedFileTokenResource();

		$resultEntry = $this->client->media->add($entry);
		$this->assertType('KalturaMediaEntry', $resultEntry, 'Created entry of wrong type');
		$this->assertNotNull($resultEntry->id, 'Created entry without id');
		$this->assertEquals(KalturaEntryStatus::NO_CONTENT, $resultEntry->status, 'Created entry with wrong status');
		
		$resultUploadToken = $this->client->uploadToken->add($uploadToken);
		$this->assertType('KalturaUploadToken', $resultUploadToken, 'Created upload token of wrong type');
		$this->assertNotNull($resultUploadToken->id, 'Created upload token without id');
		$this->assertEquals(KalturaUploadTokenStatus::PENDING, $resultUploadToken->status, 'Created upload token with wrong status');
		
		$resource->token = $resultUploadToken->id;
		
		$resultEntry = $this->client->media->addContent($resultEntry->id, $resource);
		$this->assertType('KalturaMediaEntry', $resultEntry, 'Ingested entry of wrong type');
		$this->assertEquals(KalturaEntryStatus::IMPORT, $resultEntry->status, "Ingested entry of wrong status [{$resultEntry->status}] entry id [{$resultEntry->id}]");
		
		$resultUploadToken = $this->client->uploadToken->upload($resource->token, $file);
		$this->assertType('KalturaUploadToken', $resultUploadToken, 'Uploaded upload token of wrong type');
		$this->assertNotNull($resultUploadToken->id, 'Uploaded upload token without id');
		$this->assertEquals(KalturaUploadTokenStatus::CLOSED, $resultUploadToken->status, 'Uploaded upload token with wrong status');
		
		$resultEntry = $this->client->media->get($resultEntry->id);
		$this->assertType('KalturaMediaEntry', $resultEntry, 'Retrieved entry of wrong type');
		$this->assertEquals(KalturaEntryStatus::PRECONVERT, $resultEntry->status, "Retrieved entry of wrong status [{$resultEntry->status}] entry id [{$resultEntry->id}]");
	}
	
	public function testAddVideoAssetParamsResourceContainer()
	{
		$entry							= new KalturaMediaEntry();
		$entry->mediaType				= KalturaMediaType::VIDEO;
		$entry->name					= 'VideoAssetParamsResourceContainer';
		$entry->description				= 'Expected statuses: no content, converting, ready';
		$entry->categories				= self::getCategory();
		
		$resource							= new KalturaAssetParamsResourceContainer();
		$resource->assetParamsId			= 0;
		$resource->resource					= new KalturaAssetResource();
		$resource->resource->assetId		= '0_wpn7xktl';

		$resultEntry = $this->client->media->add($entry);
		$this->assertType('KalturaMediaEntry', $resultEntry, 'Created entry of wrong type');
		$this->assertNotNull($resultEntry->id, 'Created entry without id');
		$this->assertEquals(KalturaEntryStatus::NO_CONTENT, $resultEntry->status, 'Created entry with wrong status');
		
		$resultEntry = $this->client->media->addContent($resultEntry->id, $resource);
		$this->assertType('KalturaMediaEntry', $resultEntry, 'Ingested entry of wrong type');
		$this->assertEquals(KalturaEntryStatus::PRECONVERT, $resultEntry->status, "Ingested entry of wrong status [{$resultEntry->status}] entry id [{$resultEntry->id}]");
	}
	
	public function testAddVideoAssetsParamsResourceContainers()
	{
		$entry							= new KalturaMediaEntry();
		$entry->mediaType				= KalturaMediaType::VIDEO;
		$entry->name					= 'VideoAssetsParamsResourceContainers';
		$entry->description				= 'Expected statuses: no content, converting, ready';
		$entry->categories				= self::getCategory();
		
		$res1							= new KalturaAssetParamsResourceContainer();
		$res1->assetParamsId			= 0;
		$res1->resource					= new KalturaAssetResource();
		$res1->resource->assetId		= '0_wpn7xktl';
		
		$resource						= new KalturaAssetsParamsResourceContainers();
		$resource->resources			= array($res1);

		$resultEntry = $this->client->media->add($entry);
		$this->assertType('KalturaMediaEntry', $resultEntry, 'Created entry of wrong type');
		$this->assertNotNull($resultEntry->id, 'Created entry without id');
		$this->assertEquals(KalturaEntryStatus::NO_CONTENT, $resultEntry->status, 'Created entry with wrong status');
		
		$resultEntry = $this->client->media->addContent($resultEntry->id, $resource);
		$this->assertType('KalturaMediaEntry', $resultEntry, 'Ingested entry of wrong type');
		$this->assertEquals(KalturaEntryStatus::PRECONVERT, $resultEntry->status, "Ingested entry of wrong status [{$resultEntry->status}] entry id [{$resultEntry->id}]");
	}
	
	public function testAddVideoUrlResourceSourceOnly()
	{
		$entry						= new KalturaMediaEntry();
		$entry->conversionProfileId	= 1197619;
		$entry->mediaType			= KalturaMediaType::VIDEO;
		$entry->name				= 'VideoUrlResource SourceOnly';
		$entry->description			= 'Expected statuses: no content, import, converting, ready';
		$entry->categories			= self::getCategory();
		
		$resource					= new KalturaUrlResource();
		$resource->url				= 'http://sites.google.com/site/demokmc/Home/spot.whats.mov';

		$resultEntry = $this->client->media->add($entry);
		$this->assertType('KalturaMediaEntry', $resultEntry, 'Created entry of wrong type');
		$this->assertNotNull($resultEntry->id, 'Created entry without id');
		$this->assertEquals(KalturaEntryStatus::NO_CONTENT, $resultEntry->status, 'Created entry with wrong status');
		
		$resultEntry = $this->client->media->addContent($resultEntry->id, $resource);
		$this->assertType('KalturaMediaEntry', $resultEntry, 'Ingested entry of wrong type');
		$this->assertEquals(KalturaEntryStatus::IMPORT, $resultEntry->status, "Ingested entry of wrong status [{$resultEntry->status}] entry id [{$resultEntry->id}]");
	}
	
	public function testAddVideoRemoteStorageResourceSourceOnly()
	{
		$entry							= new KalturaMediaEntry();
		$entry->conversionProfileId		= 1197619;
		$entry->mediaType				= KalturaMediaType::VIDEO;
		$entry->name					= 'VideoRemoteStorageResource SourceOnly';
		$entry->description				= 'Expected statuses: no content, ready';
		$entry->categories				= self::getCategory();
		
		$resource						= new KalturaRemoteStorageResource();
		$resource->url					= 'http://sites.google.com/site/demokmc/Home/spot.whats.mov';
		$resource->storageProfileId		= 92;

		$resultEntry = $this->client->media->add($entry);
		$this->assertType('KalturaMediaEntry', $resultEntry, 'Created entry of wrong type');
		$this->assertNotNull($resultEntry->id, 'Created entry without id');
		$this->assertEquals(KalturaEntryStatus::NO_CONTENT, $resultEntry->status, 'Created entry with wrong status');
		
		$resultEntry = $this->client->media->addContent($resultEntry->id, $resource);
		$this->assertType('KalturaMediaEntry', $resultEntry, 'Ingested entry of wrong type');
		$this->assertEquals(KalturaEntryStatus::READY, $resultEntry->status, "Ingested entry of wrong status [{$resultEntry->status}] entry id [{$resultEntry->id}]");
	}
	
	public function testAddVideoFileSyncResourceSourceOnly()
	{
		$entry							= new KalturaMediaEntry();
		$entry->conversionProfileId		= 1197619;
		$entry->mediaType				= KalturaMediaType::VIDEO;
		$entry->name					= 'VideoFileSyncResource SourceOnly';
		$entry->description				= 'Expected statuses: no content, converting, ready';
		$entry->categories				= self::getCategory();
		
		$resource						= new KalturaFileSyncResource();
		$resource->fileSyncObjectType	= 4;
		$resource->objectSubType		= 1;
		$resource->objectId				= '0_f5trqmyv';

		$resultEntry = $this->client->media->add($entry);
		$this->assertType('KalturaMediaEntry', $resultEntry, 'Created entry of wrong type');
		$this->assertNotNull($resultEntry->id, 'Created entry without id');
		$this->assertEquals(KalturaEntryStatus::NO_CONTENT, $resultEntry->status, 'Created entry with wrong status');
		
		$resultEntry = $this->client->media->addContent($resultEntry->id, $resource);
		$this->assertType('KalturaMediaEntry', $resultEntry, 'Ingested entry of wrong type');
		$this->assertEquals(KalturaEntryStatus::PRECONVERT, $resultEntry->status, "Ingested entry of wrong status [{$resultEntry->status}] entry id [{$resultEntry->id}]");
	}
	
	public function testAddVideoLocalFileResourceSourceOnly()
	{
		$entry							= new KalturaMediaEntry();
		$entry->conversionProfileId		= 1197619;
		$entry->mediaType				= KalturaMediaType::VIDEO;
		$entry->name					= 'VideoLocalFileResource SourceOnly';
		$entry->description				= 'Expected statuses: no content, converting, ready';
		$entry->categories				= self::getCategory();
		
		$resource						= new KalturaLocalFileResource();
		$resource->localFilePath		= '/web/content/zbale/myTest.mov';

		$resultEntry = $this->client->media->add($entry);
		$this->assertType('KalturaMediaEntry', $resultEntry, 'Created entry of wrong type');
		$this->assertNotNull($resultEntry->id, 'Created entry without id');
		$this->assertEquals(KalturaEntryStatus::NO_CONTENT, $resultEntry->status, 'Created entry with wrong status');
		
		$resultEntry = $this->client->media->addContent($resultEntry->id, $resource);
		$this->assertType('KalturaMediaEntry', $resultEntry, 'Ingested entry of wrong type');
		$this->assertEquals(KalturaEntryStatus::PRECONVERT, $resultEntry->status, "Ingested entry of wrong status [{$resultEntry->status}] entry id [{$resultEntry->id}]");
	}
	
	public function testAddVideoWebcamTokenResourceSourceOnly()
	{
		$entry							= new KalturaMediaEntry();
		$entry->conversionProfileId		= 1197619;
		$entry->mediaType				= KalturaMediaType::VIDEO;
		$entry->name					= 'VideoWebcamTokenResource SourceOnly';
		$entry->description				= 'Expected statuses: no content, ready';
		$entry->categories				= self::getCategory();
		
		$resource						= new KalturaWebcamTokenResource();
		$resource->token				= '191414FD-C27C-A713-C146-E83871C0EF91';

		$resultEntry = $this->client->media->add($entry);
		$this->assertType('KalturaMediaEntry', $resultEntry, 'Created entry of wrong type');
		$this->assertNotNull($resultEntry->id, 'Created entry without id');
		$this->assertEquals(KalturaEntryStatus::NO_CONTENT, $resultEntry->status, 'Created entry with wrong status');
		
		$resultEntry = $this->client->media->addContent($resultEntry->id, $resource);
		$this->assertType('KalturaMediaEntry', $resultEntry, 'Ingested entry of wrong type');
		$this->assertEquals(KalturaEntryStatus::READY, $resultEntry->status, "Ingested entry of wrong status [{$resultEntry->status}] entry id [{$resultEntry->id}]");
	}
	
	public function testAddVideoEntryResourceSourceOnly()
	{
		$entry							= new KalturaMediaEntry();
		$entry->conversionProfileId		= 1197619;
		$entry->mediaType				= KalturaMediaType::VIDEO;
		$entry->name					= 'VideoEntryResource SourceOnly';
		$entry->description				= 'Expected statuses: no content, converting, ready';
		$entry->categories				= self::getCategory();
		
		$resource						= new KalturaEntryResource();
		$resource->entryId				= '0_hrq0ye5f';
		$resource->flavorParamsId		= 0;

		$resultEntry = $this->client->media->add($entry);
		$this->assertType('KalturaMediaEntry', $resultEntry, 'Created entry of wrong type');
		$this->assertNotNull($resultEntry->id, 'Created entry without id');
		$this->assertEquals(KalturaEntryStatus::NO_CONTENT, $resultEntry->status, 'Created entry with wrong status');
		
		$resultEntry = $this->client->media->addContent($resultEntry->id, $resource);
		$this->assertType('KalturaMediaEntry', $resultEntry, 'Ingested entry of wrong type');
		$this->assertEquals(KalturaEntryStatus::PRECONVERT, $resultEntry->status, "Ingested entry of wrong status [{$resultEntry->status}] entry id [{$resultEntry->id}]");
	}
	
	public function testAddVideoAssetResourceSourceOnly()
	{
		$entry							= new KalturaMediaEntry();
		$entry->conversionProfileId		= 1197619;
		$entry->mediaType				= KalturaMediaType::VIDEO;
		$entry->name					= 'VideoAssetResource SourceOnly';
		$entry->description				= 'Expected statuses: no content, converting, ready';
		$entry->categories				= self::getCategory();
		
		$resource						= new KalturaAssetResource();
		$resource->assetId				= '0_wpn7xktl';

		$resultEntry = $this->client->media->add($entry);
		$this->assertType('KalturaMediaEntry', $resultEntry, 'Created entry of wrong type');
		$this->assertNotNull($resultEntry->id, 'Created entry without id');
		$this->assertEquals(KalturaEntryStatus::NO_CONTENT, $resultEntry->status, 'Created entry with wrong status');
		
		$resultEntry = $this->client->media->addContent($resultEntry->id, $resource);
		$this->assertType('KalturaMediaEntry', $resultEntry, 'Ingested entry of wrong type');
		$this->assertEquals(KalturaEntryStatus::PRECONVERT, $resultEntry->status, "Ingested entry of wrong status [{$resultEntry->status}] entry id [{$resultEntry->id}]");
	}
	
	public function testAddVideoUploadedFileTokenResourceSourceOnly()
	{
		$file							= 'C:\Documents and Settings\Tan-Tan\My Documents\My Videos\sample.1.flv';
		
		$entry							= new KalturaMediaEntry();
		$entry->conversionProfileId		= 1197619;
		$entry->mediaType				= KalturaMediaType::VIDEO;
		$entry->name					= 'VideoUploadedFileTokenResource SourceOnly';
		$entry->description				= 'Expected statuses: no content, importing, converting, ready';
		$entry->categories				= self::getCategory();
		
		$uploadToken					= new KalturaUploadToken();
		$uploadToken->fileName			= basename($file);
		$uploadToken->fileSize			= filesize($file);
		
		$resource						= new KalturaUploadedFileTokenResource();

		$resultEntry = $this->client->media->add($entry);
		$this->assertType('KalturaMediaEntry', $resultEntry, 'Created entry of wrong type');
		$this->assertNotNull($resultEntry->id, 'Created entry without id');
		$this->assertEquals(KalturaEntryStatus::NO_CONTENT, $resultEntry->status, 'Created entry with wrong status');
		
		$resultUploadToken = $this->client->uploadToken->add($uploadToken);
		$this->assertType('KalturaUploadToken', $resultUploadToken, 'Created upload token of wrong type');
		$this->assertNotNull($resultUploadToken->id, 'Created upload token without id');
		$this->assertEquals(KalturaUploadTokenStatus::PENDING, $resultUploadToken->status, 'Created upload token with wrong status');
		
		$resource->token = $resultUploadToken->id;
		
		$resultEntry = $this->client->media->addContent($resultEntry->id, $resource);
		$this->assertType('KalturaMediaEntry', $resultEntry, 'Ingested entry of wrong type');
		$this->assertEquals(KalturaEntryStatus::IMPORT, $resultEntry->status, "Ingested entry of wrong status [{$resultEntry->status}] entry id [{$resultEntry->id}]");
		
		$resultUploadToken = $this->client->uploadToken->upload($resource->token, $file);
		$this->assertType('KalturaUploadToken', $resultUploadToken, 'Uploaded upload token of wrong type');
		$this->assertNotNull($resultUploadToken->id, 'Uploaded upload token without id');
		$this->assertEquals(KalturaUploadTokenStatus::CLOSED, $resultUploadToken->status, 'Uploaded upload token with wrong status');
		
		$resultEntry = $this->client->media->get($resultEntry->id);
		$this->assertType('KalturaMediaEntry', $resultEntry, 'Retrieved entry of wrong type');
		$this->assertEquals(KalturaEntryStatus::PRECONVERT, $resultEntry->status, "Retrieved entry of wrong status [{$resultEntry->status}] entry id [{$resultEntry->id}]");
	}
	
	public function testAddVideoAssetParamsResourceContainerSourceOnly()
	{
		$entry							= new KalturaMediaEntry();
		$entry->conversionProfileId		= 1197619;
		$entry->mediaType				= KalturaMediaType::VIDEO;
		$entry->name					= 'VideoAssetParamsResourceContainer SourceOnly';
		$entry->description				= 'Expected statuses: no content, converting, ready';
		$entry->categories				= self::getCategory();
		
		$resource							= new KalturaAssetParamsResourceContainer();
		$resource->assetParamsId			= 0;
		$resource->resource					= new KalturaAssetResource();
		$resource->resource->assetId		= '0_wpn7xktl';

		$resultEntry = $this->client->media->add($entry);
		$this->assertType('KalturaMediaEntry', $resultEntry, 'Created entry of wrong type');
		$this->assertNotNull($resultEntry->id, 'Created entry without id');
		$this->assertEquals(KalturaEntryStatus::NO_CONTENT, $resultEntry->status, 'Created entry with wrong status');
		
		$resultEntry = $this->client->media->addContent($resultEntry->id, $resource);
		$this->assertType('KalturaMediaEntry', $resultEntry, 'Ingested entry of wrong type');
		$this->assertEquals(KalturaEntryStatus::PRECONVERT, $resultEntry->status, "Ingested entry of wrong status [{$resultEntry->status}] entry id [{$resultEntry->id}]");
	}
	
	public function testAddVideoAssetsParamsResourceContainersSourceOnly()
	{
		$entry							= new KalturaMediaEntry();
		$entry->conversionProfileId		= 1197619;
		$entry->mediaType				= KalturaMediaType::VIDEO;
		$entry->name					= 'VideoAssetsParamsResourceContainers SourceOnly';
		$entry->description				= 'Expected statuses: no content, converting, ready';
		$entry->categories				= self::getCategory();
		
		$res1							= new KalturaAssetParamsResourceContainer();
		$res1->assetParamsId			= 0;
		$res1->resource					= new KalturaAssetResource();
		$res1->resource->assetId		= '0_wpn7xktl';
		
		$resource						= new KalturaAssetsParamsResourceContainers();
		$resource->resources			= array($res1);

		$resultEntry = $this->client->media->add($entry);
		$this->assertType('KalturaMediaEntry', $resultEntry, 'Created entry of wrong type');
		$this->assertNotNull($resultEntry->id, 'Created entry without id');
		$this->assertEquals(KalturaEntryStatus::NO_CONTENT, $resultEntry->status, 'Created entry with wrong status');
		
		$resultEntry = $this->client->media->addContent($resultEntry->id, $resource);
		$this->assertType('KalturaMediaEntry', $resultEntry, 'Ingested entry of wrong type');
		$this->assertEquals(KalturaEntryStatus::PRECONVERT, $resultEntry->status, "Ingested entry of wrong status [{$resultEntry->status}] entry id [{$resultEntry->id}]");
	}
	
	public function testAddVideoIngestedOK()
	{
		$entry							= new KalturaMediaEntry();
		$entry->conversionProfileId		= 1197621;
		$entry->mediaType				= KalturaMediaType::VIDEO;
		$entry->name					= 'Video Ingested OK';
		$entry->description				= 'Expected statuses: no content, ready';
		$entry->categories				= self::getCategory();
		
		$res1							= new KalturaAssetParamsResourceContainer();
		$res1->assetParamsId			= 0;
		$res1->resource					= new KalturaAssetResource();
		$res1->resource->assetId		= '0_wpn7xktl';
		
		$res2							= new KalturaAssetParamsResourceContainer();
		$res2->assetParamsId			= 3;
		$res2->resource					= new KalturaAssetResource();
		$res2->resource->assetId		= '0_f0mvzgb4';
		
		$resource						= new KalturaAssetsParamsResourceContainers();
		$resource->resources			= array($res1, $res2);

		$resultEntry = $this->client->media->add($entry);
		$this->assertType('KalturaMediaEntry', $resultEntry, 'Created entry of wrong type');
		$this->assertNotNull($resultEntry->id, 'Created entry without id');
		$this->assertEquals(KalturaEntryStatus::NO_CONTENT, $resultEntry->status, 'Created entry with wrong status');
		
		$resultEntry = $this->client->media->addContent($resultEntry->id, $resource);
		$this->assertType('KalturaMediaEntry', $resultEntry, 'Ingested entry of wrong type');
		$this->assertEquals(KalturaEntryStatus::PRECONVERT, $resultEntry->status, "Ingested entry of wrong status [{$resultEntry->status}] entry id [{$resultEntry->id}]");
	}
	
	public function testAddVideoIngestedMissingOptionalIngested()
	{
		$entry							= new KalturaMediaEntry();
		$entry->conversionProfileId		= 1197622;
		$entry->mediaType				= KalturaMediaType::VIDEO;
		$entry->name					= 'Video Ingested - Missing Optional Ingested';
		$entry->description				= 'Expected statuses: no content, convert, ready';
		$entry->categories				= self::getCategory();
		
		$res1							= new KalturaAssetParamsResourceContainer();
		$res1->assetParamsId			= 0;
		$res1->resource					= new KalturaAssetResource();
		$res1->resource->assetId		= '0_wpn7xktl';
		
		$res2							= new KalturaAssetParamsResourceContainer();
		$res2->assetParamsId			= 3;
		$res2->resource					= new KalturaAssetResource();
		$res2->resource->assetId		= '0_f0mvzgb4';
		
		$resource						= new KalturaAssetsParamsResourceContainers();
		$resource->resources			= array($res1, $res2);

		$resultEntry = $this->client->media->add($entry);
		$this->assertType('KalturaMediaEntry', $resultEntry, 'Created entry of wrong type');
		$this->assertNotNull($resultEntry->id, 'Created entry without id');
		$this->assertEquals(KalturaEntryStatus::NO_CONTENT, $resultEntry->status, 'Created entry with wrong status');
		
		$resultEntry = $this->client->media->addContent($resultEntry->id, $resource);
		$this->assertType('KalturaMediaEntry', $resultEntry, 'Ingested entry of wrong type');
		$this->assertEquals(KalturaEntryStatus::PRECONVERT, $resultEntry->status, "Ingested entry of wrong status [{$resultEntry->status}] entry id [{$resultEntry->id}]");
	}
	
	public function testAddVideoIngestedMissingRequiredIngested()
	{
		$entry							= new KalturaMediaEntry();
		$entry->conversionProfileId		= 1197622;
		$entry->mediaType				= KalturaMediaType::VIDEO;
		$entry->name					= 'Video Ingested - Missing Required Ingested';
		$entry->description				= 'Expected statuses: no content, converting';
		$entry->categories				= self::getCategory();
		
		$res1							= new KalturaAssetParamsResourceContainer();
		$res1->assetParamsId			= 0;
		$res1->resource					= new KalturaAssetResource();
		$res1->resource->assetId		= '0_wpn7xktl';
		
		$resource						= new KalturaAssetsParamsResourceContainers();
		$resource->resources			= array($res1);

		$resultEntry = $this->client->media->add($entry);
		$this->assertType('KalturaMediaEntry', $resultEntry, 'Created entry of wrong type');
		$this->assertNotNull($resultEntry->id, 'Created entry without id');
		$this->assertEquals(KalturaEntryStatus::NO_CONTENT, $resultEntry->status, 'Created entry with wrong status');
		
		$resultEntry = $this->client->media->addContent($resultEntry->id, $resource);
		$this->assertType('KalturaMediaEntry', $resultEntry, 'Ingested entry of wrong type');
		$this->assertEquals(KalturaEntryStatus::PRECONVERT, $resultEntry->status, "Ingested entry of wrong status [{$resultEntry->status}] entry id [{$resultEntry->id}]");
	}
	
	public function testAddVideoIngestedAllIngestedIngested()
	{
		$entry							= new KalturaMediaEntry();
		$entry->conversionProfileId		= 1197622;
		$entry->mediaType				= KalturaMediaType::VIDEO;
		$entry->name					= 'Video Ingested - All Ingested Ingested';
		$entry->description				= 'Expected statuses: no content, converting, ready';
		$entry->categories				= self::getCategory();
		
		$res1							= new KalturaAssetParamsResourceContainer();
		$res1->assetParamsId			= 0;
		$res1->resource					= new KalturaAssetResource();
		$res1->resource->assetId		= '0_wpn7xktl';
		
		$res2							= new KalturaAssetParamsResourceContainer();
		$res2->assetParamsId			= 3;
		$res2->resource					= new KalturaAssetResource();
		$res2->resource->assetId		= '0_f0mvzgb4';
		
		$res3							= new KalturaAssetParamsResourceContainer();
		$res3->assetParamsId			= 4;
		$res3->resource					= new KalturaAssetResource();
		$res3->resource->assetId		= '0_f0mvzgb4';
		
		$res4							= new KalturaAssetParamsResourceContainer();
		$res4->assetParamsId			= 5;
		$res4->resource					= new KalturaAssetResource();
		$res4->resource->assetId		= '0_f0mvzgb4';
		
		$resource						= new KalturaAssetsParamsResourceContainers();
		$resource->resources			= array($res1, $res2, $res3, $res4);

		$resultEntry = $this->client->media->add($entry);
		$this->assertType('KalturaMediaEntry', $resultEntry, 'Created entry of wrong type');
		$this->assertNotNull($resultEntry->id, 'Created entry without id');
		$this->assertEquals(KalturaEntryStatus::NO_CONTENT, $resultEntry->status, 'Created entry with wrong status');
		
		$resultEntry = $this->client->media->addContent($resultEntry->id, $resource);
		$this->assertType('KalturaMediaEntry', $resultEntry, 'Ingested entry of wrong type');
		$this->assertEquals(KalturaEntryStatus::PRECONVERT, $resultEntry->status, "Ingested entry of wrong status [{$resultEntry->status}] entry id [{$resultEntry->id}]");
	}
	
	public function testAddVideoIngestedAllIngestedImported()
	{
		$entry							= new KalturaMediaEntry();
		$entry->conversionProfileId		= 1197622;
		$entry->mediaType				= KalturaMediaType::VIDEO;
		$entry->name					= 'Video Ingested - All Ingested Imported';
		$entry->description				= 'Expected statuses: no content, import, converting, ready';
		$entry->categories				= self::getCategory();
		
		$res1							= new KalturaAssetParamsResourceContainer();
		$res1->assetParamsId			= 0;
		$res1->resource					= new KalturaUrlResource();
		$res1->resource->url			= 'http://sites.google.com/site/demokmc/Home/spot.whats.mov';
		
		$res2							= new KalturaAssetParamsResourceContainer();
		$res2->assetParamsId			= 3;
		$res2->resource					= new KalturaUrlResource();
		$res2->resource->url			= 'http://sites.google.com/site/demokmc/Home/spot.whats.mov';
		
		$res3							= new KalturaAssetParamsResourceContainer();
		$res3->assetParamsId			= 4;
		$res3->resource					= new KalturaUrlResource();
		$res3->resource->url			= 'http://sites.google.com/site/demokmc/Home/spot.whats.mov';
		
		$res4							= new KalturaAssetParamsResourceContainer();
		$res4->assetParamsId			= 5;
		$res4->resource					= new KalturaUrlResource();
		$res4->resource->url			= 'http://sites.google.com/site/demokmc/Home/spot.whats.mov';
		
		$resource						= new KalturaAssetsParamsResourceContainers();
		$resource->resources			= array($res1, $res2, $res3, $res4);

		$resultEntry = $this->client->media->add($entry);
		$this->assertType('KalturaMediaEntry', $resultEntry, 'Created entry of wrong type');
		$this->assertNotNull($resultEntry->id, 'Created entry without id');
		$this->assertEquals(KalturaEntryStatus::NO_CONTENT, $resultEntry->status, 'Created entry with wrong status');
		
		$resultEntry = $this->client->media->addContent($resultEntry->id, $resource);
		$this->assertType('KalturaMediaEntry', $resultEntry, 'Ingested entry of wrong type');
		$this->assertEquals(KalturaEntryStatus::IMPORT, $resultEntry->status, "Ingested entry of wrong status [{$resultEntry->status}] entry id [{$resultEntry->id}]");
	}
	
	public function testAddVideoIngestedAllIngestedRemote()
	{
		$entry								= new KalturaMediaEntry();
		$entry->conversionProfileId			= 1197622;
		$entry->mediaType					= KalturaMediaType::VIDEO;
		$entry->name						= 'Video Ingested - All Ingested Remote';
		$entry->description					= 'Expected statuses: no content, converting';
		$entry->categories					= self::getCategory();
		
		$res1								= new KalturaAssetParamsResourceContainer();
		$res1->assetParamsId				= 0;
		$res1->resource						= new KalturaRemoteStorageResource();
		$res1->resource->storageProfileId	= 92;
		$res1->resource->url				= 'http://sites.google.com/site/demokmc/Home/spot.whats.mov';
		
		$res2								= new KalturaAssetParamsResourceContainer();
		$res2->assetParamsId				= 3;
		$res2->resource						= new KalturaRemoteStorageResource();
		$res2->resource->storageProfileId	= 92;
		$res2->resource->url				= 'http://sites.google.com/site/demokmc/Home/spot.whats.mov';
		
		$res3								= new KalturaAssetParamsResourceContainer();
		$res3->assetParamsId				= 4;
		$res3->resource						= new KalturaRemoteStorageResource();
		$res3->resource->storageProfileId	= 92;
		$res3->resource->url				= 'http://sites.google.com/site/demokmc/Home/spot.whats.mov';
		
		$res4								= new KalturaAssetParamsResourceContainer();
		$res4->assetParamsId				= 5;
		$res4->resource						= new KalturaRemoteStorageResource();
		$res4->resource->storageProfileId	= 92;
		$res4->resource->url				= 'http://sites.google.com/site/demokmc/Home/spot.whats.mov';
		
		$resource						= new KalturaAssetsParamsResourceContainers();
		$resource->resources			= array($res1, $res2, $res3, $res4);

		$resultEntry = $this->client->media->add($entry);
		$this->assertType('KalturaMediaEntry', $resultEntry, 'Created entry of wrong type');
		$this->assertNotNull($resultEntry->id, 'Created entry without id');
		$this->assertEquals(KalturaEntryStatus::NO_CONTENT, $resultEntry->status, 'Created entry with wrong status');
		
		$resultEntry = $this->client->media->addContent($resultEntry->id, $resource);
		$this->assertType('KalturaMediaEntry', $resultEntry, 'Ingested entry of wrong type');
		$this->assertEquals(KalturaEntryStatus::IMPORT, $resultEntry->status, "Ingested entry of wrong status [{$resultEntry->status}] entry id [{$resultEntry->id}]");
	}
	
	public function testAddVideoIngestedAllIngested()
	{
		$entry							= new KalturaMediaEntry();
		$entry->conversionProfileId		= 1197622;
		$entry->mediaType				= KalturaMediaType::VIDEO;
		$entry->name					= 'Video Ingested - All Ingested';
		$entry->description				= 'Expected statuses: no content, converting, ready';
		$entry->categories				= self::getCategory();
		
		$res1							= new KalturaAssetParamsResourceContainer();
		$res1->assetParamsId			= 0;
		$res1->resource					= new KalturaAssetResource();
		$res1->resource->assetId		= '0_wpn7xktl';
		
		$res2							= new KalturaAssetParamsResourceContainer();
		$res2->assetParamsId			= 2;
		$res2->resource					= new KalturaAssetResource();
		$res2->resource->assetId		= '0_f0mvzgb4';
		
		$res3							= new KalturaAssetParamsResourceContainer();
		$res3->assetParamsId			= 3;
		$res3->resource					= new KalturaAssetResource();
		$res3->resource->assetId		= '0_f0mvzgb4';
		
		$res4							= new KalturaAssetParamsResourceContainer();
		$res4->assetParamsId			= 4;
		$res4->resource					= new KalturaAssetResource();
		$res4->resource->assetId		= '0_f0mvzgb4';
		
		$res5							= new KalturaAssetParamsResourceContainer();
		$res5->assetParamsId			= 5;
		$res5->resource					= new KalturaAssetResource();
		$res5->resource->assetId		= '0_f0mvzgb4';
		
		$resource						= new KalturaAssetsParamsResourceContainers();
		$resource->resources			= array($res1, $res2, $res3, $res4, $res5);

		$resultEntry = $this->client->media->add($entry);
		$this->assertType('KalturaMediaEntry', $resultEntry, 'Created entry of wrong type');
		$this->assertNotNull($resultEntry->id, 'Created entry without id');
		$this->assertEquals(KalturaEntryStatus::NO_CONTENT, $resultEntry->status, 'Created entry with wrong status');
		
		$resultEntry = $this->client->media->addContent($resultEntry->id, $resource);
		$this->assertType('KalturaMediaEntry', $resultEntry, 'Ingested entry of wrong type');
		$this->assertEquals(KalturaEntryStatus::PRECONVERT, $resultEntry->status, "Ingested entry of wrong status [{$resultEntry->status}] entry id [{$resultEntry->id}]");
	}
	
	public function testAddVideoIngestedMissingRequired()
	{
		$entry							= new KalturaMediaEntry();
		$entry->conversionProfileId		= 1197621;
		$entry->mediaType				= KalturaMediaType::VIDEO;
		$entry->name					= 'Video Ingested - Missing Required';
		$entry->description				= 'Expected statuses: no content, converting';
		$entry->categories				= self::getCategory();
		
		$res1							= new KalturaAssetParamsResourceContainer();
		$res1->assetParamsId			= 0;
		$res1->resource					= new KalturaAssetResource();
		$res1->resource->assetId		= '0_wpn7xktl';
		
		$resource						= new KalturaAssetsParamsResourceContainers();
		$resource->resources			= array($res1);

		$resultEntry = $this->client->media->add($entry);
		$this->assertType('KalturaMediaEntry', $resultEntry, 'Created entry of wrong type');
		$this->assertNotNull($resultEntry->id, 'Created entry without id');
		$this->assertEquals(KalturaEntryStatus::NO_CONTENT, $resultEntry->status, 'Created entry with wrong status');
		
		$resultEntry = $this->client->media->addContent($resultEntry->id, $resource);
		$this->assertType('KalturaMediaEntry', $resultEntry, 'Ingested entry of wrong type');
		$this->assertEquals(KalturaEntryStatus::PRECONVERT, $resultEntry->status, "Ingested entry of wrong status [{$resultEntry->status}] entry id [{$resultEntry->id}]");
	}
	
	public function testAddImageUrlResource()
	{
		$entry					= new KalturaMediaEntry();
		$entry->mediaType		= KalturaMediaType::IMAGE;
		$entry->name			= 'ImageUrlResource';
		$entry->description		= 'Expected statuses: no content, import, ready';
		$entry->categories		= self::getCategory();
		
		$resource				= new KalturaUrlResource();
		$resource->url			= 'http://cdnbakmi.kaltura.com/p/547921/sp/54792100/flvclipper/entry_id/1_g001t1ae/version/100000';

		$resultEntry = $this->client->media->add($entry);
		$this->assertType('KalturaMediaEntry', $resultEntry, 'Created entry of wrong type');
		$this->assertNotNull($resultEntry->id, 'Created entry without id');
		$this->assertEquals(KalturaEntryStatus::NO_CONTENT, $resultEntry->status, 'Created entry with wrong status');
		
		$resultEntry = $this->client->media->addContent($resultEntry->id, $resource);
		$this->assertType('KalturaMediaEntry', $resultEntry, 'Ingested entry of wrong type');
		$this->assertEquals(KalturaEntryStatus::READY, $resultEntry->status, "Ingested entry of wrong status [{$resultEntry->status}] entry id [{$resultEntry->id}]");
	}
	
	public function testAddImageRemoteStorageResource()
	{
		$entry							= new KalturaMediaEntry();
		$entry->mediaType				= KalturaMediaType::IMAGE;
		$entry->name					= 'ImageRemoteStorageResource';
		$entry->description				= 'Expected statuses: no content, ready';
		$entry->categories				= self::getCategory();
		
		$resource						= new KalturaRemoteStorageResource();
		$resource->url					= 'http://cdnbakmi.kaltura.com/p/547921/sp/54792100/flvclipper/entry_id/1_g001t1ae/version/100000';
		$resource->storageProfileId		= 92;

		$resultEntry = $this->client->media->add($entry);
		$this->assertType('KalturaMediaEntry', $resultEntry, 'Created entry of wrong type');
		$this->assertNotNull($resultEntry->id, 'Created entry without id');
		$this->assertEquals(KalturaEntryStatus::NO_CONTENT, $resultEntry->status, 'Created entry with wrong status');
		
		$resultEntry = $this->client->media->addContent($resultEntry->id, $resource);
		$this->assertType('KalturaMediaEntry', $resultEntry, 'Ingested entry of wrong type');
		$this->assertEquals(KalturaEntryStatus::READY, $resultEntry->status, "Ingested entry of wrong status [{$resultEntry->status}] entry id [{$resultEntry->id}]");
	}
	
	public function testAddImageFileSyncResource()
	{
		$entry							= new KalturaMediaEntry();
		$entry->mediaType				= KalturaMediaType::IMAGE;
		$entry->name					= 'ImageFileSyncResource';
		$entry->description				= 'Expected statuses: no content, ready';
		$entry->categories				= self::getCategory();
		
		$resource						= new KalturaFileSyncResource();
		$resource->fileSyncObjectType	= 1;
		$resource->objectSubType		= 1;
		$resource->objectId				= '0_1fg0ps2v';

		$resultEntry = $this->client->media->add($entry);
		$this->assertType('KalturaMediaEntry', $resultEntry, 'Created entry of wrong type');
		$this->assertNotNull($resultEntry->id, 'Created entry without id');
		$this->assertEquals(KalturaEntryStatus::NO_CONTENT, $resultEntry->status, 'Created entry with wrong status');
		
		$resultEntry = $this->client->media->addContent($resultEntry->id, $resource);
		$this->assertType('KalturaMediaEntry', $resultEntry, 'Ingested entry of wrong type');
		$this->assertEquals(KalturaEntryStatus::READY, $resultEntry->status, "Ingested entry of wrong status [{$resultEntry->status}] entry id [{$resultEntry->id}]");
	}
	
	public function testAddImageLocalFileResource()
	{
		$entry							= new KalturaMediaEntry();
		$entry->mediaType				= KalturaMediaType::IMAGE;
		$entry->name					= 'ImageLocalFileResource';
		$entry->description				= 'Expected statuses: no content, ready';
		$entry->categories				= self::getCategory();
		
		$resource						= new KalturaLocalFileResource();
		$resource->localFilePath		= '/web/content/zbale/myTest.jpg';

		$resultEntry = $this->client->media->add($entry);
		$this->assertType('KalturaMediaEntry', $resultEntry, 'Created entry of wrong type');
		$this->assertNotNull($resultEntry->id, 'Created entry without id');
		$this->assertEquals(KalturaEntryStatus::NO_CONTENT, $resultEntry->status, 'Created entry with wrong status');
		
		$resultEntry = $this->client->media->addContent($resultEntry->id, $resource);
		$this->assertType('KalturaMediaEntry', $resultEntry, 'Ingested entry of wrong type');
		$this->assertEquals(KalturaEntryStatus::READY, $resultEntry->status, "Ingested entry of wrong status [{$resultEntry->status}] entry id [{$resultEntry->id}]");
	}
	
	public function testAddImageEntryResource()
	{
		$entry							= new KalturaMediaEntry();
		$entry->mediaType				= KalturaMediaType::IMAGE;
		$entry->name					= 'ImageEntryResource';
		$entry->description				= 'Expected statuses: no content, converting, ready';
		$entry->categories				= self::getCategory();
		
		$resource						= new KalturaEntryResource();
		$resource->entryId				= '0_1fg0ps2v';

		$resultEntry = $this->client->media->add($entry);
		$this->assertType('KalturaMediaEntry', $resultEntry, 'Created entry of wrong type');
		$this->assertNotNull($resultEntry->id, 'Created entry without id');
		$this->assertEquals(KalturaEntryStatus::NO_CONTENT, $resultEntry->status, 'Created entry with wrong status');
		
		$resultEntry = $this->client->media->addContent($resultEntry->id, $resource);
		$this->assertType('KalturaMediaEntry', $resultEntry, 'Ingested entry of wrong type');
		$this->assertEquals(KalturaEntryStatus::READY, $resultEntry->status, "Ingested entry of wrong status [{$resultEntry->status}] entry id [{$resultEntry->id}]");
	}
	
	public function testAddImageUploadedFileTokenResource()
	{
		$file							= 'C:\Documents and Settings\Tan-Tan\My Documents\My Pictures\1_1v.jpg';
		
		$entry							= new KalturaMediaEntry();
		$entry->mediaType				= KalturaMediaType::IMAGE;
		$entry->name					= 'ImageUploadedFileTokenResource';
		$entry->description				= 'Expected statuses: no content, importing, ready';
		$entry->categories				= self::getCategory();
		
		$uploadToken					= new KalturaUploadToken();
		$uploadToken->fileName			= basename($file);
		$uploadToken->fileSize			= filesize($file);
		
		$resource						= new KalturaUploadedFileTokenResource();

		$resultEntry = $this->client->media->add($entry);
		$this->assertType('KalturaMediaEntry', $resultEntry, 'Created entry of wrong type');
		$this->assertNotNull($resultEntry->id, 'Created entry without id');
		$this->assertEquals(KalturaEntryStatus::NO_CONTENT, $resultEntry->status, 'Created entry with wrong status');
		
		$resultUploadToken = $this->client->uploadToken->add($uploadToken);
		$this->assertType('KalturaUploadToken', $resultUploadToken, 'Created upload token of wrong type');
		$this->assertNotNull($resultUploadToken->id, 'Created upload token without id');
		$this->assertEquals(KalturaUploadTokenStatus::PENDING, $resultUploadToken->status, 'Created upload token with wrong status');
		
		$resource->token = $resultUploadToken->id;
		
		$resultEntry = $this->client->media->addContent($resultEntry->id, $resource);
		$this->assertType('KalturaMediaEntry', $resultEntry, 'Ingested entry of wrong type');
		$this->assertEquals(KalturaEntryStatus::IMPORT, $resultEntry->status, "Ingested entry of wrong status [{$resultEntry->status}] entry id [{$resultEntry->id}]");
		
		$resultUploadToken = $this->client->uploadToken->upload($resource->token, $file);
		$this->assertType('KalturaUploadToken', $resultUploadToken, 'Uploaded upload token of wrong type');
		$this->assertNotNull($resultUploadToken->id, 'Uploaded upload token without id');
		$this->assertEquals(KalturaUploadTokenStatus::CLOSED, $resultUploadToken->status, 'Uploaded upload token with wrong status');
		
		$resultEntry = $this->client->media->get($resultEntry->id);
		$this->assertType('KalturaMediaEntry', $resultEntry, 'Retrieved entry of wrong type');
		$this->assertEquals(KalturaEntryStatus::READY, $resultEntry->status, 'Retrieved entry of wrong status');
	}

	public function testAddVideoUploadedFileTokenResourceNoConvert()
	{
		$file							= 'C:\Documents and Settings\Tan-Tan\My Documents\My Videos\sample.1.flv';
		
		$entry							= new KalturaMediaEntry();
		$entry->conversionProfileId		= -1;
		$entry->mediaType				= KalturaMediaType::VIDEO;
		$entry->name					= 'VideoUploadedFileTokenResource - No Convert';
		$entry->description				= 'Expected statuses: no content, importing, pending';
		$entry->categories				= self::getCategory();
		
		$uploadToken					= new KalturaUploadToken();
		$uploadToken->fileName			= basename($file);
		$uploadToken->fileSize			= filesize($file);
		
		$resource						= new KalturaUploadedFileTokenResource();

		$resultEntry = $this->client->media->add($entry);
		$this->assertType('KalturaMediaEntry', $resultEntry, 'Created entry of wrong type');
		$this->assertNotNull($resultEntry->id, 'Created entry without id');
		$this->assertEquals(KalturaEntryStatus::NO_CONTENT, $resultEntry->status, 'Created entry with wrong status');
		
		$resultUploadToken = $this->client->uploadToken->add($uploadToken);
		$this->assertType('KalturaUploadToken', $resultUploadToken, 'Created upload token of wrong type');
		$this->assertNotNull($resultUploadToken->id, 'Created upload token without id');
		$this->assertEquals(KalturaUploadTokenStatus::PENDING, $resultUploadToken->status, "Created upload token with wrong status [{$resultUploadToken->status}]");
		
		$resource->token = $resultUploadToken->id;
		
		$resultEntry = $this->client->media->addContent($resultEntry->id, $resource);
		$this->assertType('KalturaMediaEntry', $resultEntry, 'Ingested entry of wrong type');
		$this->assertEquals(KalturaEntryStatus::IMPORT, $resultEntry->status, "Ingested entry of wrong status [{$resultEntry->status}] entry id [{$resultEntry->id}]");
		
		$resultUploadToken = $this->client->uploadToken->upload($resource->token, $file);
		$this->assertType('KalturaUploadToken', $resultUploadToken, 'Uploaded upload token of wrong type');
		$this->assertNotNull($resultUploadToken->id, 'Uploaded upload token without id');
		$this->assertEquals(KalturaUploadTokenStatus::CLOSED, $resultUploadToken->status, "Uploaded upload token with wrong status [{$resultUploadToken->status}]");
		
		$resultEntry = $this->client->media->get($resultEntry->id);
		$this->assertType('KalturaMediaEntry', $resultEntry, 'Retrieved entry of wrong type');
		$this->assertEquals(KalturaEntryStatus::PENDING, $resultEntry->status, "Retrieved entry of wrong status [{$resultEntry->status}] entry id [{$resultEntry->id}]");
	}	
	
	public function testAddVideoFlavorAsset()
	{
		$entry							= new KalturaMediaEntry();
		$entry->conversionProfileId		= 1197622;
		$entry->mediaType				= KalturaMediaType::VIDEO;
		$entry->name					= 'Video Ingested - All Ingested Ingested';
		$entry->description				= 'Expected statuses: no content, converting, ready';
		$entry->categories				= self::getCategory();
		
		$resultEntry = $this->client->media->add($entry);
		$this->assertType('KalturaMediaEntry', $resultEntry, 'Created entry of wrong type');
		$this->assertNotNull($resultEntry->id, 'Created entry without id');
		$this->assertEquals(KalturaEntryStatus::NO_CONTENT, $resultEntry->status, 'Created entry with wrong status');
		
		$flavorAsset = new KalturaFlavorAsset();
		$flavorAsset->flavorParamsId = 0;
		
		$resultFlavorAsset = $this->client->flavorAsset->add($resultEntry->id, $flavorAsset);
		$this->assertType('KalturaFlavorAsset', $resultFlavorAsset, 'Created flavor asset of wrong type');
		$this->assertNotNull($resultFlavorAsset->id, 'Created flavor asset without id');
		$this->assertEquals($resultFlavorAsset->isOriginal, true);
		$this->assertEquals(KalturaFlavorAssetStatus::QUEUED, $resultFlavorAsset->status, "Created flavor asset of wrong status [{$resultFlavorAsset->status}]");
	
		$contentResource				= new KalturaAssetResource();
		$contentResource->assetId		= '0_wpn7xktl';
		
		$resultFlavorAsset = $this->client->flavorAsset->setContent($resultFlavorAsset->id, $contentResource);
		$this->assertType('KalturaFlavorAsset', $resultFlavorAsset, 'Ingested flavor asset of wrong type');
		$this->assertNotNull($resultFlavorAsset->id, 'Ingested flavor asset without id');
		$this->assertEquals(KalturaFlavorAssetStatus::QUEUED, $resultFlavorAsset->status, "Ingested flavor asset of wrong status [{$resultFlavorAsset->status}]");
	
		$resultEntry = $this->client->media->get($resultEntry->id);
		$this->assertType('KalturaMediaEntry', $resultEntry, 'Ingested entry of wrong type');
		$this->assertNotNull($resultEntry->id, 'Ingested entry without id');
		$this->assertEquals(KalturaEntryStatus::PRECONVERT, $resultEntry->status, "Ingested entry with wrong status [{$resultEntry->status}] entry id [{$resultEntry->id}]");
		
		$flavorAsset = new KalturaFlavorAsset();
		$flavorAsset->flavorParamsId = 3;
		
		$resultFlavorAsset = $this->client->flavorAsset->add($resultEntry->id, $flavorAsset);
		$this->assertType('KalturaFlavorAsset', $resultFlavorAsset, 'Created flavor asset of wrong type');
		$this->assertNotNull($resultFlavorAsset->id, 'Created flavor asset without id');
		$this->assertEquals($resultFlavorAsset->isOriginal, false);
		$this->assertEquals(KalturaFlavorAssetStatus::QUEUED, $resultFlavorAsset->status, "Created flavor asset of wrong status [{$resultFlavorAsset->status}]");
	
		$contentResource				= new KalturaAssetResource();
		$contentResource->assetId		= '0_wpn7xktl';
		
		$resultFlavorAsset = $this->client->flavorAsset->setContent($resultFlavorAsset->id, $contentResource);
		$this->assertType('KalturaFlavorAsset', $resultFlavorAsset, 'Ingested flavor asset of wrong type');
		$this->assertNotNull($resultFlavorAsset->id, 'Ingested flavor asset without id');
		$this->assertEquals(KalturaFlavorAssetStatus::VALIDATING, $resultFlavorAsset->status, "Ingested flavor asset of wrong status [{$resultFlavorAsset->status}]");
	
		$resultEntry = $this->client->media->get($resultEntry->id);
		$this->assertType('KalturaMediaEntry', $resultEntry, 'Ingested entry of wrong type');
		$this->assertNotNull($resultEntry->id, 'Ingested entry without id');
		$this->assertEquals(KalturaEntryStatus::PRECONVERT, $resultEntry->status, "Ingested entry with wrong status [{$resultEntry->status}] entry id [{$resultEntry->id}]");
	}
	
	public function testAddVideoClip()
	{
		$sourceEntryId					= '0_hrq0ye5f';
		
		$entry							= new KalturaMediaEntry();
		$entry->mediaType				= KalturaMediaType::VIDEO;
		$entry->name					= 'VideoOperationClipResource';
		$entry->description				= 'Expected statuses: no content, converting';
		$entry->categories				= self::getCategory();
		
		$operation1						= new KalturaClipAttributes();
		$operation1->offset				= 2000;
		$operation1->duration			= 4000;
		
		$resource						= new KalturaOperationResource();
		$resource->resource				= new KalturaEntryResource();
		$resource->resource->entryId	= $sourceEntryId;
		$resource->operationAttributes	= array($operation1);

		$resultEntry = $this->client->media->add($entry);
		$this->assertType('KalturaMediaEntry', $resultEntry, 'Created entry of wrong type');
		$this->assertNotNull($resultEntry->id, 'Created entry without id');
		$this->assertEquals(KalturaEntryStatus::NO_CONTENT, $resultEntry->status, 'Created entry with wrong status');
		
		$resultEntry = $this->client->media->addContent($resultEntry->id, $resource);
		$this->assertType('KalturaMediaEntry', $resultEntry, 'Ingested entry of wrong type');
		$this->assertEquals(KalturaEntryStatus::PENDING, $resultEntry->status, "Ingested entry of wrong status [{$resultEntry->status}] entry id [{$resultEntry->id}]");
		$this->assertEquals($sourceEntryId, $resultEntry->rootEntryId, "Ingested entry with wrong root entry id [{$resultEntry->rootEntryId}] in entry id [{$resultEntry->id}]");
	}
	
	public function testAddVideoTrim()
	{
		$entry							= new KalturaMediaEntry();
		$entry->mediaType				= KalturaMediaType::VIDEO;
		$entry->name					= 'VideoOperationTrimResource';
		$entry->description				= 'Expected statuses: no content, ready';
		$entry->categories				= self::getCategory();
		
		$resource						= new KalturaWebcamTokenResource();
		$resource->token				= '191414FD-C27C-A713-C146-E83871C0EF91';

		$resultEntry = $this->client->media->add($entry);
		$this->assertType('KalturaMediaEntry', $resultEntry, 'Created entry of wrong type');
		$this->assertNotNull($resultEntry->id, 'Created entry without id');
		$this->assertEquals(KalturaEntryStatus::NO_CONTENT, $resultEntry->status, 'Created entry with wrong status');
		
		$resultEntry = $this->client->media->addContent($resultEntry->id, $resource);
		$this->assertType('KalturaMediaEntry', $resultEntry, 'Ingested entry of wrong type');
		$this->assertEquals(KalturaEntryStatus::READY, $resultEntry->status, "Ingested entry of wrong status [{$resultEntry->status}] entry id [{$resultEntry->id}]");
		
		// replace
		
		$operation1						= new KalturaClipAttributes();
		$operation1->offset				= 2000;
		$operation1->duration			= 4000;
		
		$resource						= new KalturaOperationResource();
		$resource->resource				= new KalturaEntryResource();
		$resource->resource->entryId	= $resultEntry->id;
		$resource->operationAttributes	= array($operation1);
		
		$resultEntry = $this->client->media->updateContent($resultEntry->id, $resource);
		$this->assertType('KalturaMediaEntry', $resultEntry, 'Replaced entry of wrong type');
		$this->assertEquals(KalturaEntryStatus::READY, $resultEntry->status, "Replaced entry of wrong status [{$resultEntry->status}] entry id [{$resultEntry->id}]");
		$this->assertNotNull($resultEntry->replacingEntryId, 'Replaced entry without replacing entry id');
		
		$tempEntry = $this->client->media->get($resultEntry->replacingEntryId);
		$this->assertType('KalturaMediaEntry', $tempEntry, 'Temp entry of wrong type');
		$this->assertEquals(KalturaEntryStatus::PENDING, $tempEntry->status, "Temp entry of wrong status [{$tempEntry->status}]");
		$this->assertEquals($resultEntry->id, $tempEntry->replacedEntryId, "Temp entry with wrong replaced id [{$tempEntry->replacedEntryId}]");
	}
}

