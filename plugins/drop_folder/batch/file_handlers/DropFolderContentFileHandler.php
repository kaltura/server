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
	
	public function getType() {
		return DropFolderFileHandlerType::CONTENT;
	}


	public function handle()
	{
		if (!$this->config) {
			//TODO: add error!
		}
		
		if (!$this->kClient) {
			//TODO: add error!
		}
		
		if (!$this->dropFolder) {
			//TODO: add error!
		}
		
		if (!$this->dropFolderFile) {
			//TODO: add error!
		}
		
		// parse file name according to slugRegex
		$regexMatch = $this->parseRegex();
		if (!$regexMatch) {
			//TODO: what to do ?
		}
		
		$this->addAsExistingContent();
		
		
		switch ($this->config->contentMatchPolicy)
		{
			case KalturaDropFolderContentFileHandlerMatchPolicy::ADD_AS_NEW:
				$entryAdded = $this->addAsNewContent();
				if (!$entryAdded) {
					//TODO: implement
				}
				break;
			
			case KalturaDropFolderContentFileHandlerMatchPolicy::MATCH_EXISTING_OR_KEEP_IN_FOLDER:
				$entryAdded = $this->addAsExistingContent();
				if (!$entryAdded) {
					$this->dropFolderFile->status = KalturaDropFolderFileStatus::NO_MATCH;
					$this->dropFolderFile->errorDescription = 'No matching entry found';
				}
				break;
				
			case KalturaDropFolderContentFileHandlerMatchPolicy::MATCH_EXISTING_OR_ADD_AS_NEW:
				$entryAdded = $this->addAsExistingContent();
				if (!$entryAdded) {
					$entryAdded = $this->addAsNewContent();
					if (!$entryAdded) {
						//TODO: implement
					}
				}
				break;
		}

		if ($entryAdded)
		{
			//TODO: update status to HANDLED + parsed slug and flavor
		}
		else
		{
			//TODO: update status to ERROR and update error description
		}		
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
				//TODO: error - flavor system name not found!	
			}
		}
		
		return true;		
	}
	

	
	
	/**
	 * Add a new entry with the given drop folder file as the resource.
	 * Entry's ingestion profile id should be the one defined on the file's drop folder object.
	 */
	private function addAsNewContent()
	{ 
		$resource = new KalturaDropFolderFileResource();
		$resource->dropFolderFileId = $this->dropFolderFile->getId();
		
		$templateEntry = new KalturaBaseEntry();
		$templateEntry->ingestionProfileId = $this->dropFolder->ingestionProfileId;
		$templateEntry->name = $this->dropFolderFile->parsedSlug;
		
		if (!is_null($this->dropFolderFile->parsedFlavor))
		{
			$resource = $this->getAllRequiredFiles($this->dropFolder->ingestionProfileId);
			if (!$resource) {
				$this->dropFolderFile->status = KalturaDropFolderFileStatus::WAITING;
				return false;
			}
		}

		$handledFiles = array(); //TODO: use
		try 
		{
			$addedEntry = $this->kClient->baseEntry->add($templateEntry, $resource, $templateEntry->type);
		}
		catch (Exception $e)
		{
			//TODO: set error
			
			return false;
		}
		
		//TODO: return the LIST of handled files!
		
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
		
		$results = $this->kClient->doMultiRequest();
		
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
	
	
	private function addAsExistingContent()
	{
		list($matchedEntry, $matchedFlavor) = $this->matchEntryAndFlavor();
		
		if (!$matchedEntry)
		{
			return false;
		}
		
		if (!$matchedFlavor && !is_null($this->dropFolderFile->parsedFlavor))
		{
			//TODO: error! actually this check is already done in parseRegex - need to unite
		}
		
		//TODO: flavorAsset->list
		
		//TODO: if flavorAsset does not exist yet -> use flavorAsset.add to add a current file as a new flavor asset
		
		//TODO: if flavorAsset already exists -> 
		//			get the conversion profile from the matches entry
		//                  if no conversion profile / profile contains only current flavor as required / profile doesn't contain current flavor / all required flavor for the profile already exists in the folder
		//                       => call baseEntry.update to replace all relevant flavors!
		//                  else
		//                       => just do nothing and change the file status to WAITING!
		
		
		//TODO: return true/false - if entry was added
	}
	
	
	private function getAllRequiredFiles($ingestionProfileId)
	{
		
		$fileFilter = new KalturaDropFolderFileFilter();
		$fileFilter->dropFolderIdEqual = $this->dropFolder->id;
		$fileFilter->statusIn = KalturaDropFolderFileStatus::PENDING.','.KalturaDropFolderFileStatus::WAITING;
		$fileFilter->parsedSlugEqual = $this->dropFolderFile->parsedSlug;
				
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