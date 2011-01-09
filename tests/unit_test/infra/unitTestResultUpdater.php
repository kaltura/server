<?php

require_once (dirname(__FILE__) . '/../bootstrap.php');

class unitTestResultUpdater
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
		$unitTestDataFile = unitTestDataFile::generateFromDataXml($dataFilePath);
				
		$testsFailures = testsFailures::generateFromXml($failuresFilePath);
			
		$newUnitTestData = unitTestResultUpdater::update($unitTestDataFile, $testsFailures);
		
		$newDataFile = fopen($dataFilePath . ".new", "w+");
		
		fwrite($newDataFile, $newUnitTestData->toDataXml());
	}
		
	/**
	 * 
	 * Update the data with the given failure
	 * @param unitTestDataFile $unitTestData
	 * @param testsFailures $testsFailures
	 * @return unitTestDataFile - The new unitTestDataFile with the changes
	 */
	private static function update(unitTestDataFile $unitTestDataFile, testsFailures $testsFailures)
	{
		//Maybe a bug because of shallow copy, but currently we don't need the old object after the copy
		//So bug can't be checked
		$newUnitTestDataFile = clone($unitTestDataFile);
		
		//TODO: Change the new file according to the error file
		//For each failure
		foreach ($testsFailures->failures as $singleInputFailures)
		{
			//Find the right input
			$unitTestDataKey = unitTestResultUpdater::getTestKeyByInputs($newUnitTestDataFile, $singleInputFailures->inputs);
			
			//Update the values by its failures
			foreach ($singleInputFailures->failures as $failure)
			{
				$field = $failure->field;
				
				$actualValue = $failure->actualValue;
				
				$outputReferenceObject = $newUnitTestDataFile->unitTestsData[$unitTestDataKey]->outputReference[0];
				//We update only the first output reference which is propel
				$outputReferenceObject->setByName($field, $actualValue);
				
				//if there are no dbValues yet we create the first ones
				if(!isset($outputReferenceObject->comments[$field]))
				{
					$outputReferenceObject->comments[$field] = $failure->outputReferenceValue; 
				}
			}
		}
		
		return $newUnitTestDataFile;
	}
	
	/**
	 * 
	 *  Finds the right test data by its inputs (id and type)
	 * @param array $unitTestDataFile
	 * @param array $failuresInputs
	 * @return int - the unitTestData key or null for if non found
	 */
	private static function getTestKeyByInputs(unitTestDataFile $unitTestDataFile, array $failuresInputs)
	{
		$testKey = null;
		 
		foreach ($unitTestDataFile->unitTestsData as $key => $unitTestData)
		{
			$isFound = true;
			
			$isAllInputsFound = unitTestResultUpdater::isAllFound($unitTestData->input, $failuresInputs);
			$isAllOutputReferencesFound = unitTestResultUpdater::isAllFound($unitTestData->outputReference, $failuresInputs);
							
			if($isAllInputsFound && $isAllOutputReferencesFound)
			{
				$testKey = $key;
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