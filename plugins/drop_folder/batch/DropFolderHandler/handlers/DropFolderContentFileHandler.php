<?php


class DropFolderContentFileHandler extends DropFolderFileHandler
{	
	
	// if regexp includes (?P<referenceId>\w+) or (?P<flavorName>\w+), they will be translated to the parsedSlug and parsedFlavor
	
	const REFERENCE_ID_WILDCARD = 'referenceId';
	const FLAVOR_NAME_WILDCARD  = 'flavorName';
	
	const DEFAULT_SLUG_REGEX = '/(?P<referenceId>\w+)_(?P<flavorName>\w+)[.](?P<extension>\w+)/'; // matches "referenceId_flavorName.extension"
	
	
	/**
	 * @var KalturaDropFolderContentFileHandlerConfig
	 */
	protected $config;
	
	/**
	 * @var KalturaFlavorParams
	 */
	private $parsedFlavorObject;
	
	
	public function getType() {
		return DropFolderFileHandlerType::CONTENT;
	}
	
	

	public function handle()
	{
		// check prerequisites
		
		if (!$this->config) {
			KalturaLog::err('File handler configuration not defined');
			return false; // file not handled
		}
		
		if (!$this->kClient) {
			KalturaLog::err('Kaltura client not defined');
			return false; // file not handled
		}
		
		if (!$this->dropFolder) {
			KalturaLog::err('Drop folder not defined');
			return false; // file not handled
		}
		
		if (!$this->dropFolderFile) {
			KalturaLog::err('Drop folder file not defined');
			return false; // file not handled
		}
		
		
		// parse file name according to slugRegex and extract parsedSlug and parsedFlavor
		$regexMatch = $this->parseRegex();
		if (!$regexMatch) {
			$this->dropFolderFile->status = KalturaDropFolderFileStatus::ERROR_HANDLING;
			$this->dropFolderFile->errorDescription = 'File name ['.$this->dropFolderFile->fileName.'] does not match defined slug regex ['.$this->config->slugRegex.']';
			KalturaLog::err($this->dropFolderFile->errorDescription);
			$this->updateDropFolderFile(); // update errors tatus
			return false; // file not handled
		}
		
		// check if parsed flavor exists
		if (!is_null($this->dropFolderFile->parsedFlavor))
		{
			$this->parsedFlavorObject = $this->getFlavorBySystemName($this->dropFolderFile->parsedFlavor);
			if (!$this->parsedFlavorObject) {
				$this->dropFolderFile->status = KalturaDropFolderFileStatus::ERROR_HANDLING;
				$this->dropFolderFile->errorDescription = 'Parsed flavor system name ['.$this->dropFolderFile->parsedFlavor.'] could not be found';
				KalturaLog::err($this->dropFolderFile->errorDescription);
				$this->updateDropFolderFile(); // update errors tatus
				return false; // file not handled
			}
			
		}
		
		// handle file according to the defined policy
		switch ($this->config->contentMatchPolicy)
		{
			case KalturaDropFolderContentFileHandlerMatchPolicy::ADD_AS_NEW:
				$this->addAsNewContent();
				break;
			
			case KalturaDropFolderContentFileHandlerMatchPolicy::MATCH_EXISTING_OR_KEEP_IN_FOLDER:
				$this->addAsExistingContent();
				break;
				
			case KalturaDropFolderContentFileHandlerMatchPolicy::MATCH_EXISTING_OR_ADD_AS_NEW:
				$this->addAsExistingContent();
				if ($this->dropFolderFile->status === KalturaDropFolderFileStatus::NO_MATCH) {
					$this->addAsNewContent();
				}
				break;
		}

		// update file with all changes that were done during the handling process
		try {
			$this->updateDropFolderFile();
		}
		catch (Exception $e) {
			KalturaLog::err('Cannot update file - '.$e->getMessage());
			return false;			
		}
		
		return ($this->dropFolderFile->status === KalturaDropFolderFileStatus::HANDLED); // return true if handled, false otherwise
	}
	
	
	
	/**
	 * Parse file name according to defined slugRegex and set the extracted parsedSlug and parsedFlavor
	 * @return bool true if file name matches the slugRegex or false otherwise
	 */
	private function parseRegex()
	{
		$matches = null;
		$slugRegex = is_null($this->config->slugRegex) ? self::DEFAULT_SLUG_REGEX : $this->config->slugRegex;
		$matchFound = @preg_match($slugRegex, $this->dropFolderFile->fileName, $matches);
		
		if (!$matchFound) {
			return false; // file name does not match defined regex
		}
		
		$this->dropFolderFile->parsedSlug   = isset($matches[self::REFERENCE_ID_WILDCARD]) ? $matches[self::REFERENCE_ID_WILDCARD] : null;
		$this->dropFolderFile->parsedFlavor = isset($matches[self::FLAVOR_NAME_WILDCARD])  ? $matches[self::FLAVOR_NAME_WILDCARD]  : null;
			
		KalturaLog::debug('Parsed slug ['.$this->dropFolderFile->parsedSlug.'], Parsed flavor ['.$this->dropFolderFile->parsedFlavor.']');
		return true; // file name matches the defined regex
	}
	

	
	/**
	 * Update the status of all drop folder files with the given ids to be KalturaDropFolderFileStatus::HANDLED
	 * @param array $idsArray array of drop folder file ids
	 */
	private function setAsHandled($idsArray)
	{
		$updateObj = new KalturaDropFolderFile();
		$updateObj->status = KalturaDropFolderFileStatus::HANDLED;
		
		$this->kClient->startMultiRequest();
		foreach ($idsArray as $id)
		{
			KalturaLog::debug('Updating additional drop folder file ['.$id.'] with status HANDLED');
			$this->kClient->dropFolderFile->update($id, $updateObj);
		}
		$this->kClient->doMultiRequest();		
	}
	
	
	

	
	/**
	 * Add the new file to a new entry, together with all other relevant drop folder files, according to the ingestion profile
	 * 
	 * @return bool true if file was handled or false otherwise
	 */
	private function addAsNewContent()
	{ 
		$addionnalFileIds = null;
		$resource = null;
		
		if (is_null($this->dropFolderFile->parsedFlavor))
		{
			$resource = new KalturaDropFolderFileResource();
			$resource->dropFolderFileId = $this->dropFolderFile->id;
		}
		else
		{
			$resource = $this->getAllIngestedFiles();
			if (!$resource) {
				KalturaLog::debug('Some required flavors do not exist in the drop folder - changing status to WAITING');
				$this->dropFolderFile->status = KalturaDropFolderFileStatus::WAITING;
				return true;
			}
			$addionnalFileIds = array();
			foreach ($resource->resources as $assetContainer) {
				if ($assetContainer->resource->dropFolderFileId != $this->dropFolderFile->id) {
					$addionnalFileIds[] = $assetContainer->resource->dropFolderFileId;
				}
			}
		}
		
		$newEntry = new KalturaBaseEntry();
		$ingestionProfile = $this->getIngestionProfile();
		$newEntry->ingestionProfileId = $ingestionProfile->id;
		$newEntry->name = $this->dropFolderFile->parsedSlug;
		
		if (is_null($newEntry->name))
		{
			// if parsed slug not defined -> file name without extension and flavor is taken the default entry name
			$tempSlug = str_replace($this->dropFolderFile->parsedFlavor, '', $this->dropFolderFile->fileName); // remove flavor name part
			$tempSlug = substr($tempSlug, 0, strrchr($tempSlug, '.')+1); // remove extension
			$newEntry->name = $tempSlug;
		}

		try 
		{
			$this->impersonate($this->dropFolderFile->partnerId);
			$addedEntry = $this->kClient->baseEntry->add($newEntry, $resource, null);
			$this->unimpersonate();
			
			// set all addional files as handled
			if ($addionnalFileIds) {
				$this->setAsHandled($addionnalFileIds);
			}
			$this->dropFolderFile->status = KalturaDropFolderFileStatus::HANDLED;
		
		}
		catch (Exception $e)
		{
			KalturaLog::err('Cannot add new entry - '.$e->getMessage());
			$this->dropFolderFile->status = KalturaDropFolderFileStatus::ERROR_HANDLING;
			$this->dropFolderFile->errorDescription = 'Internal error adding new entry';	
			return false;
		}
		
		return true;
	}
	

	
	/**
	 * Match the current file to an existing entry and flavor according to the slug regex.
	 * Update the matched entry with the new file and all other relevant files from the drop folder, according to the ingestion profile.
	 *
	 * @return bool true if file was handled or false otherwise
	 */
	private function addAsExistingContent()
	{
		// check for matching entry and flavor
		
		if (is_null($this->dropFolderFile->parsedFlavor))
		{
			$this->dropFolderFile->status = KalturaDropFolderFileStatus::ERROR_HANDLING;
			$this->dropFolderFile->errorDescription = 'Cannot match to existing entry with no flavor reference';
			KalturaLog::err($this->dropFolderFile->errorDescription);
			return false; // file not handled
		}

		$matchedEntry = $this->getEntryByReferenceId($this->dropFolderFile->parsedSlug);
		
		if (!$matchedEntry)
		{
			$this->dropFolderFile->status = KalturaDropFolderFileStatus::NO_MATCH;
			$this->dropFolderFile->errorDescription = 'No matching entry found';
			KalturaLog::debug($this->dropFolderFile->errorDescription);
			return true; // file handled even though no match was found
		}

		if (!$this->parsedFlavorObject)
		{
			$this->dropFolderFile->status = KalturaDropFolderFileStatus::ERROR_HANDLING;
			$this->dropFolderFile->errorDescription = 'Parsed flavor system name ['.$this->dropFolderFile->parsedFlavor.'] could not be found';
			KalturaLog::err($this->dropFolderFile->errorDescription);
			return false; // file not handled
		}
		

		$entryConversionProfileId = $matchedEntry->ingestionProfileId;
		if (is_null($entryConversionProfileId)) {
			$entryConversionProfileId = $this->getIngestionProfile()->id;
		}

		$resource = $this->getAllIngestedFiles($entryConversionProfileId);
		if (!$resource) {
			$this->dropFolderFile->status = KalturaDropFolderFileStatus::WAITING;
			return false;
		}
		$addionnalFileIds = array();
		foreach ($resource->resources as $assetContainer) {
			if ($assetContainer->resource->dropFolderFileId != $this->dropFolderFile->id) {
				$addionnalFileIds[] = $assetContainer->resource->dropFolderFileId;
			}
		}
		
		try 
		{
			$this->impersonate($this->dropFolderFile->partnerId);
			$updatedEntry = $this->kClient->baseEntry->update($matchedEntry->id, null, $resource);
			$this->unimpersonate();
			
			// set all addional files as handled
			if ($addionnalFileIds) {
				$this->setAsHandled($addionnalFileIds);
			}
			$this->dropFolderFile->status = KalturaDropFolderFileStatus::HANDLED;
		
		}
		catch (Exception $e)
		{
			KalturaLog::err('Cannot update entry - '.$e->getMessage());
			$this->dropFolderFile->status = KalturaDropFolderFileStatus::ERROR_HANDLING;
			$this->dropFolderFile->errorDescription = 'Internal error updating entry';	
			return false;
		}
		
		return true;		
	}
	
	/**
	 * Check if all required files for the given ingestion profile are in the drop folder.
	 * If yes -> retrun a KalturaAssetsParamsResourceContainers resource containing them + the current file
	 * If not -> return false
	 * 
	 * @param int $ingestionProfileId
	 * @return KalturaAssetsParamsResourceContainers
	 */
	private function getAllIngestedFiles($ingestionProfileId = null)
	{
		if(is_null($ingestionProfileId))
			$ingestionProfileId = $this->getIngestionProfile()->id;
		
		$fileFilter = new KalturaDropFolderFileFilter();
		$fileFilter->dropFolderIdEqual = $this->dropFolder->id;
		$fileFilter->statusIn = KalturaDropFolderFileStatus::PENDING.','.KalturaDropFolderFileStatus::WAITING;
		$fileFilter->parsedSlugEqual = $this->dropFolderFile->parsedSlug; // must belong to the same entry
				
		$existingFileList = $this->kClient->dropFolderFile->listAction($fileFilter); // current file will not be returned because parsed slug is not yet set
		
		$existingFlavors = array();
		$existingFlavors[$this->dropFolderFile->parsedFlavor] = $this->dropFolderFile->id;
		
		foreach ($existingFileList->objects as $existingFile)
		{
			$existingFlavors[$existing->parsedSlug] = $existingFile->id;
		}
		
		$assetContainerArray = array();
		$currentFlavorAdded = false;
		
		$assetParamsList = $this->kClient->conversionProfile->listAssetParams($ingestionProfileId);
		foreach ($assetParamsList->objects as $assetParams)
		{
			if ($assetParams->origin == KalturaAssetParamsOrigin::CONVERT) {
				continue;
			}
			
			if ($assetParams->readyBehavior == KalturaFlavorReadyBehaviorType::REQUIRED && !array_key_exists($assetParams->systemName, $existingFlavors)) {
				return false;
			}
			
			$assetContainer = new KalturaAssetParamsResourceContainer();
			$assetContainer->assetParamsId = $assetParams->id;
			$assetContainer->resource = new KalturaDropFolderFileResource();
			$assetContainer->resource->dropFolderFileId = $existingFlavors[$assetParams->systemName];
			$assetContainerArray[] = $assetContainer;
			if ($assetContainer->resource->dropFolderFileId === $this->dropFolderFile->id) {
				$currentFlavorAdded = true;
			}
		}
		
		// add current drop folder file to list even if it is not part of the ingestion profile
		if (!$currentFlavorAdded) {
			$assetContainer = new KalturaAssetParamsResourceContainer();
			$assetContainer->assetParamsId = $this->parsedFlavorObject->id;
			$assetContainer->resource = new KalturaDropFolderFileResource();
			$assetContainer->resource->dropFolderFileId = $this->dropFolderFile->id;
			$assetContainerArray[] = $assetContainer;
		}
		
		$containers = new KalturaAssetsParamsResourceContainers();
		$containers->resources = $assetContainerArray;
		
		return $containers;		
	}
}