<?php

	require_once ('../../bootstrap.php');
			
	/**
	 * The KDl unit test case
	 * tests if decision layer makes a right decision about converting and validating files 
	 * @author Roni
	 *
	 */
	class DLUnitTest extends unitTestBase
	{
		/**
		 * 
		 * Test the KDL - WrapCDLGenerateTargetFlavors() method
		 * @dataProvider providerCDLGenerateTargetFlavors
		 */
		public function testKDLWrapCDLGenerateTargetFlavors($flavorList, $mediaInfo, $flavorParamsOutput)
		{
			
			//returns KDLWrap
			$result = KDLWrap::CDLGenerateTargetFlavors($mediaInfo, array($flavorList));

			$this->validErrorFields = array("CreatedAt", "UpdatedAt", "Id", "PartnerId", "EntryId", "FlavorAssetId", "DeletedAt");
			
			$isEqual = parent::comparePropelObjectsByFeilds($flavorParamsOutput, reset($result->_targetList));
		
			$this->assertEquals(true, $isEqual);
			
				

			//TODO: write the error list to the file. create an error reporter that gets the string and the path or something
//			$fhandle = fopen("c:/Users/Roni/.hudson/jobs/");
//			fwrite($fhandle, PHPUnit_Framework_TestResult);
		}
		
		/**
		 * 
		 * Tests the KDLWrapCDLValidateProduct method
		 * @param mediaInfo $cdlSourceMediaInfo
		 * @param flavorParamsOutput $cdlTarget
		 * @param mediaInfo $cdlProductMediaInfo
		 * @dataProvider providerCDLValidateProduct
		 */
		public function testKDLWrapCDLValidateProduct(mediaInfo $cdlSourceMediaInfo, flavorParamsOutput $cdlTarget, mediaInfo $cdlProductMediaInfo)
		{ 
			$result = KDLWrap::CDLValidateProduct($cdlSourceMediaInfo, $cdlTarget, $cdlProductMediaInfo);

			//assert that no errors were generated
			$this->assertEquals(null, $result->errors);
		}
		
		/**
		 * 
		 * The unit test data provider gets the data for the test "testKDLWrapCDLGenerateTargetFlavors"
		 * @return array<array>();
		 */
		public function providerCDLGenerateTargetFlavors()
		{
			//TODO: from where to get the data file path.
			$inputs = parent::provider("C:/opt/kaltura/app/tests/unit_test/unitTests/KDL/tests_data/Test1.Data");
			return $inputs; 
		}
	
		/**
		 * 
		 * The unit test data provider gets the data for the test "testKDLWrapCDLValidateProduct"
		 */
		public function providerCDLValidateProduct()
		{
			$inputs = parent::provider("C:/opt/kaltura/app/tests/unit_test/unitTests/KDL/tests_data/Test2.Data");
			return $inputs;
		}
		
	}			

?>