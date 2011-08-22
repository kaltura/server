<?php

require_once (dirname(__FILE__) . '/../bootstrap/bootstrapServer.php');

class KalturaTestResultUpdater
{	
	
	/**
	 * 
	 * Returns the last index that the .old data files use (so we can cycle them)
	 * @param string $dataFilePath
	 * @return int - the last index
	 */
	public static function getLastDataFileIndex($dataFilePath)
	{
		$index = 0;
		while(file_exists($dataFilePath."old".$index))
		{
			$index++;
		}
		
		return $index;
	}
	
	
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
		KalturaLog::debug("dataFilePath [$dataFilePath], failuresFilePath [$failuresFilePath]\n");
		
		$oldFilesIndex = KalturaTestResultUpdater::getLastDataFileIndex($dataFilePath);
		copy($dataFilePath, $dataFilePath.".old".$oldFilesIndex);
		
		$oldTestDataFile = KalturaTestCaseDataFile::generateFromDataXml($dataFilePath);
		$testsFailures = KalturaTestCaseFailures::generateFromXml($failuresFilePath);
			
		$newTestDataFile = KalturaTestResultUpdater::update($oldTestDataFile, $testsFailures);
		$newTestDataDom = KalturaTestCaseDataFile::toXml($newTestDataFile);

		$newDataFile = fopen($dataFilePath, "w+");
		
		$newTestDataDom->formatOutput = true;
		
		KalturaLog::debug("Writing new test data file [" . $newTestDataDom->saveXML() . "]");
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
		//TODO: check this for no errors
		$newTestDataFile = unserialize(serialize($testDataFile));
				
		//For each test procedure failure
		foreach ($testCaseFailures->getTestProceduresFailures() as $testProcedureFailures)
		{
			//Get the test procedure name / key
			$testProcedureName = $testProcedureFailures->getTestProcedureName();
			KalturaLog::debug("testProcedureName [" . $testProcedureName . "]\n");
			
			foreach ($testProcedureFailures->getTestCaseInstanceFailures() as $testCaseInstanceFailure)
			{
				$testCaseInstanceKey = KalturaTestResultUpdater::getTestCaseInstnaceKey($newTestDataFile, $testCaseInstanceFailure);
				KalturaLog::debug("testCaseInstanceKey  [" . $testCaseInstanceKey . "]\n");
				//If key wasnt found skip the error
				if(is_null($testCaseInstanceKey))
				{
					KalturaLog::debug("Test case instance wasn't found [" . print_r($testCaseInstanceFailure, true) ."] ");
					KalturaLog::debug("Skipping failure!!!");
					continue;
				}

				//Update the values by its failures
				foreach($testCaseInstanceFailure->getFailures() as $failure)
				{
					$field = $failure->getField();
					$actualValue = $failure->getActualValue();
					
					$testProcedureData = $newTestDataFile->getTestProcedureData($testProcedureName);
	   				$testCaseInstanceData = $testProcedureData->getTestCaseData($testCaseInstanceKey);
	   				
					//Gets the first output reference
					$outputReferenceObject = $testCaseInstanceData->getOutputReference(0);
	  										
					//We update only the first output reference which is propel
					$outputReferenceObject->setByName($field, $actualValue);
					
					//If there are no dbValues yet we create the first ones
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
	 * Gets the test case instance data key
	 * First by name and if name don't exists then by the test case inputs
	 * @param KalturaTestCaseDataFile $newTestDataFile
	 * @param KalturaTestCaseInstanceFailure $testCaseInstanceFailure
	 */
	protected static function getTestCaseInstnaceKey(KalturaTestCaseDataFile $newTestDataFile, KalturaTestCaseInstanceFailure $testCaseInstanceFailure)
	{
		$testCaseInstanceKey = $testCaseInstanceFailure->getTestCaseInstanceName();
		
		//Check if key exists in the data file
		if(!$newTestDataFile->isTestCaseInstanceExists($testCaseInstanceKey))
		{
			//If name doesn't exists we find the right input 
			//TODO: maybe remove getTestKeyByInputs
			$testCaseInstanceKey = KalturaTestResultUpdater::getTestKeyByInputs($newTestDataFile, $testCaseInstanceFailure->getTestCaseInput());
		}
		
		return $testCaseInstanceKey;
	}
	
	/**
	 * TODO: check if needed
	 *  Finds the right test data by its inputs (id and type)
	 * @param KalturaTestCaseDataFile $unitTestDataFile
	 * @param array $failuresInputs
	 * @return int - the KalturaTestCaseDataFile key or null for if non found
	 */
	protected static function getTestKeyByInputs(KalturaTestCaseDataFile $unitTestDataFile, array $failuresInputs)
	{
		$testKey = null;
		 
		foreach ($unitTestDataFile->getTestProceduresData() as $testProcedureKey => $testProcedureData)
		{
			foreach ($testProcedureData->getTestCasesData() as $key => $unitTestData)
			{
				$isAllInputsFound = KalturaTestResultUpdater::isAllFound($unitTestData->getInput(), $failuresInputs);
				$isAllOutputReferencesFound = KalturaTestResultUpdater::isAllFound($unitTestData->getOutputReferences(), $failuresInputs);
								
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