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
			self::$category = 'cat_' . date('d_m_H_i');
			
		return self::$category;
	}
	
	/**
	 * Tests media->add action
	 * @param KalturaMediaEntry $entry 
	 * @param KalturaResource $resource 
	 * @param KalturaMediaEntry $reference 
	 * @return int
	 * @dataProvider provideData
	 */
	public function testAdd(KalturaMediaEntry $entry, KalturaResource $resource = null, KalturaMediaEntry $reference = null)
	{
		$entry->categories = self::getCategory();
		
		$resultObject = $this->client->media->add($entry, $resource);
		$this->assertType('KalturaMediaEntry', $resultObject);
		$this->assertNotNull($resultObject->id);
		KalturaLog::debug("Created entry [$resultObject->id]");
		return $resultObject->id;
	}

	
	/**
	 * Tests media->add action
	 * @param KalturaMediaEntry $entry 
	 * @param string $fileData 
	 * @param KalturaMediaEntry $reference 
	 * @return int
	 * @dataProvider provideData
	 */
	public function testAddFromUpload(KalturaMediaEntry $entry, $fileData, KalturaMediaEntry $reference)
	{
		$resource = new KalturaUploadedFileTokenResource();
		$resource->token = $this->client->upload->upload($fileData);
		return $this->testAdd($entry, $resource, $reference);
	}
}
