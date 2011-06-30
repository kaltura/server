<?php

require_once(dirname(__FILE__) . '/../../bootstrap/bootstrapServer.php');

/**
 * The KDl unit test case
 * tests if decision layer makes a right decision about converting and validating files 
 * @author Roni
 *
 */
class KDLTest extends KalturaServerTestCase
{
	/**
	 * 
	 * Creates a new KDL Test case
	 * @param string $name
	 * @param array<unknown_type> $data
	 * @param string $dataName
	 */
	public function __construct($name = "KDLTest", array $data = array(), $dataName ="Default data")
	{
		parent::__construct($name, $data, $dataName);
	}
	
	/**
	 * 
	 * Test the KDL - WrapCDLGenerateTargetFlavors() method
	 * @dataProvider provideData
	 */
	public function testKDLWrapCDLGenerateTargetFlavors(flavorParams $flavorList, mediaInfo $mediaInfo, flavorParamsOutput $flavorParamsOutput)
	{
		print("\nin KDL TEst\n");
		print_r($flavorList, true);
		print_r($mediaInfo, true);
		print_r($flavorParamsOutput, true);
		//returns KDLWrap
		$result = KDLWrap::CDLGenerateTargetFlavors($mediaInfo, array($flavorList));

		$validErrorFields = array("CreatedAt", "UpdatedAt", "Id", "PartnerId", "EntryId", "FlavorAssetId", "DeletedAt", "ReadyBehavior", "FlavorAssetVersion", "FlavorParamsVersion", "AudioResolution");
		
		$newErrors = $this->comparePropelObjectsByFields($flavorParamsOutput, reset($result->_targetList), $validErrorFields);
	}
	
	/**
	 * 
	 * Tests the KDLWrapCDLValidateProduct method
	 * @param mediaInfo $cdlSourceMediaInfo
	 * @param flavorParamsOutput $cdlTarget
	 * @param mediaInfo $cdlProductMediaInfo
	 * @dataProvider provideData
	 */
	public function testKDLWrapCDLValidateProduct(mediaInfo $cdlSourceMediaInfo, flavorParamsOutput $cdlTarget, mediaInfo $cdlProductMediaInfo)
	{ 
		$result = KDLWrap::CDLValidateProduct($cdlSourceMediaInfo, $cdlTarget, $cdlProductMediaInfo);

		//assert that 0 errors were generated
		$this->assertEquals(0, count($result->_errors));
	}

//	/**
//	 * 
//	 * Returns the KalturaTestSuite for the test
//	 */
//	public function suite()
//	{
//		return new KalturaTestSuite("KDLTest");
//	}
}