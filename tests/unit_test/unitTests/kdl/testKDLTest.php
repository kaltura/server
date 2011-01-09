<?php

try{
	require_once(dirname(__FILE__) . '/../../bootstrap.php');
}
catch (Exception $e)
{
}
	/**
	 * The KDl unit test case
	 * tests if decision layer makes a right decision about converting and validating files 
	 * @author Roni
	 *
	 */
	class KDLUnitTest extends UnitTestBase
	{
		/**
		 * 
		 * Test the KDL - WrapCDLGenerateTargetFlavors() method
		 * @dataProvider providerCDLGenerateTargetFlavors
		 */
		public function testKDLWrapCDLGenerateTargetFlavors(flavorParams $flavorList, mediaInfo $mediaInfo, flavorParamsOutput $flavorParamsOutput)
		{
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
		 * @dataProvider providerCDLValidateProduct
		 */
		public function testKDLWrapCDLValidateProduct(mediaInfo $cdlSourceMediaInfo, flavorParamsOutput $cdlTarget, mediaInfo $cdlProductMediaInfo)
		{ 
			$result = KDLWrap::CDLValidateProduct($cdlSourceMediaInfo, $cdlTarget, $cdlProductMediaInfo);

			//assert that 0 errors were generated
			$this->assertEquals(0, count($result->_errors));
		}
				
		/**
		 * 
		 * The unit test data provider gets the data for the test "testKDLWrapCDLGenerateTargetFlavors"
		 * @return array<array>();
		 */
		public function providerCDLGenerateTargetFlavors()
		{
			$inputsAsUnitTestObjects = parent::provider(dirname(__FILE__) . "/testsData/RealTest1.Data");
			
			//The actual input for the tests
			$inputsForTest = array();
			
			foreach ($inputsAsUnitTestObjects as $input)
			{
				$testParameters = array();
				foreach ($input as $singleParameter)
				{
					 $testParameters[] = $singleParameter->dataObject;
				}
				
				$inputsForTest[] = $testParameters;
			}

//			$this->errorFile = fopen(dirname(__FILE__) .  "/testsData/KDLUnitTest.result", "w+");
			
			return $inputsForTest;
		}
	
		/**
		 * 
		 * The unit test data provider gets the data for the test "testKDLWrapCDLValidateProduct"
		 */
		public function providerCDLValidateProduct()
		{
			$inputsAsUnitTestObjects = parent::provider(dirname(__FILE__) . "/testsData/RealTest2.Data");
			
			//The actual input for the tests
			$inputsForTest = array();
			
			foreach ($inputsAsUnitTestObjects as $input)
			{
				$testParameters = array();
				foreach ($input as $singleParameter)
				{
					 $testParameters[] = $singleParameter->dataObject;
				}
				
				$inputsForTest[] = $testParameters;
			}
			
			return $inputsForTest; 
		}
	}