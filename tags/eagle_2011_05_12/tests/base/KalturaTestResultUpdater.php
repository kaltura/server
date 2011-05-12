<?php

require_once (dirname(__FILE__) . '/../bootstrap/bootstrapServer.php');

class KalturaTestResultUpdater
{	
	/**
	 * 
	 * Update the failures into the data so in the next run no failures will accure
	 * Should be use with extreme care after completly reviewing the failure file  
	 * @param unknown_type $dataFile
	 * @param unknown_type $failuresFile
	 */
	public static function UpdateResults($dataFilePath, $failuresFilePath)
	{
		//Returns the inputs for the unit tests
		$oldTestDataFile = KalturaTestCaseDataFile::generateFromDataXml($dataFilePath);
		$testsFailures = KalturaTestCaseFailures::generateFromXml($failuresFilePath);
			
		$newTestDataFile = KalturaTestResultUpdater::update($oldTestDataFile, $testsFailures);
		$newTestDataDom = KalturaTestCaseDataFile::toXml($newTestDataFile);

		$newDataFile = fopen($dataFilePath . ".new", "w+");
		$newTestDataDom->formatOutput = true;
		fwrite($newDataFile, $newTestDataDom->saveXML());
	}

	/**
	 * 
	 * Update the data with the given failure
	 * @param KalturaTestCaseDataFile $unitTestData
	 * @param KalturaTestCaseFailures $testsFailures
	 * @return KalturaTestCaseDataFile - The new unitTestDataFile with the changes
	 */
	private static function update(KalturaTestCaseDataFile $testDataFile, KalturaTestCaseFailures $testCaseFailures)
	{
		//Maybe a bug because of shallow copy, but currently we don't need the old object after the copy
		//So bug can't be checked
		$newTestDataFile = clone($testDataFile);
		
		//For each test procedure failure
		foreach ($testCaseFailures->getTestProceduresFailures() as $testProcedureFailures)
		{
			//Get the test procedure name / key
			$testProcedureKey = $testProcedureFailures->getTestProcedureName();
			
			foreach ($testProcedureFailures->getTestCaseInstanceFailures() as $testCaseInstanceFailure)
			{
				//Find the right input
				$testDataKey = KalturaTestResultUpdater::getTestKeyByInputs($newTestDataFile, $testCaseInstanceFailure->getTestCaseInput());
				
				//if key wasnt found skip the error
				if(is_null($testDataKey))
				{
					print("\nTest inputs were not found:\n");
					var_dump($testCaseInstanceFailure->getTestCaseInput());
					print("\nSkipping failure!!!\n");
					continue;
				}
			
				//Update the values by its failures
				foreach($testCaseInstanceFailure->getFailures() as $failure)
				{
					$field = $failure->getField();
					
					$actualValue = $failure->getActualValue();
					
					$testProceduresData = $newTestDataFile->getTestProceduresData();
					$testProcedureData = $testProceduresData[$testProcedureKey];
					
					$testCaseInstancesData = $testProcedureData->getTestCasesData();
					$testCaseInstanceData = $testCaseInstancesData[$testDataKey];
					$testCaseInstanceOutputReference = $testCaseInstanceData->getOutputReference();
					
					$outputReferenceObject =  $testCaseInstanceOutputReference[0];
						
					//We update only the first output reference which is propel
					$outputReferenceObject->setByName($field, $actualValue);
					
					//if there are no dbValues yet we create the first ones
					$oldComments = $outputReferenceObject->getComments();
					
					if(!isset($oldComments[$field]))
					{
						$outputReferenceObject->addComment($field ,$failure->getOutputReferenceValue()); 
					}
				}	
			}
		}
		return $newTestDataFile;
	}
	
	/**
	 * 
	 *  Finds the right test data by its inputs (id and type)
	 * @param KalturaTestCaseDataFile $unitTestDataFile
	 * @param array $failuresInputs
	 * @return int - the KalturaTestCaseDataFile key or null for if non found
	 */
	private static function getTestKeyByInputs(KalturaTestCaseDataFile $unitTestDataFile, array $failuresInputs)
	{
		$testKey = null;
		 
		foreach ($unitTestDataFile->getTestProceduresData() as $testProcedureKey => $testProcedureData)
		{
			foreach ($testProcedureData->getTestCasesData() as $key => $unitTestData)
			{
				$isAllInputsFound = KalturaTestResultUpdater::isAllFound($unitTestData->getInput(), $failuresInputs);
				$isAllOutputReferencesFound = KalturaTestResultUpdater::isAllFound($unitTestData->getOutputReference(), $failuresInputs);
								
				if($isAllInputsFound && $isAllOutputReferencesFound)
				{
					$testKey = $key;
					break; 
				}
			}
			
			//If key is found then skip
			if(isset($testKey))
			{
				break;
			}
		}
		
		return $testKey;
	}
	
	/**
	 * 
	 *  Checks that all the unitTetsDataObjects given are foudn at the failureInputs array
	 * @param array $unitTestDataObjects
	 * @param array $failuresInputs
	 * @return bool - is the entire array found
	 */
	private static function isAllFound(array $unitTestDataObjects, array $failuresInputs)
	{
		$isAllInputsFound = true;
		
		foreach ($unitTestDataObjects as $unitTestDataObject)
		{
			$originalObjectId = $unitTestDataObject->getId();
			$originalObjectType = $unitTestDataObject->getType();
			
			$inputFound = false;
			
			foreach ($failuresInputs as $failureInput)
			{
				$failureObjectType = $failureInput['type'];
				$failureObjectId =  $failureInput[ $failureObjectType . 'Id'];
										
				if($failureObjectType == $originalObjectType && $failureObjectId == $originalObjectId)
				{
					$inputFound = true;
				}
			}
			
			//if the current object wasn't found so we move to the next unit testData
			if(!$inputFound)
			{
				$isAllInputsFound = false; 
				break;
			}
		}
		
		return $isAllInputsFound;
	}
}