<?php

//TODO: add logs!!

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
	
	public function getType() {
		return DropFolderFileHandlerType::CONTENT;
	}
	
	private function updateAndClose()
	{
		//TODO: update file
		try {
			$this->kClient->dropFolderFile->update($this->dropFolderFile->id, $this->dropFolderFile);
		}
		catch (Exception $e) {
			//TODO: implement
		}
		
		//TODO: return true/false
	}


	public function handle()
	{
		if (!$this->config) {
			KalturaLog::err('File handler configuration not defined');
			return false;
		}
		
		if (!$this->kClient) {
			KalturaLog::err('Kaltura client not defined');
			return false;
		}
		
		if (!$this->dropFolder) {
			KalturaLog::err('Drop folder not defined');
			return false;
		}
		
		if (!$this->dropFolderFile) {
			KalturaLog::err('Drop folder file not defined');
			return false;
		}
		
		// parse file name according to slugRegex
		$regexMatch = $this->parseRegex();
		if (!$regexMatch) {
			//TODO: what to do ?
			$this->dropFolderFile->status = KalturaDropFolderFileStatus::ERROR_HANDLING;
			$this->dropFolderFile->errorDescription = 'File name ['.$this->dropFolderFile->fileName.'] does not match defined slug regex ['.$this->config->slugRegex.']';
			KalturaLog::err($this->dropFolderFile->errorDescription);
			return $this->updateAndClose();
		}
		
		//TODO: check if parsed flavor exists!
		
		switch ($this->config->contentMatchPolicy)
		{
			case KalturaDropFolderContentFileHandlerMatchPolicy::ADD_AS_NEW:
				$fileHandled = $this->addAsNewContent();
				if (!$fileHandled) {
					//TODO: implement
				}
				break;
			
			case KalturaDropFolderContentFileHandlerMatchPolicy::MATCH_EXISTING_OR_KEEP_IN_FOLDER:
				$fileHandled = $this->addAsExistingContent();
				if (!$fileHandled) {
					$this->dropFolderFile->status = KalturaDropFolderFileStatus::NO_MATCH;
					$this->dropFolderFile->errorDescription = 'No matching entry found';
				}
				break;
				
			case KalturaDropFolderContentFileHandlerMatchPolicy::MATCH_EXISTING_OR_ADD_AS_NEW:
				$fileHandled = $this->addAsExistingContent();
				if (!$fileHandled) {
					$fileHandled = $this->addAsNewContent();
					if (!$fileHandled) {
						//TODO: implement
					}
				}
				break;
		}

		//TODO: update the file according to the local status (status, parsed slug, parsed flavor, error description).	
	}
	
	private function parseRegex()
	{
		$matches = null;
		$slugRegex = is_null($this->config->slugRegex) ? self::DEFAULT_SLUG_REGEX : $this->config->slugRegex;
		$matchFound = @preg_match($slugRegex, $this->dropFolderFile->fileName, $matches);
		
		if (!$matchFound) {
			return false;
		}
		
		
		$this->dropFolderFile->parsedSlug   = isset($matches[self::REFERENCE_ID_WILDCARD]) ? $matches[self::REFERENCE_ID_WILDCARD] : null;
		$this->dropFolderFile->parsedFlavor = isset($matches[self::FLAVOR_NAME_WILDCARD])  ? $matches[self::FLAVOR_NAME_WILDCARD]  : null;
		
		if (is_null($this->dropFolderFile->parsedSlug))
		{
			$tempSlug = str_replace($this->dropFolderFile->parsedFlavor, '', $this->dropFolderFile->fileName); // remove flavor name part
			$tempSlug = substr($tempSlug, 0, strrchr($tempSlug, '.')+1); // remove extension
			$this->dropFolderFile->parsedSlug = $tempSlug;
		}
		
		if (!is_null($this->dropFolderFile->parsedFlavor))
		{
			$flavorFilter = new KalturaFlavorParamsFilter();
			$flavorFilter->systemNameEqual = $this->dropFolderFile->parsedFlavor;
			$flavorList = $this->kClient->flavorParams->listAction($flavorFilter);
			if (!isset($flavorList->objects) || !isset($flavorList->objects[0]))
			{
				//TODO: error - flavor's system name not found!	
			}
		}
		
		return true;		
	}
	

	//TODO: Return if file handled or not
	private function addAsNewContent()
	{ 
		$addionnalFileIds = array();
		$resource = null;
		if (is_null($this->dropFolderFile->parsedFlavor))
		{
			$resource = new KalturaDropFolderFileResource();
			$resource->dropFolderFileId = $this->dropFolderFile->getId();
		}
		else
		{
			$resource = $this->getAllRequiredFiles($this->dropFolder->ingestionProfileId);
			if (!$resource) {
				$this->dropFolderFile->status = KalturaDropFolderFileStatus::WAITING;
				return false;
			}
			foreach ($resource->resources as $assetContainer) {
				$addionnalFileIds[] = $assetContainer->resource->dropFolderFileId;
			}
		}
		
		$templateEntry = new KalturaBaseEntry();
		$templateEntry->ingestionProfileId = $this->dropFolder->ingestionProfileId;
		$templateEntry->name = $this->dropFolderFile->parsedSlug;

		try 
		{
			$addedEntry = $this->kClient->baseEntry->add($templateEntry, $resource, $templateEntry->type);
		}
		catch (Exception $e)
		{
			//TODO: set error
			
			return false;
		}
		
		//TODO: go over $addionnalFileIds and update their status to HANDLED
		
		return true;
	}
	
	
	private function matchEntryAndFlavor()
	{
		$this->kClient->startMultiRequest();
		
		// find entry
		$entryFilter = new KalturaBaseEntryFilter();
		$entryFilter->referenceIdEqual = $this->dropFolderFile->parsedSlug;
		$this->kClient->baseEntry->listAction($entryFilter);
		
		// find flavor
		$flavorFilter = new KalturaFlavorParamsFilter();
		$flavorFilter->systemNameEqual = $this->dropFolderFile->parsedFlavor;
		$this->kClient->flavorParams->listAction($flavorFilter);
		
		$results = $this->kClient->doMultiRequest(); //TODO: add try/catch ?
		
		$matchedEntryList  = $results[0];
		$matchedFlavorList = $results[1];
		
		$matchedEntry = null;
		$matchedFlavor = null;
		
		if (is_array($matchedEntryList->objects) || isset($matchedEntryList->objects[0]) ) {
			$matchedEntry = $matchedEntryList->objects[0];
		}
		if (is_array($matchedFlavorList->objects) || isset($matchedFlavorList->objects[0]) ) {
			$matchedFlavor = $matchedFlavorList->objects[0];
		}
				
		return array($matchedEntry, $matchedFlavor);
	}
	
	//TODO: return if file handled or not
	private function addAsExistingContent()
	{
		if (is_null($this->dropFolderFile->parsedFlavor))
		{
			//TODO: cannot continue with no flavor for existing entry!
		}		
		
		list($matchedEntry, $matchedFlavor) = $this->matchEntryAndFlavor();
		
		if (!$matchedEntry)
		{
			return false; // TODO: add error
		}
		
		if (!$matchedFlavor)
		{
			return false; // TODO: add error
		}
		
		
		$existingAssets = $this->kClient->flavorAsset->getByEntryId($matchedEntry->id); //TODO: add try/catch
		$existingAssets = $existingAssets->objects;
		$flavorAssetExists = false;
		
		foreach ($existingAssets->objects as $existingAsset)
		{
			if ($existingAsset->flavorParamsId === $matchedFlavor->id) {
				$flavorAssetExists = true;
				break;
			}
		}
		
		if (!$flavorAssetExists) // flavor asset does not exist yet
		{
			// add the current file as a new flavor asset for the existing entry
			$flavorAsset = new KalturaFlavorAsset();
			$flavorAsset->flavorParamsId == $matchedFlavor->id;
			
			$resource = new KalturaDropFolderFileResource();
			$resource->dropFolderFileId = $this->dropFolderFile->getId();
			$addedEntry = $this->kClient->flavorAsset->add($matchedEntry->id, $flavorAsset, $resource); //TODO: add try/catch
			return true;
		}
		else // flavor asset already exits
		{
			$entryConversionProfileId = $matchedEntry->ingestionProfileId;
			if (is_null($entryConversionProfileId))
			{
				//  => TODO: call baseEntry.update to replace all relevant flavors!
			}
			else
			{
				$entryConversionProfile = $this->kClient->conversionProfile->get($entryConversionProfileId);  //TODO: add try/catch
				$profileParamsIds = explode(',', $entryConversionProfile->flavorParamsIds);
				
				if (!in_array($matchedFlavor->id, $profileParamsIds))
				{
					//  => TODO: call baseEntry.update to replace all relevant flavors!
				}
				else
				{
					$resource = $this->getAllRequiredFiles($entryConversionProfileId);
					if (!$resource) {
						$this->dropFolderFile->status = KalturaDropFolderFileStatus::WAITING;
						return false;
					}
					foreach ($resource->resources as $assetContainer) {
						$addionnalFileIds[] = $assetContainer->resource->dropFolderFileId;
					}
					
					//  => TODO: call baseEntry.update to replace all relevant flavors!
				}				
				
			}
			
			//TODO: Remember to go over additional file ids
			
			//TODO: return true/false
		}
		
	}
	
	
	private function getAllRequiredFiles($ingestionProfileId)
	{
		
		$fileFilter = new KalturaDropFolderFileFilter();
		$fileFilter->dropFolderIdEqual = $this->dropFolder->id;
		$fileFilter->statusIn = KalturaDropFolderFileStatus::PENDING.','.KalturaDropFolderFileStatus::WAITING;
		$fileFilter->parsedSlugEqual = $this->dropFolderFile->parsedSlug; // must belong to the same entry
				
		$existingFileList = $this->kClient->dropFolderFile->listAction($fileFilter); // current file will not be returned
		
		$existingFlavors[] = array();
		$existingFlavors[$this->dropFolderFile->parsedFlavor] = $this->dropFolderFile->id;
		
		foreach ($existingFileList->objects as $existingFile)
		{
			$existingFlavors[$existing->parsedSlug] = $existingFile->id;
		}
		
		$assetContainerArray = array();
		
		$assetParamsList = $this->kClient->conversionProfile->listAssetParams($ingestionProfileId);
		foreach ($assetParamsList->objects as $assetParams)
		{
			if ($assetParams->readyBehavior != KalturaFlavorReadyBehaviorType::REQUIRED &&  $assetParams->origin != KalturaAssetParamsOrigin::INGEST) {
				continue;
			}
			
			if (!array_key_exists($assetParams->systemName, $existingFlavors)) {
				return false;
			}
			
			$assetContainer = new KalturaAssetParamsResourceContainer();
			$assetContainer->assetParamsId = $assetParams->id;
			$assetContainer->resource = new KalturaDropFolderFileResource();
			$assetContainer->resource->dropFolderFileId = $existingFlavors[$assetParams->systemName];
			$assetContainerArray[] = $assetContainer;
		}
		
		$containers = new KalturaAssetsParamsResourceContainers();
		$containers->resources = $assetContainerArray;
		
		return $containers;		
	}
}