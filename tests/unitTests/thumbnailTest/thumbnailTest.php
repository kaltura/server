<?php

require_once(dirname(__FILE__) . '/../../bootstrap/bootstrapApi.php');

/**
 * The KMC sanity test case
 * tests if decision layer makes a right decision about converting and validating files 
 * @author Roni
 *
 * TODO: change the file name to be caps
 */
class thumbnailTest extends KalturaApiTestCase
{
	/**
	 * 
	 * Creates a new KMC Test case
	 * @param string $name
	 * @param array<unknown_type> $data
	 * @param string $dataName
	 */
	public function __construct($name = "thumbnailTest", array $data = array(), $dataName ="Default data")
	{
		parent::__construct($name, $data, $dataName);
	}
	
	/**
	 * 
	 * Test the KMC Checks that the starting calls return okay
	 * @param array<unknown_type> $params
	 * @param array<unknown_type> $results
	 * @dataProvider provideData
	 */
	public function testCreateEntryForThumbAsset($entryId, $result)
	{		
		$filter = new KalturaAssetFilter();
		$filter->entryIdEqual = $entryId;
		$results = $this->client->thumbAsset->listAction($filter, null);
		
		$assetUrl = $this->client->thumbAsset->getUrl($results->objects[0]->id, null);
		$filePath = dirname(__FILE__) . 'data/new_thumb_asset.jpg';
		self::saveApiFileFromUrl($assetUrl, $filePath);
		
		
		$tmpFile = tempnam(dirname(__FILE__), 'imageComperingTmp');
		$convert = dirname(kConf::get('bin_path_imagemagick')) . '\compare';
		$cmd = $convert . ' ' . $filePath . ' ' . $result . ' ' . $tmpFile . ' 2>resultLog.txt';		
		$retValue = null;
		$output = null;
		$output = system($cmd, $retValue);
		
		@unlink($tmpFile);			// delete tmp comparing file (used to copmpare the two image files)
		@unlink("resultLog.txt");	// delete tmp log file that was used to retrieve compare return value
		
		if ($retValue != 0)
			$this->fail('Files are not equal [' . $filePath . '] [' . $result . ']' . Compare return value was' . $retValue);
		
		
	}
}