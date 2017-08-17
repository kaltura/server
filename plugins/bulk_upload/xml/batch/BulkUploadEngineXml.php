<?php
/**
 * Class for the handling Bulk upload using XML in the system
 *
 * @package plugins.bulkUploadXml
 * @subpackage Scheduler.BulkUpload
 */
class BulkUploadEngineXml extends KBulkUploadEngine
{
	/**
	 * The defalut thumbnail tag
	 * @var string
	 */
	const DEFAULT_THUMB_TAG = 'default_thumb';
	
	const OBJECT_TYPE_TITLE = 'entry';
	
	/**
	 * The default ingestion profile id
	 * @var int
	 */
	private $defaultConversionProfileId = null;
	
	/**
	 * Holds the number of the current proccessed item
	 * @var int
	 */
	private $currentItem = 0;
	
	/**
	 * Allows the usage of server content resource
	 * @var bool
	 */
	protected $allowServerResource = false;
	
	/**
	 * Maps the flavor params name to id
	 * @var array()
	 */
	private $assetParamsNameToIdPerConversionProfile = null;

	/**
	 * Maps the asset id to flavor params id
	 * @var array()
	 */
	private $assetIdToAssetParamsId = null;
	
	/**
	 * Maps the access control name to id
	 * @var array()
	 */
	private $accessControlNameToId = null;
	
	/**
	 * Maps the converstion profile name to id
	 * @var array()
	 */
	private $conversionProfileNameToId = array();
	
	/**
	 * Maps the storage profile name to id
	 * @var array()
	 */
	private $storageProfileNameToId = null;
	
	/**
	 * Conversion profile xsl file
	 * @var string
	 */
	protected $conversionProfileXsl = null;
	
	/**
	 * The original xml content after going through xsl transformation
	 * @var string
	 */
	protected $xslTransformedContent = null;

	/**
	 * @param KalturaBatchJob $job
	 */
	public function __construct(KalturaBatchJob $job)
	{
		parent::__construct($job);
		
		if(KBatchBase::$taskConfig->params->allowServerResource)
			$this->allowServerResource = (bool) KBatchBase::$taskConfig->params->allowServerResource;
	}
	
	/* (non-PHPdoc)
	 * @see KBulkUploadEngine::HandleBulkUpload()
	 */
	public function handleBulkUpload()
	{
		$this->validate();
	    $this->parse();
	}
	
	protected function getSchemaType()
	{
		return KalturaSchemaType::BULK_UPLOAD_XML;
	}
	
	/**
	 * Validates that the xml is valid using the XSD
	 *@return bool - if the validation is ok
	 */
	protected function validate()
	{
		if(!file_exists($this->data->filePath))
		{
			throw new KalturaBatchException("File doesn't exist [{$this->data->filePath}]", KalturaBatchJobAppErrors::BULK_FILE_NOT_FOUND);
		}
		
		libxml_use_internal_errors(true);
		
		$this->loadXslt();
			
		$xdoc = new KDOMDocument();
		
		$this->xslTransformedContent = $this->xslTransform($this->data->filePath);
		
		KalturaLog::info("Tranformed content: " . $this->xslTransformedContent);
		
		libxml_clear_errors();
		if(!$xdoc->loadXML($this->xslTransformedContent)){
			$errorMessage = kXml::getLibXmlErrorDescription($this->xslTransformedContent);
			throw new KalturaBatchException("Could not load xml [{$this->job->id}], $errorMessage", KalturaBatchJobAppErrors::BULK_VALIDATION_FAILED, null);
		}
		//Validate the XML file against the schema
		libxml_clear_errors();
		
		$xsdURL = KBatchBase::$kClient->schema->serve($this->getSchemaType());
		if(KBatchBase::$taskConfig->params->xmlSchemaVersion)
			$xsdURL .=  "&version=" . KBatchBase::$taskConfig->params->xmlSchemaVersion;
		$xsd = KCurlWrapper::getContent($xsdURL);
		
		if(!$xdoc->schemaValidateSource($xsd))
		{
			$errorMessage = kXml::getLibXmlErrorDescription($this->xslTransformedContent);
			throw new KalturaBatchException("Validate files failed on job [{$this->job->id}], $errorMessage", KalturaBatchJobAppErrors::BULK_VALIDATION_FAILED);
		}
		
		return true;
	}
	
	/**
	 * Load xsl transform
	 */
	protected function loadXslt()
	{
		$data = self::getData();
		//TODO: remove this statement once its purpose is served - conversionProfileId information should be taken only from the objectData
		$conversionProfileId = isset ($data->objectData->conversionProfileId) ? $data->objectData->conversionProfileId : $data->conversionProfileId;
        if(!$conversionProfileId)
           throw new KalturaBatchException("Conversion profile not defined", KalturaBatchJobAppErrors::BULK_MISSING_MANDATORY_PARAMETER);
		
		KBatchBase::impersonate($this->currentPartnerId);;
		$conversionProfile = KBatchBase::$kClient->conversionProfile->get($conversionProfileId);
		KBatchBase::unimpersonate();
		if(!$conversionProfile || !$conversionProfile->xslTransformation)
			return false;
		$this->conversionProfileXsl = $conversionProfile->xslTransformation;
		return true;
	}

	/**
	 * Transform Xml file with conversion profile xsl
	 * If xsl is not found, original Xml returned
	 * @param string $filePath the original xml that was taken from partner file path
	 * @return string - transformed Xml
	 */
	protected function xslTransform($filePath)
	{
		$xdoc = file_get_contents($filePath);
		if(is_null($xdoc) || is_null($this->conversionProfileXsl))
			return $xdoc;
		
		libxml_clear_errors();
		$xml = new KDOMDocument();
		if(!$xml->loadXML($xdoc)){
			$errorMessage = kXml::getLibXmlErrorDescription($xdoc);
			throw new KalturaBatchException("Could not load xml [{$this->job->id}], $errorMessage", KalturaBatchJobAppErrors::BULK_VALIDATION_FAILED);
		}
		
		libxml_clear_errors();
		$proc = new XSLTProcessor;
		$proc->registerPHPFunctions(kXml::getXslEnabledPhpFunctions());
		$xsl = new KDOMDocument();
		if(!$xsl->loadXML($this->conversionProfileXsl)){
			$errorMessage = kXml::getLibXmlErrorDescription($this->conversionProfileXsl);
			throw new KalturaBatchException("Could not load xsl [{$this->job->id}], $errorMessage", KalturaBatchJobAppErrors::BULK_VALIDATION_FAILED);
		}
		$proc->importStyleSheet($xsl);
		libxml_clear_errors();
		$transformedXml = $proc->transformToXML($xml);
		if(!$transformedXml){
			$errorMessage = kXml::getLibXmlErrorDescription($this->conversionProfileXsl);
			throw new KalturaBatchException("Could not transform xml [{$this->job->id}], $errorMessage", KalturaBatchJobAppErrors::BULK_VALIDATION_FAILED);
		}
		return $transformedXml;
	}
	
	/**
	 * Parses the Xml file lines and creates the right actions in the system
	 */
	protected function parse()
	{
		$this->currentItem = 0;
		
		$xdoc = new SimpleXMLElement($this->xslTransformedContent);
		
		foreach( $xdoc->channel as $channel)
		{
			$this->handleChannel($channel);
			if($this->exceededMaxRecordsEachRun) // exit if we have proccessed max num of items
				return;
		}
	}

	/**
	 * @param string $referenceId
	 * @return string entry id
	 */
	protected function getEntryIdFromReference($referenceId)
	{
		$existingEntry = $this->getEntryFromReference($referenceId);
		if($existingEntry)
			return $existingEntry->id;
			
		return null;
	}

	/**
	 * @param string $referenceId
	 * @return KalturaBaseEntry
	 */
	protected function getEntryFromReference($referenceId)
	{
		$filter = new KalturaBaseEntryFilter();
		$filter->referenceIdEqual = $referenceId;
		$filter->statusIn = implode(',', array(
			KalturaEntryStatus::ERROR_IMPORTING,
			KalturaEntryStatus::ERROR_CONVERTING,
			KalturaEntryStatus::IMPORT,
			KalturaEntryStatus::PRECONVERT,
			KalturaEntryStatus::READY,
			KalturaEntryStatus::PENDING,
			KalturaEntryStatus::NO_CONTENT,
		));
		$pager = new KalturaFilterPager();
		$pager->pageSize = 1;
		
		KBatchBase::impersonate($this->currentPartnerId);;
		$entries = null;
		try
		{
			$entries = KBatchBase::$kClient->baseEntry->listAction($filter, $pager);
		}
		catch (KalturaException $e)
		{
			KalturaLog::err($e->getMessage());
		}
		KBatchBase::unimpersonate();
		
		/* @var $entries KalturaBaseEntryListResponse */
		if(!$entries || !$entries->totalCount)
			return null;
		
		return reset($entries->objects);
	}

	/**
	 * @param string $entryId
	 * @return KalturaBaseEntry
	 */
	protected function getEntry($entryId)
	{
		$entry = null;
		KBatchBase::impersonate($this->currentPartnerId);;
		try
		{
			$entry = KBatchBase::$kClient->baseEntry->get($entryId);
		}
		catch (KalturaException $e)
		{
			KalturaLog::err($e->getMessage());
		}
		KBatchBase::unimpersonate();
		
		return $entry;
	}

	/**
	 * Gets and handles a channel from the mrss
	 * @param SimpleXMLElement $channel
	 */
	protected function handleChannel(SimpleXMLElement $channel)
	{
		$startIndex = $this->getStartIndex();
		
		//Gets all items from the channel
		foreach( $channel->item as $item)
		{
			if($this->currentItem < $startIndex)
			{
				$this->currentItem++;
				continue;
			}
			
			if($this->exceededMaxRecordsEachRun) // exit if we have proccessed max num of items
				return;
			
			$this->currentItem++; //move to the next item (first item is 1)
			try
			{
				$this->checkAborted();
				
				$actionToPerform = self::$actionsMap[KalturaBulkUploadAction::ADD];
						
				$action = KalturaBulkUploadAction::ADD;
				if(isset($item->action))
				{
					$actionToPerform = strtolower($item->action);
				}
				elseif(isset($item->entryId))
				{
					$actionToPerform = self::$actionsMap[KalturaBulkUploadAction::UPDATE];
				}
				elseif(isset($item->referenceId))
				{
					if($this->getEntryIdFromReference("{$item->referenceId}"))
						$actionToPerform = self::$actionsMap[KalturaBulkUploadAction::UPDATE];
				}
				
				switch($actionToPerform)
				{
					case self::$actionsMap[KalturaBulkUploadAction::ADD]:
						$action = KalturaBulkUploadAction::ADD;
						$this->validateItem($item);
						$this->handleItemAdd($item);
						break;
					case self::$actionsMap[KalturaBulkUploadAction::UPDATE]:
						$action = KalturaBulkUploadAction::UPDATE;
						$this->handleItemUpdate($item);
						break;
					case self::$actionsMap[KalturaBulkUploadAction::DELETE]:
						$action = KalturaBulkUploadAction::DELETE;
						$this->handleItemDelete($item);
						break;
					default :
						throw new KalturaBatchException("Action: {$actionToPerform} is not supported", KalturaBatchJobAppErrors::BULK_ACTION_NOT_SUPPORTED);
				}
			}
			catch (Exception $e)
			{
				//in case an exception was thrown we need to change back to batch user in order to addBulkResult
				KBatchBase::unimpersonate();
				KalturaLog::err('Item failed (' . get_class($e) . '): ' . $e->getMessage());
				$bulkUploadResult = $this->createUploadResult($item, $action);
				if ($this->exceededMaxRecordsEachRun){
					return;
				}
				$bulkUploadResult->status = KalturaBulkUploadResultStatus::ERROR;
				$bulkUploadResult->errorDescription = $e->getMessage();
				$bulkUploadResult->entryStatus = KalturaEntryStatus::ERROR_IMPORTING;
				$this->addBulkUploadResult($bulkUploadResult);
			}
		}
	}
	
	/**
	 * Validates the given item so it's valid (some validation can't be enforced in the schema)
	 * @param SimpleXMLElement $item
	 */
	protected function validateItem(SimpleXMLElement $item)
	{
		//Validates that the item type has a matching type element
		$this->validateTypeToTypedElement($item);
	}
	
	/**
	 * Gets the flavor params from the given flavor asset
	 * @param string $assetId
	 */
	protected function getAssetParamsIdFromAssetId($assetId, $entryId)
	{
		if(is_null($this->assetIdToAssetParamsId[$entryId]))
		{
			$this->initAssetIdToAssetParamsId($entryId);
		}
		
		if(isset($this->assetIdToAssetParamsId[$entryId][$assetId]))
		{
			return $this->assetIdToAssetParamsId[$entryId][$assetId];
		}
		else //The asset wasn't found on the entry
		{
			throw new KalturaBatchException("Asset Id [$assetId] not found on entry [$entryId]", KalturaBatchJobAppErrors::BULK_ITEM_VALIDATION_FAILED);
		}
	}
	
	/**
	 * Removes all non updatble fields from the entry
	 * @param KalturaBaseEntry $entry
	 */
	protected function removeNonUpdatbleFields(KalturaBaseEntry $entry)
	{
		$retEntry = clone $entry;
		$retEntry->conversionProfileId = null;
		return $retEntry;
	}
	
	/**
	 * Handles xml bulk upload update
	 * @param SimpleXMLElement $item
	 * @throws KalturaException
	 */
	protected function handleItemUpdate(SimpleXMLElement $item)
	{
		$entryId = null;
		$conversionProfileId = null;
		if(isset($item->entryId))
		{
			$entryId = "{$item->entryId}";
			
			$existingEntry = $this->getEntry($entryId);
			if(!$existingEntry)
				throw new KalturaBatchException("Entry id [$entryId] not found", KalturaBatchJobAppErrors::BULK_ITEM_NOT_FOUND);
				
			$conversionProfileId = $existingEntry->conversionProfileId;
		}
		elseif(isset($item->referenceId))
		{
			$existingEntry = $this->getEntryFromReference("{$item->referenceId}");
			if(!$existingEntry)
				throw new KalturaBatchException("Reference id [{$item->referenceId}] not found", KalturaBatchJobAppErrors::BULK_ITEM_NOT_FOUND);
				
			$entryId = $existingEntry->id;
			$conversionProfileId = $existingEntry->conversionProfileId;
		}
		else
		{
			throw new KalturaBatchException("Missing entry id element", KalturaBatchJobAppErrors::BULK_MISSING_MANDATORY_PARAMETER);
		}
		
		// Overwriting conversionProfileId if one is supplied in the XML
		if(isset($item->conversionProfileId) || isset($item->conversionProfile))
			$conversionProfileId = $this->getConversionProfileId($item);
		KalturaLog::info("Conversion profile found within XML - setting to [ $conversionProfileId ]");
		
		//Throw exception in case of max proccessed items and handle all exceptions there
		$updatedEntryBulkUploadResult = $this->createUploadResult($item, KalturaBulkUploadAction::UPDATE);
		
		if($this->exceededMaxRecordsEachRun) // exit if we have proccessed max num of items
			return;

		$entry = $this->createEntryFromItem($item, $existingEntry->type); //Creates the entry from the item element
		
		$this->handleTypedElement($entry, $item); //Sets the typed element values (Mix, Media, ...)
				
		$thumbAssets = array();
		$thumbAssetsResources = array();
		$flavorAssets = array();
		$flavorAssetsResources = array();
		$noParamsThumbAssets = array(); //Holds the no flavor params thumb assests
		$noParamsThumbResources = array(); //Holds the no flavor params resources assests
		$noParamsFlavorAssets = array();  //Holds the no flavor params flavor assests
		$noParamsFlavorResources = array(); //Holds the no flavor params flavor resources
		$flavorAssetsForUpdate = array();
		$flavorResources = array();
		$thumbAssetsForUpdate = array();
		$thumbResources = array();
		$resource = new KalturaAssetsParamsResourceContainers(); // holds all teh needed resources for the conversion
		$resource->resources = array();

		//default action to perfom for assets and thumbnails is replace
		$contentAssetsAction = self::$actionsMap[KalturaBulkUploadAction::REPLACE];
		$thumbnailsAction = self::$actionsMap[KalturaBulkUploadAction::REPLACE];
		if(isset($item->contentAssets->action) && (isset($item->thumbnails->action)))
		{
			$contentAssetsAction = strtolower($item->contentAssets->action);
			$thumbnailsAction = strtolower($item->thumbnails->action);
		}
		elseif (isset($item->contentAssets->action))
		{
			$contentAssetsAction = strtolower($item->contentAssets->action);
			$thumbnailsAction = strtolower($item->contentAssets->action);
		}
		elseif (isset($item->thumbnails->action))
		{
			$contentAssetsAction = strtolower($item->thumbnails->action);
			$thumbnailsAction = strtolower($item->thumbnails->action);
		}

		if (isset($item->contentAssets->action) && isset($item->thumbnails->action) && ($contentAssetsAction != $thumbnailsAction))
			throw new KalturaBatchException("ContentAsset->action: {$contentAssetsAction} must be the same as thumbnails->action: {$thumbnailsAction}", KalturaBatchJobAppErrors::BULK_ACTION_NOT_SUPPORTED);
		
		//For each content in the item element we add a new flavor asset
		if(isset($item->contentAssets))
		{
			foreach ($item->contentAssets->content as $contentElement)
			{
				KalturaLog::info("contentElement [" . print_r($contentElement->asXml(), true). "]");
				
				if(empty($contentElement)) // if the content is empty skip
				{
					continue;
				}

				$flavorAsset = $this->getFlavorAsset($contentElement, $conversionProfileId);
				$flavorAssetResource = $this->getResource($contentElement, $conversionProfileId);
				if(!$flavorAssetResource)
					continue;
				
				$assetParamsId = $flavorAsset->flavorParamsId;
	
				$assetId = kXml::getXmlAttributeAsString($contentElement, "assetId");
				if($assetId) // if we have an asset id then we need to update the asset
				{
					KalturaLog::info("Asset id [ $assetId]");
					$assetParamsId = $this->getAssetParamsIdFromAssetId($assetId, $entryId);
				}

				if(isset($contentElement->streams))
					$this->handleStreamsElement($contentElement->streams, $entry);

				KalturaLog::info("assetParamsId [$assetParamsId]");
							
				if(is_null($assetParamsId)) // no params resource
				{
					$noParamsFlavorAssets[] = $flavorAsset;
					$noParamsFlavorResources[] = $flavorAssetResource;
					continue;
				}
				else
				{
					$flavorAssetsResources[$flavorAsset->flavorParamsId] = $flavorAssetResource;
				}
				
				$flavorAssets[$flavorAsset->flavorParamsId] = $flavorAsset;
				$assetResource = new KalturaAssetParamsResourceContainer();
				$assetResource->resource = $flavorAssetResource;
				$assetResource->assetParamsId = $assetParamsId;
				$resource->resources[] = $assetResource;

			}
		}
		
		//For each thumbnail in the item element we create a new thumb asset
		if(isset($item->thumbnails))
		{
			foreach ($item->thumbnails->thumbnail as $thumbElement)
			{
				if(empty($thumbElement)) // if the content is empty
				{
					continue;
				}
				
				KalturaLog::info("thumbElement [" . print_r($thumbElement->asXml(), true). "]");
							
				$thumbAsset = $this->getThumbAsset($thumbElement, $conversionProfileId);
				$thumbAssetResource = $this->getResource($thumbElement, $conversionProfileId);
				if(!$thumbAssetResource)
					continue;
										
				$assetParamsId = $thumbAsset->thumbParamsId;
	
				$assetId = kXml::getXmlAttributeAsString($thumbElement, "assetId");
				if($assetId) // if we have an asset id then we need to update the asset
				{
					KalturaLog::info("Asset id [ $assetId]");
					$assetParamsId = $this->getAssetParamsIdFromAssetId($assetId, $entryId);
				}
							
				KalturaLog::info("assetParamsId [$assetParamsId]");
				
				if(is_null($assetParamsId))
				{
					$noParamsThumbAssets[] = $thumbAsset;
					$noParamsThumbResources[] = $thumbAssetResource;
					continue;
				}
				else
				{
					$thumbAssetsResources[$thumbAsset->thumbParamsId] = $thumbAssetResource;
				}
				
				$thumbAssets[$thumbAsset->thumbParamsId] = $thumbAsset;
				$assetResource = new KalturaAssetParamsResourceContainer();
				$assetResource->resource = $thumbAssetResource;
				$assetResource->assetParamsId = $assetParamsId;
				$resource->resources[] = $assetResource;
			}
		}
		
		if(!count($resource->resources))
		{
			if (count($noParamsFlavorResources) == 1)
			{
				$resource = reset($noParamsFlavorResources);
				$noParamsFlavorResources = array();
				$noParamsFlavorAssets = array();
			}
			else
			{
				$resource = null;
			}
		}


		$this->handlePluginAddedData($item, $existingEntry);
		$pluginReplacementOptions = $this->getPluginReplacementOptions($item);

		switch($contentAssetsAction)
		{
			case self::$actionsMap[KalturaBulkUploadAction::UPDATE]:
				list($entry, $nonCriticalErrors) = $this->sendItemUpdateData($entryId, $entry, $flavorAssets, $flavorAssetsResources, $thumbAssets, $thumbAssetsResources);
				break;
			case self::$actionsMap[KalturaBulkUploadAction::REPLACE]:
				list($entry, $nonCriticalErrors) = $this->sendItemReplaceData($entryId, $entry, $resource,
												  $noParamsFlavorAssets, $noParamsFlavorResources,
												  $noParamsThumbAssets, $noParamsThumbResources, $pluginReplacementOptions, $item->keepManualThumbnails);
				$entryId = $entry->id;
				break;
			default :
				throw new KalturaBatchException("Action: {$contentAssetsAction} is not supported", KalturaBatchJobAppErrors::BULK_ACTION_NOT_SUPPORTED);
		}
		//Creates new category associations between the entry and the categories
		$updatedEntryBulkUploadResult = $this->createCategoryAssociations($entryId, $this->implodeChildElements($item->categories), $updatedEntryBulkUploadResult, true);
		//Adds the additional data for the flavors and thumbs
		$this->handleFlavorAndThumbsAdditionalData($entryId, $flavorAssets, $thumbAssets);

		//Updates the bulk upload result for the given entry (with the status and other data)
		$updatedEntryBulkUploadResult->errorDescription = $nonCriticalErrors;
		$this->updateObjectsResults(array($entry), array($updatedEntryBulkUploadResult));
	}

	/**
	 * (non-PHPdoc)
	 * @see KBulkUploadEngine::addBulkUploadResult()
	 */
	protected function addBulkUploadResult(KalturaBulkUploadResult $bulkUploadResult)
	{
		parent::addBulkUploadResult($bulkUploadResult);
					
		$this->handledRecordsThisRun++; //adds one to the count of handled records
	}
	
	/**
	 * Sends the data using a multi requsest according to the given data
	 * @param int $entryID
	 * @param KalturaBaseEntry $entry
	 * @param KalturaResource $resource - the main resource collection for the entry
	 * @param array $noParamsFlavorAssets - Holds the no flavor params flavor assets
	 * @param array $noParamsFlavorResources - Holds the no flavor params flavor resources
	 * @param array $noParamsThumbAssets - Holds the no flavor params thumb assets
	 * @param array $noParamsThumbResources - Holds the no flavor params thumb resources
	 * @param boolean $keepManualThumbnails - flag to keep thumbnails
	 * @return $requestResults - the multi request result
	 */
	protected function sendItemReplaceData($entryId, KalturaBaseEntry $entry ,KalturaResource $resource = null,
										array $noParamsFlavorAssets, array $noParamsFlavorResources,
										array $noParamsThumbAssets, array $noParamsThumbResources, array $pluginReplacementOptions, $keepManualThumbnails = false)
	{

		KalturaLog::info("Resource is: " . print_r($resource, true));
		
		KBatchBase::impersonate($this->currentPartnerId);;
		$updateEntry = $this->removeNonUpdatbleFields($entry);
		$updatedEntry = KBatchBase::$kClient->baseEntry->get($entryId);
		
		KBatchBase::$kClient->startMultiRequest();
		
		$updatedEntryId = $updatedEntry->id;
		if(!is_null($updatedEntry->replacingEntryId))
			$updatedEntryId = $updatedEntry->replacingEntryId;
		
		$advancedOptions = null;
		foreach($pluginReplacementOptions as $pluginReplacementObjectName => $options)
		{
			if(is_null($advancedOptions))
			{
				$advancedOptions = new KalturaEntryReplacementOptions();
				$advancedOptions->pluginOptionItems = array();
			}
			$replacementObject = new $pluginReplacementObjectName();
			foreach($options as $optionName => $optionValue)
			{
				$replacementObject->$optionName = $optionValue;
			}
			
			$advancedOptions->pluginOptionItems[] = $replacementObject;
		}
		if ($keepManualThumbnails)
		{
			if (is_null($advancedOptions))
				$advancedOptions = new KalturaEntryReplacementOptions();
			$advancedOptions->keepManualThumbnails = 1;
		}

		if ($updateEntry->streams )
		{
			$baseEntry = new KalturaMediaEntry();
			$baseEntry->streams = array();
			$baseEntry->streams = $updateEntry->streams;
			$updatedEntry = KBatchBase::$kClient->baseEntry->update($entryId, $baseEntry);
		}

		if($resource)
			KBatchBase::$kClient->baseEntry->updateContent($updatedEntryId ,$resource, $entry->conversionProfileId, $advancedOptions); //to create a temporery entry.
		
		foreach($noParamsFlavorAssets as $index => $flavorAsset) // Adds all the entry flavors
		{
			$flavorResource = $noParamsFlavorResources[$index];
			$flavor = KBatchBase::$kClient->flavorAsset->add($updatedEntryId, $flavorAsset);
			KBatchBase::$kClient->flavorAsset->setContent(KBatchBase::$kClient->getMultiRequestResult()->id, $flavorResource);		// TODO: use flavor instead of getMultiRequestResult
		}

		$requestResults = KBatchBase::$kClient->doMultiRequest();
		if(is_array($requestResults))
			foreach($requestResults as $requestResult)
			{
				if(is_array($requestResult) && isset($requestResult['code']))
					throw new KalturaException($requestResult['message'], $requestResult['code']);

				if($requestResult instanceof Exception)
					throw $requestResult;
			}


		KBatchBase::$kClient->startMultiRequest();
		
		foreach($noParamsThumbAssets as $index => $thumbAsset) //Adds the entry thumb assests
		{
			$thumbResource = $noParamsThumbResources[$index];
			$thumb = KBatchBase::$kClient->thumbAsset->add($updatedEntryId, $thumbAsset);
			KBatchBase::$kClient->thumbAsset->setContent(KBatchBase::$kClient->getMultiRequestResult()->id, $thumbResource);		// TODO: use thumb instead of getMultiRequestResult
			if (strpos($thumbAsset->tags, self::DEFAULT_THUMB_TAG) !== false)
				KBatchBase::$kClient->thumbAsset->setAsDefault($thumb->id);
		}
		
		$requestResults = KBatchBase::$kClient->doMultiRequest();

		$nonCriticalErrors = '';
		$newThumbAssetsIds = array();
		foreach($requestResults as $requestResult)
		{
			if (is_array($requestResult) && isset($requestResult['code']))
				$nonCriticalErrors .= $requestResult['message']."\n";
			if ($requestResult instanceof Exception)
				$nonCriticalErrors .= $requestResult->getMessage()."\n";
			if ($requestResult instanceof KalturaThumbAsset)
				$newThumbAssetsIds[] = $requestResult->id;
		}

		//delete old thumbnails if necessary
		if(!$resource && !$keepManualThumbnails  && count($newThumbAssetsIds) > 0)
			$nonCriticalErrors .= $this->removeOldThumbnails($updatedEntryId, $newThumbAssetsIds);


		//update is after add content since in case entry replacement we want the duration to be set on the new entry.
		$updatedEntry = KBatchBase::$kClient->baseEntry->update($entryId, $updateEntry);
		
		KBatchBase::unimpersonate();
		
		return array($updatedEntry, $nonCriticalErrors);
	}

	protected function removeOldThumbnails($updatedEntryId, $newThumbAssetsIds)
	{
		$filter = new KalturaAssetFilter();
		$filter->entryIdEqual = $updatedEntryId;
		$thumbList = KBatchBase::$kClient->thumbAsset->listAction($filter);

		KBatchBase::$kClient->startMultiRequest();
		foreach($thumbList->objects as $thumbAsset)
		{
			if(!in_array($thumbAsset->id, $newThumbAssetsIds))
				KBatchBase::$kClient->thumbAsset->delete($thumbAsset->id);
		}
		$requestResults = KBatchBase::$kClient->doMultiRequest();

		$nonCriticalErrors = '';
		foreach($requestResults as $requestResult)
		{
			if (is_array($requestResult) && isset($requestResult['code']))
				$nonCriticalErrors .= $requestResult['message']."\n";
			if ($requestResult instanceof Exception)
				$nonCriticalErrors .= $requestResult->getMessage()."\n";
		}
		return $nonCriticalErrors;
	}

	/**
	 * Handles xml bulk upload delete
	 * @param SimpleXMLElement $item
	 * @throws KalturaException
	 */
	protected function handleItemDelete(SimpleXMLElement $item)
	{
		$entryId = null;
		if(isset($item->entryId))
		{
			$entryId = "{$item->entryId}";
		}
		elseif(isset($item->referenceId))
		{
			$entryId = $this->getEntryIdFromReference("{$item->referenceId}");
			if(!$entryId)
				throw new KalturaBatchException("Reference id [{$item->referenceId}] not found", KalturaBatchJobAppErrors::BULK_ITEM_NOT_FOUND);
		}
		else
		{
			throw new KalturaBatchException("Missing entry id element", KalturaBatchJobAppErrors::BULK_MISSING_MANDATORY_PARAMETER);
		}
		
		KBatchBase::impersonate($this->currentPartnerId);;
		$result = KBatchBase::$kClient->baseEntry->delete($entryId);
		KBatchBase::unimpersonate();
		
		$bulkUploadResult = $this->createUploadResult($item, KalturaBulkUploadAction::DELETE);
		if($this->exceededMaxRecordsEachRun) // exit if we have proccessed max num of items
			return;
		
		$bulkUploadResult->entryId = $entryId;
		$this->addBulkUploadResult($bulkUploadResult);
	}

	/**
	 * Gets an item and insert it into the system
	 * @param SimpleXMLElement $item
	 */
	protected function handleItemAdd(SimpleXMLElement $item)
	{
		//Throw exception in case of  max proccessed items and handle all exceptions there
		$createdEntryBulkUploadResult = $this->createUploadResult($item, KalturaBulkUploadAction::ADD);
		if($this->exceededMaxRecordsEachRun) // exit if we have proccessed max num of items
			return;
		
		$entry = $this->createEntryFromItem($item); //Creates the entry from the item element
		$this->handleTypedElement($entry, $item); //Sets the typed element values (Mix, Media, ...)
		$entry->creatorId = $this->data->userId;
				
		$thumbAssets = array();
		$flavorAssets = array();
		$noParamsThumbAssets = array(); //Holds the no flavor params thumb assests
		$noParamsThumbResources = array(); //Holds the no flavor params resources assests
		$noParamsFlavorAssets = array();  //Holds the no flavor params flavor assests
		$noParamsFlavorResources = array(); //Holds the no flavor params flavor resources
		$resource = new KalturaAssetsParamsResourceContainers(); // holds all teh needed resources for the conversion
		$resource->resources = array();

		//For each content in the item element we add a new flavor asset
		if(isset($item->contentAssets))
		{
			foreach ($item->contentAssets->content as $contentElement)
			{
				$assetResource = $this->getResource($contentElement, $entry->conversionProfileId);
				if(!$assetResource)
					continue;
					
				$assetResourceContainer = new KalturaAssetParamsResourceContainer();
				$flavorAsset = $this->getFlavorAsset($contentElement, $entry->conversionProfileId);
				
				if(is_null($flavorAsset->flavorParamsId))
				{
					KalturaLog::info("flavorAsset [". print_r($flavorAsset, true) . "]");
					$noParamsFlavorAssets[] = $flavorAsset;
					$noParamsFlavorResources[] = $assetResource;
				}
				else
				{
					KalturaLog::info("flavorAsset->flavorParamsId [$flavorAsset->flavorParamsId]");
					$flavorAssets[$flavorAsset->flavorParamsId] = $flavorAsset;
					$assetResourceContainer->assetParamsId = $flavorAsset->flavorParamsId;
					$assetResourceContainer->resource = $assetResource;
					$resource->resources[] = $assetResourceContainer;
				}

				if(isset($contentElement->streams))
					$this->handleStreamsElement($contentElement->streams, $entry);
			}
		}

		//For each thumbnail in the item element we create a new thumb asset
		if(isset($item->thumbnails))
		{
			foreach ($item->thumbnails->thumbnail as $thumbElement)
			{
				$assetResource = $this->getResource($thumbElement, $entry->conversionProfileId);
				if(!$assetResource)
					continue;
					
				$assetResourceContainer = new KalturaAssetParamsResourceContainer();
				$thumbAsset = $this->getThumbAsset($thumbElement, $entry->conversionProfileId);
				
				if(is_null($thumbAsset->thumbParamsId))
				{
					KalturaLog::info("thumbAsset [". print_r($thumbAsset, true) . "]");
					$noParamsThumbAssets[] = $thumbAsset;
					$noParamsThumbResources[] = $assetResource;
				}
				else //we have a thumbParamsId so we add to the resources
				{
					KalturaLog::info("thumbAsset->thumbParamsId [$thumbAsset->thumbParamsId]");
					$thumbAssets[$thumbAsset->thumbParamsId] = $thumbAsset;
					$assetResourceContainer->assetParamsId = $thumbAsset->thumbParamsId;
					$assetResourceContainer->resource = $assetResource;
					$resource->resources[] = $assetResourceContainer;
				}
			}
		}

		if(!count($resource->resources))
		{
			if (count($noParamsFlavorResources) == 1)
			{
				$resource = reset($noParamsFlavorResources);
				$noParamsFlavorResources = array();
				$noParamsFlavorAssets = array();
			}
			else
			{
				$resource = null;
			}
		}

		list($createdEntry, $nonCriticalErrors) = $this->sendItemAddData($entry, $resource, $noParamsFlavorAssets, $noParamsFlavorResources, $noParamsThumbAssets, $noParamsThumbResources, $flavorAssets);
			
		if (isset ($item->categories))
	        $createdEntryBulkUploadResult = $this->createCategoryAssociations($createdEntry->id, $this->implodeChildElements($item->categories), $createdEntryBulkUploadResult);
		$createdEntryBulkUploadResult->errorDescription = $nonCriticalErrors;
		//Updates the bulk upload result for the given entry (with the status and other data)
		$this->updateObjectsResults(array($createdEntry), array($createdEntryBulkUploadResult));
		
		//Adds the additional data for the flavors and thumbs
		$this->handleFlavorAndThumbsAdditionalData($createdEntry->id, $flavorAssets, $thumbAssets);
				
		//Handles the plugin added data
		$pluginsErrorResults = array();
		$pluginsInstances = KalturaPluginManager::getPluginInstances('IKalturaBulkUploadXmlHandler');
		foreach($pluginsInstances as $pluginsInstance)
		{
			/* @var $pluginsInstance IKalturaBulkUploadXmlHandler */
			try {
				$pluginsInstance->configureBulkUploadXmlHandler($this);
				$pluginsInstance->handleItemAdded($createdEntry, $item);
			}catch (Exception $e)
			{
			    KBatchBase::unimpersonate();
				KalturaLog::err($pluginsInstance->getContainerName() . ' failed: ' . $e->getMessage());
				$pluginsErrorResults[] = $pluginsInstance->getContainerName() . ' failed: ' . $e->getMessage();
			}
		}
		
		if(count($pluginsErrorResults))
			throw new Exception(implode(', ', $pluginsErrorResults));
	
	}

	private function handleStreamsElement($streams, $entry)
	{
		$streamsArray = array();
		foreach ($streams->stream as $stream)
		{
			$streamContainer = new KalturaStreamContainer();
			$streamContainer->type = kXml::getXmlAttributeAsString($stream, "type");
			$streamContainer->trackIndex = kXml::getXmlAttributeAsString($stream, "trackIndex");
			$streamContainer->channelIndex = kXml::getXmlAttributeAsString($stream, "channelIndex");
			$streamContainer->channelLayout = kXml::getXmlAttributeAsString($stream, "channelLayout");
			$streamContainer->language = kXml::getXmlAttributeAsString($stream, "language");
			$streamContainer->label = kXml::getXmlAttributeAsString($stream, "label");

			$streamsArray[] = $streamContainer;
		}

		$entry->streams = $streamsArray;
	}

	/**
	 * Sends the data using a multi requsest according to the given data
	 * @param KalturaBaseEntry $entry
	 * @param KalturaResource $resource
	 * @param array $noParamsFlavorAssets
	 * @param array $noParamsFlavorResources
	 * @param array $noParamsThumbAssets
	 * @param array $noParamsThumbResources
	 * @return $requestResults - the multi request result
	 */
	protected function sendItemAddData(KalturaBaseEntry $entry ,KalturaResource $resource = null, array $noParamsFlavorAssets, array $noParamsFlavorResources, array $noParamsThumbAssets, array $noParamsThumbResources, array $flavorAssets)
	{
		KBatchBase::impersonate($this->currentPartnerId);;
		KBatchBase::$kClient->startMultiRequest();
		
		KalturaLog::info("Resource is: " . print_r($resource, true));
		
		KBatchBase::$kClient->baseEntry->add($entry); //Adds the entry
		$newEntryId = KBatchBase::$kClient->getMultiRequestResult()->id;							// TODO: use the return value of add instead of getMultiRequestResult
		
		foreach ($flavorAssets as $currFlavorAsset)
		{
			KBatchBase::$kClient->flavorAsset->add($newEntryId, $currFlavorAsset);
		}

		if($resource)
			KBatchBase::$kClient->baseEntry->addContent($newEntryId, $resource); // adds the entry resources
		
		foreach($noParamsFlavorAssets as $index => $flavorAsset) // Adds all the entry flavors
		{
			$flavorResource = $noParamsFlavorResources[$index];
			$flavor = KBatchBase::$kClient->flavorAsset->add($newEntryId, $flavorAsset);
			KBatchBase::$kClient->flavorAsset->setContent(KBatchBase::$kClient->getMultiRequestResult()->id, $flavorResource);			// TODO: use flavor instead of getMultiRequestResult
		}
		//Seperating to two multi-requests since if any of these fails we should fail all the process but if only thumbs fail it should continue fine
		$requestResults = KBatchBase::$kClient->doMultiRequest();

		foreach($requestResults as $requestResult)
		{
			if(is_array($requestResult) && isset($requestResult['code']))
				throw new KalturaException($requestResult['message'], $requestResult['code']);

			if($requestResult instanceof Exception)
				throw $requestResult;
		}

		$createdEntry = reset($requestResults);
		if(is_null($createdEntry)) //checks that the entry was created
			throw new KalturaBulkUploadXmlException("The entry wasn't created", KalturaBatchJobAppErrors::BULK_ITEM_VALIDATION_FAILED);

		if(!($createdEntry instanceof KalturaObjectBase)) // if the entry is not kaltura object (in case of errors)
			throw new KalturaBulkUploadXmlException("Returned type is [" . get_class($createdEntry) . "], KalturaObjectBase was expected", KalturaBatchJobAppErrors::BULK_ITEM_VALIDATION_FAILED);

		KBatchBase::$kClient->startMultiRequest();
		foreach($noParamsThumbAssets as $index => $thumbAsset) //Adds the entry thumb assests
		{
			
			$thumbResource = $noParamsThumbResources[$index];
			$thumb = KBatchBase::$kClient->thumbAsset->add($createdEntry->id, $thumbAsset, $thumbResource);
			KBatchBase::$kClient->thumbAsset->setContent(KBatchBase::$kClient->getMultiRequestResult()->id, $thumbResource);			// TODO: use thumb instead of getMultiRequestResult
			if (strpos($thumbAsset->tags, self::DEFAULT_THUMB_TAG) !== false)
				KBatchBase::$kClient->thumbAsset->setAsDefault($thumb->id);
		}

		$requestResults = KBatchBase::$kClient->doMultiRequest();
		KBatchBase::unimpersonate();

		$nonCriticalErrors = '';
		if($requestResults)
		{
			foreach($requestResults as $requestResult)
			{
				if (is_array($requestResult) && isset($requestResult['code']))
					$nonCriticalErrors .= $requestResult['message']."\n";
				if ($requestResult instanceof Exception)
					$nonCriticalErrors .= $requestResult->getMessage()."\n";
			}
		}
		else
			KalturaLog::log('Failed to get response from multiRequest');

		return array($createdEntry, $nonCriticalErrors);
	}
	
		/**
	 * Handles the adding of additional data to the preciously created flavors and thumbs
	 * @param int $createdEntryId
	 * @param array $flavorAssets
	 * @param array $thumbAssets
	 */
	protected function handleFlavorAndThumbsAdditionalData($createdEntryId, $flavorAssets, $thumbAssets)
	{
		KBatchBase::impersonate($this->currentPartnerId);;
		KBatchBase::$kClient->startMultiRequest();
		KBatchBase::$kClient->flavorAsset->getByEntryId($createdEntryId);
		KBatchBase::$kClient->thumbAsset->getByEntryId($createdEntryId);
		$result = KBatchBase::$kClient->doMultiRequest();
		
		foreach($result as $requestResult)
		{
			if(is_array($requestResult) && isset($requestResult['code']))
				throw new KalturaException($requestResult['message'], $requestResult['code']);
			
			if($requestResult instanceof Exception)
				throw $requestResult;
		}
		
		$createdFlavorAssets = $result[0];
		$createdThumbAssets =  $result[1];
				
		KBatchBase::$kClient->startMultiRequest();
		///For each flavor asset that we just added without his data then we need to update his additional data
		foreach($createdFlavorAssets as $createdFlavorAsset)
		{
			if(is_null($createdFlavorAsset->flavorParamsId)) //no flavor params to the flavor asset
				continue;
				
			if(!isset($flavorAssets[$createdFlavorAsset->flavorParamsId])) // We don't have the flavor in our dictionary
				continue;
			
			$flavorAsset = $flavorAssets[$createdFlavorAsset->flavorParamsId];
			KBatchBase::$kClient->flavorAsset->update($createdFlavorAsset->id, $flavorAsset);
		}
		
		foreach($createdThumbAssets as $createdThumbAsset)
		{
			if(is_null($createdThumbAsset->thumbParamsId))
				continue;
				
			if(!isset($thumbAssets[$createdThumbAsset->thumbParamsId]))
				continue;
				
			$thumbAsset = $thumbAssets[$createdThumbAsset->thumbParamsId];
			KBatchBase::$kClient->thumbAsset->update($createdThumbAsset->id, $thumbAsset);
		}
		
		$requestResults = KBatchBase::$kClient->doMultiRequest();
		KBatchBase::unimpersonate();

		if(is_array($requestResults))
			foreach($requestResults as $requestResult)
			{
				if(is_array($requestResult) && isset($requestResult['code']))
					throw new KalturaException($requestResult['message'], $requestResult['code']);
				
				if($requestResult instanceof Exception)
					throw $requestResult;
			}
		
		return $requestResults;
	}
	
	
	/**
	 * Extract replacements options from item
	 * @param SimpleXMLElement $item
	 * @return array $options
	 */
	protected function getPluginReplacementOptions(SimpleXMLElement $item)
	{
		$options = array();

		if(isset($item->pluginReplacementOptions))
		{
			if(isset($item->pluginReplacementOptions->metadataReplacementOptionsItem))
			{
				if(isset($item->pluginReplacementOptions->metadataReplacementOptionsItem->shouldCopyMetadata) && $item->pluginReplacementOptions->metadataReplacementOptionsItem->shouldCopyMetadata == 'true')
					$options['KalturaMetadataReplacementOptionsItem'] = array("shouldCopyMetadata" => true);
			}
		}
		return $options;
	}
	
	/**
	 * Handles the adding of additional data to the preciously created flavors and thumbs
	 * @param int $entryId
	 * @param KalturaBaseEntry $entry
	 * @param array $flavorAssets
	 * @param array $flavorAssetsResources
	 * @param array $thumbAssets
	 * @param array $thumbAssetsResources
	 */
	protected function sendItemUpdateData($entryId, $entry, array $flavorAssets, array $flavorAssetsResources, array $thumbAssets, array $thumbAssetsResources)
	{
		KBatchBase::impersonate($this->currentPartnerId);;
		$updateEntry = $this->removeNonUpdatbleFields($entry);
		$updatedEntry = KBatchBase::$kClient->baseEntry->update($entryId, $updateEntry);
		
		KBatchBase::$kClient->startMultiRequest();
		KBatchBase::$kClient->flavorAsset->getByEntryId($entryId);
		KBatchBase::$kClient->thumbAsset->getByEntryId($entryId);
		$result = KBatchBase::$kClient->doMultiRequest();
		
		foreach($result as $requestResult)
		{
			if(is_array($requestResult) && isset($requestResult['code']))
				throw new KalturaException($requestResult['message'], $requestResult['code']);
			
			if($requestResult instanceof Exception)
				throw $requestResult;
		}
		
		$createdFlavorAssets = $result[0];
		$createdThumbAssets =  $result[1];
		$existingflavorAssets = array();
		$existingthumbAssets = array();
				
		foreach($createdFlavorAssets as $createdFlavorAsset)
		{
			if(is_null($createdFlavorAsset->flavorParamsId)) //no flavor params to the flavor asset
				continue;
				
			$existingflavorAssets[$createdFlavorAsset->flavorParamsId] = $createdFlavorAsset->id;
		}
		
		KBatchBase::$kClient->startMultiRequest();
		foreach ($flavorAssetsResources as $flavorParamsId => $flavorAssetsResource)
		{
			if(!isset($existingflavorAssets[$flavorParamsId]))
			{
				KBatchBase::$kClient->flavorAsset->add($entryId, $flavorAssets[$flavorParamsId]);
				KBatchBase::$kClient->flavorAsset->setContent(KBatchBase::$kClient->getMultiRequestResult()->id, $flavorAssetsResource);
			}else{
				KBatchBase::$kClient->flavorAsset->update($existingflavorAssets[$flavorParamsId], $flavorAssets[$flavorParamsId]);
				KBatchBase::$kClient->flavorAsset->setContent($existingflavorAssets[$flavorParamsId], $flavorAssetsResource);
			}
		}

		$requestResults = KBatchBase::$kClient->doMultiRequest();

		foreach($requestResults as $requestResult)
		{
			if(is_array($requestResult) && isset($requestResult['code']))
				throw new KalturaException($requestResult['message'], $requestResult['code']);

			if($requestResult instanceof Exception)
				throw $requestResult;
		}

		KBatchBase::$kClient->startMultiRequest();
		foreach($createdThumbAssets as $createdThumbAsset)
		{
			if(is_null($createdThumbAsset->thumbParamsId))
				continue;
				
			$existingthumbAssets[$createdThumbAsset->thumbParamsId] = $createdThumbAsset->id;
		}
		
		
		foreach($thumbAssetsResources as $thumbParamsId => $thumbAssetsResource)
		{
			if(!isset($existingthumbAssets[$thumbParamsId]))
			{
				$thumbsAsset = KBatchBase::$kClient->thumbAsset->add($entryId, $thumbAssets[$thumbParamsId]);
				KBatchBase::$kClient->thumbAsset->setContent(KBatchBase::$kClient->getMultiRequestResult()->id, $thumbAssetsResource);
			}else{
				$thumbsAsset = KBatchBase::$kClient->thumbAsset->update($existingthumbAssets[$thumbParamsId], $thumbAssets[$thumbParamsId]);
				KBatchBase::$kClient->thumbAsset->setContent($existingthumbAssets[$thumbParamsId], $thumbAssetsResource);
			}
			if (strpos($thumbAssetsResource->tags, self::DEFAULT_THUMB_TAG) !== false)
				KBatchBase::$kClient->thumbAsset->setAsDefault($thumbsAsset->id);
		}
		
		$requestResults = KBatchBase::$kClient->doMultiRequest();
		KBatchBase::unimpersonate();

		$nonCriticalErrors = '';
		foreach($requestResults as $requestResult)
		{
			if (is_array($requestResult) && isset($requestResult['code']))
				$nonCriticalErrors .= $requestResult['message']."\n";
			if ($requestResult instanceof Exception)
				$nonCriticalErrors .= $requestResult->getMessage()."\n";
		}


		return array($updatedEntry,$nonCriticalErrors);
	}
	
	/**
	 * @param string $entryId
	 * @param string $categories comma seperated categoy full names.
	 * @param KalturaBulkUploadResultEntry $bulkuploadResult
	 * @param bool $update indicates that we are in update state and old categories that no in the list should be removed.
	 */
	protected function createCategoryAssociations($entryId, $categories, KalturaBulkUploadResultEntry $bulkuploadResult, $update = false)
	{
		// no change requested
		if(is_null($categories))
			return $bulkuploadResult;
		
		KBatchBase::impersonate($this->currentPartnerId);;
		
		$existingCategoryIds = array(); // category ids that already associated with the entry - current list
		$requiredCategoryIds = array(); // category ids that should be associated with the entry - final list
		$createdCategories = array();

		try
		{
			KBatchBase::$kClient->startMultiRequest();
			if($update)
			{
				$categoryEntryFilter = new KalturaCategoryEntryFilter();
				$categoryEntryFilter->entryIdEqual = $entryId;
				KBatchBase::$kClient->categoryEntry->listAction($categoryEntryFilter);
			}
			if($categories)
			{
				$categoryFilter = new KalturaCategoryFilter();
				$categoryFilter->fullNameIn = $categories;
				KBatchBase::$kClient->category->listAction($categoryFilter);
			}
			$responses = KBatchBase::$kClient->doMultiRequest();
			if($update)
			{
				$categoryEntryListResponse = array_shift($responses);
				/* @var $categoryEntryListResponse KalturaCategoryEntryListResponse */
				
				foreach($categoryEntryListResponse->objects as $categoryEntry)
				{
					/* @var $categoryEntry KalturaCategoryEntry */
					$existingCategoryIds[] = $categoryEntry->categoryId;
				}
			}
			
			if($categories)
			{
				$categoryListResponse = array_shift($responses);
				/* @var $categoryEntryListResponse KalturaCategoryEntryListResponse */
				
				$existingCategoryNames = array();
				foreach($categoryListResponse->objects as $category)
				{
					$existingCategoryNames[] = $category->fullName;
					$requiredCategoryIds[] = $category->id;
				}
				
				$categoryNamesArr = explode(',', $categories);
				foreach($categoryNamesArr as $categoryName)
				{
					if(!in_array($categoryName, $existingCategoryNames)) //Category does not exis
					{
						KalturaLog::info("Creating a new category by the name [$categoryName]");
						$createdCategories[] = $this->createCategoryByPath($categoryName);
					}
				}
				
				if ($createdCategories) {
					foreach($createdCategories as $createdCategory)
					{
						/* @var $createdCategory KalturaCategory */
						$requiredCategoryIds[] = $createdCategory->id; //Adding the newly created category IDs to the ToWork list
					}
				}
			}
			
			if ($update)
			{
				$categoryIdsToRemove = array_diff($existingCategoryIds, $requiredCategoryIds);
				
				if (count ($categoryIdsToRemove))
				{
					//If any of these categories are aggregation categories - the deletion will occur automatically, and is not required here.
					$categoryFilter = new KalturaCategoryFilter();
					$categoryFilter->idIn = implode(',', $categoryIdsToRemove);
					$response = KBatchBase::$kClient->category->listAction($categoryFilter);
					
					$categoriesToCheck = array();
					foreach ($response->objects as $category)
					{
						$categoriesToCheck[$category->id] = $category;
					}
				}
			}
			
			KBatchBase::$kClient->startMultiRequest();
			
			if($update) // Remove existing categories and associations
			{
				foreach($categoryIdsToRemove as $categoryIdToRemove)
				{
					if (isset ($categoriesToCheck[$categoryIdToRemove]) && $categoriesToCheck[$categoryIdToRemove]->isAggregationCategory)
					{
						KalturaLog::info ("No need to remove entry from category $categoryIdToRemove - this is an aggregation category.");
						continue;
					}
					
					KalturaLog::info("Removing category ID [$categoryIdToRemove] from entry [$entryId]");
					KBatchBase::$kClient->categoryEntry->delete($entryId, $categoryIdToRemove);
				}
			}
			
			//Add new categories and associations
			$categoryIdsToAdd = array_diff($requiredCategoryIds, $existingCategoryIds);
			foreach($categoryIdsToAdd as $categoryIdToAdd)
			{
				$categoryEntryToAdd = new KalturaCategoryEntry();
				$categoryEntryToAdd->categoryId = $categoryIdToAdd;
				$categoryEntryToAdd->entryId = $entryId;
				KalturaLog::info("Adding category ID [$categoryIdToAdd] to entry [$entryId]");
				KBatchBase::$kClient->categoryEntry->add($categoryEntryToAdd);
			}
			
			KBatchBase::$kClient->doMultiRequest();
		
		}
		catch(KalturaException $ex)
		{
			$bulkuploadResult->errorDescription .= $ex->getMessage();
		}
		
		KBatchBase::unimpersonate();
		return $bulkuploadResult;
	}
	
	private function createCategoryByPath ($fullname)
	{
        $catNames = explode(">", $fullname);
        $parentId = null;
        $fullNameEq = '';
        foreach ($catNames as $catName)
        {
            $category = new KalturaCategory();
            $category->name = $catName;
            $category->parentId = $parentId;
            
            if ($fullNameEq == '')
            	$fullNameEq .= $catName;
            else
            	$fullNameEq .= ">$catName";
            	
            try
            {
                $category = KBatchBase::$kClient->category->add($category);
            }
            catch (Exception $e)
            {
                if ($e->getCode() == 'DUPLICATE_CATEGORY')
                {
                	KalturaLog::info("Categroy [$fullNameEq] already exist");
                    $catFilter = new KalturaCategoryFilter();
                    $catFilter->fullNameEqual = $fullNameEq;
                    $res = KBatchBase::$kClient->category->listAction($catFilter);
                    $category = $res->objects[0];
                }
                else
                {
                    throw $e;
                }
            }
            
            $parentId = $category->id;
        }
        
        return $category;
	    
	}
	
	/**
	 * returns a flavor asset form the current content element
	 * @param SimpleXMLElement $contentElement
	 * @return KalturaFlavorAsset
	 */
	protected function getFlavorAsset(SimpleXMLElement $contentElement, $conversionProfileId)
	{
		$flavorAsset = new KalturaFlavorAsset(); //we create a new asset (for add)
		$flavorAsset->flavorParamsId = $this->getFlavorParamsId($contentElement, $conversionProfileId, true);
		$flavorAsset->tags = $this->implodeChildElements($contentElement->tags);
			
		return $flavorAsset;
	}
	
	/**
	 * returns a thumbnail asset form the current thumbnail element
	 * @param SimpleXMLElement $thumbElement
	 * @param int $conversionProfileId - The converrsion profile id
	 * @return KalturaThumbAsset
	 */
	protected function getThumbAsset(SimpleXMLElement $thumbElement, $conversionProfileId)
	{
		$thumbAsset = new KalturaThumbAsset();
		$thumbAsset->thumbParamsId = $this->getThumbParamsId($thumbElement, $conversionProfileId);
		
		if(isset($thumbElement["isDefault"]) && $thumbElement["isDefault"] == 'true') // if the attribute is set to true we add the is default tag to the thumb
			$thumbAsset->tags = self::DEFAULT_THUMB_TAG;
		
		$thumbAsset->tags = $this->implodeChildElements($thumbElement->tags, $thumbAsset->tags);
		
		return $thumbAsset;
	}
	
	/**
	 * Validates if the resource is valid
	 * @param KalturaResource $resource
	 * @param SimpleXMLElement $elementToSearchIn
	 */
	protected function validateResource(KalturaResource $resource = null, SimpleXMLElement $elementToSearchIn)
	{
		if(!$resource)
			return;
			
		//We only check for filesize and check sum in local files
		if($resource instanceof KalturaServerFileResource)
		{
			$filePath = $resource->localFilePath;
			
			if(is_null($filePath))
			{
				throw new KalturaBulkUploadXmlException("Can't validate file as file path is null", KalturaBatchJobAppErrors::BULK_ITEM_VALIDATION_FAILED);
			}
				
			if(isset($elementToSearchIn->serverFileContentResource->fileChecksum)) //Check checksum if exists
			{
				if($elementToSearchIn->serverFileContentResource->fileChecksum['type'] == 'sha1')
				{
					 $checksum = sha1_file($filePath);
				}
				else
				{
					$checksum = md5_file($filePath);
				}
				
				$xmlChecksum = (string)$elementToSearchIn->serverFileContentResource->fileChecksum;

				if($xmlChecksum != $checksum)
				{
					throw new KalturaBulkUploadXmlException("File checksum is invalid for file [$filePath], Xml checksum [$xmlChecksum], actual checksum [$checksum]", KalturaBatchJobAppErrors::BULK_ITEM_VALIDATION_FAILED);
				}
			}
			
			if(isset($elementToSearchIn->serverFileContentResource->fileSize)) //Check checksum if exists
			{
				$fileSize = kFile::fileSize($filePath);
				$xmlFileSize = (int)$elementToSearchIn->serverFileContentResource->fileSize;
				if($xmlFileSize != $fileSize)
				{
					throw new KalturaBulkUploadXmlException("File size is invalid for file [$filePath], Xml size [$xmlFileSize], actual size [$fileSize]", KalturaBatchJobAppErrors::BULK_ITEM_VALIDATION_FAILED);
				}
			}
		}
	}
	
	/**
	 * Gets an item and returns the resource
	 * @param SimpleXMLElement $elementToSearchIn
	 * @param int $conversionProfileId
	 * @return KalturaResource - the resource located in the given element
	 */
	public function getResource(SimpleXMLElement $elementToSearchIn, $conversionProfileId)
	{
		$resource = $this->getResourceInstance($elementToSearchIn, $conversionProfileId);
		if($resource)
			$this->validateResource($resource, $elementToSearchIn);
										
		return $resource;
	}
	
	/**
	 * Returns the right resource instance for the source content of the item
	 * @param SimpleXMLElement $elementToSearchIn
	 * @param int $conversionProfileId
	 * @return KalturaResource - the resource located in the given element
	 */
	protected function getResourceInstance(SimpleXMLElement $elementToSearchIn, $conversionProfileId)
	{
		$resource = null;
			
		if(isset($elementToSearchIn->serverFileContentResource))
		{
			if($this->allowServerResource)
			{
				$resource = new KalturaServerFileResource();
				$localContentResource = $elementToSearchIn->serverFileContentResource;
				$resource->localFilePath = kXml::getXmlAttributeAsString($localContentResource, "filePath");
			}
			else
			{
				KalturaLog::err("serverFileContentResource is not allowed");
			}
		}
		elseif(isset($elementToSearchIn->urlContentResource))
		{
			$resource = new KalturaUrlResource();
			$urlContentResource = $elementToSearchIn->urlContentResource;
			$resource->url = kXml::getXmlAttributeAsString($urlContentResource, "url");
		}
		elseif(isset($elementToSearchIn->sshUrlContentResource))
		{
			$resource = new KalturaSshUrlResource();
			$sshUrlContentResource = $elementToSearchIn->sshUrlContentResource;
			$resource->url = kXml::getXmlAttributeAsString($sshUrlContentResource, "url");
			$resource->keyPassphrase = kXml::getXmlAttributeAsString($sshUrlContentResource, "keyPassphrase");
			$resource->privateKey = strval($sshUrlContentResource->privateKey);
			$resource->publicKey = strval($sshUrlContentResource->publicKey);
		}
		elseif(isset($elementToSearchIn->remoteStorageContentResource))
		{
			$resource = new KalturaRemoteStorageResource();
			$remoteContentResource = $elementToSearchIn->remoteStorageContentResource;
			$resource->url = kXml::getXmlAttributeAsString($remoteContentResource, "url");
			$resource->storageProfileId = $this->getStorageProfileId($remoteContentResource);
		}
		elseif(isset($elementToSearchIn->remoteStorageContentResources))
		{
			$resource = new KalturaRemoteStorageResources();
			$resource->resources = array();
			$remoteContentResources = $elementToSearchIn->remoteStorageContentResources;
			
			foreach($remoteContentResources->remoteStorageContentResource as $remoteContentResource)
			{
				/* @var $remoteContentResource SimpleXMLElement */
				KalturaLog::info("Resources name [" . $remoteContentResource->getName() . "] url [" . $remoteContentResource['url'] . "] storage [$remoteContentResource->storageProfile]");
				$childResource = new KalturaRemoteStorageResource();
				$childResource->url = kXml::getXmlAttributeAsString($remoteContentResource, "url");
				$childResource->storageProfileId = $this->getStorageProfileId($remoteContentResource);
				$resource->resources[] = $childResource;
			}
		}
		elseif(isset($elementToSearchIn->entryContentResource))
		{
			$resource = new KalturaEntryResource();
			$entryContentResource = $elementToSearchIn->entryContentResource;
			$resource->entryId = kXml::getXmlAttributeAsString($entryContentResource, "entryId");
			$resource->flavorParamsId = $this->getFlavorParamsId($entryContentResource, $conversionProfileId, false);
		}
		elseif(isset($elementToSearchIn->assetContentResource))
		{
			$resource = new KalturaAssetResource();
			$assetContentResource = $elementToSearchIn->assetContentResource;
			$resource->assetId = kXml::getXmlAttributeAsString($assetContentResource, "assetId");
		}
		
		return $resource;
	}
	
	/**
	 * Gets the flavor params id from the given element
	 * @param $elementToSearchIn - The element to search in
	 * @param $conversionProfileId - The conversion profile on the item
	 * @return int - The id of the flavor params
	 */
	protected function getFlavorParamsId(SimpleXMLElement $elementToSearchIn, $conversionProfileId, $isAttribute = true)
	{
		return $this->getAssetParamsId($elementToSearchIn, $conversionProfileId, $isAttribute, 'flavor');
	}
	
	/**
	 * Validates a given asset params id for the current partner
	 * @param int $assetParamsId - The asset id
	 * @param string $assetType - The asset type (flavor or thumb)
	 * @param $conversionProfileId - The conversion profile this asset relates to
	 * @throws KalturaBatchJobAppErrors::BULK_ITEM_VALIDATION_FAILED
	 */
	protected function validateAssetParamsId($assetParamsId, $assetType, $conversionProfileId)
	{
		if(count($this->assetParamsNameToIdPerConversionProfile[$conversionProfileId]) == 0) //the name to id profiles weren't initialized
		{
			$this->initAssetParamsNameToId($conversionProfileId);
		}
		
		if(!in_array($assetParamsId, $this->assetParamsNameToIdPerConversionProfile[$conversionProfileId]))
		{
			throw new KalturaBatchException("Asset Params Id [$assetParamsId] not found for conversion profile [$conversionProfileId] ", KalturaBatchJobAppErrors::BULK_ITEM_VALIDATION_FAILED);
		}
	}
	
	/**
	 * Gets the flavor params id from the given element
	 * @param SimpleXMLElement $elementToSearchIn - The element to search in
	 * @param int $conversionProfileId - The conversion profile on the item
	 * @param bool $isAttribute
	 * @param string $assetType flavor / thumb
	 * @return int - The id of the flavor params
	 */
	public function getAssetParamsId(SimpleXMLElement $elementToSearchIn, $conversionProfileId, $isAttribute, $assetType)
	{
		$assetParams = "{$assetType}Params";
		$assetParamsId = "{$assetParams}Id";
		$assetParamsName = null;
		
		if($isAttribute)
		{
			if(isset($elementToSearchIn[$assetParamsId]))
			{
				$this->validateAssetParamsId($elementToSearchIn[$assetParamsId], $assetType, $conversionProfileId);
				return (int)$elementToSearchIn[$assetParamsId];
			}
	
			if(isset($elementToSearchIn[$assetParams]))
				$assetParamsName = $elementToSearchIn[$assetParams];
		}
		else
		{
			if(isset($elementToSearchIn->$assetParamsId))
			{
				$this->validateAssetParamsId($elementToSearchIn->$assetParamsId, $assetType, $conversionProfileId);
				return (int)$elementToSearchIn->$assetParamsId;
			}
	
			if(isset($elementToSearchIn->$assetParams))
				$assetParamsName = $elementToSearchIn->$assetParams;
		}
			
		if(!$assetParamsName)
			return null;
			
		if(!isset($this->assetParamsNameToIdPerConversionProfile[$conversionProfileId]))
			$this->initAssetParamsNameToId($conversionProfileId);
			
		if(isset($this->assetParamsNameToIdPerConversionProfile["$conversionProfileId"]["$assetParamsName"]))
			return $this->assetParamsNameToIdPerConversionProfile["$conversionProfileId"]["$assetParamsName"];
			
		throw new KalturaBatchException("{$assetParams} system name [$assetParamsName] not found for conversion profile [$conversionProfileId] ", KalturaBatchJobAppErrors::BULK_ITEM_VALIDATION_FAILED);
	}
	
	/**
	 * Gets the ingestion profile id in this order:
	 * 1.from the element 2.from the data of the bulk 3.use default)
	 * @param SimpleXMLElement $elementToSearchIn
	 */
	protected function getConversionProfileId(SimpleXMLElement $elementToSearchIn)
	{
		$conversionProfileId = $this->getConversionProfileIdFromElement($elementToSearchIn);
		
		KalturaLog::info("conversionProfileid from element [ $conversionProfileId ]");
		
		if(is_null($conversionProfileId)) // if we didn't set it in the item element
		{
			$conversionProfileId = $this->data->conversionProfileId;
			KalturaLog::info("conversionProfileid from data [ $conversionProfileId ]");
		}
		
		if(is_null($conversionProfileId)) // if we didn't set it in the item element
		{
			//Gets the user default conversion
			if(!isset($this->defaultConversionProfileId))
			{
				KBatchBase::impersonate($this->currentPartnerId);;
				$conversionProfile = KBatchBase::$kClient->conversionProfile->getDefault();
				KBatchBase::unimpersonate();
				$this->defaultConversionProfileId = $conversionProfile->id;
			}
			
			$conversionProfileId = $this->defaultConversionProfileId;
			KalturaLog::info("conversionProfileid from default [ $conversionProfileId ]");
		}
		
		return $conversionProfileId;
	}
	
	/**
	 * Validates a given conversion profile id for the current partner
	 * @param int $converionProfileId
	 * @throws KalturaBatchJobAppErrors::BULK_ITEM_VALIDATION_FAILED
	 */
	protected function validateConversionProfileId($converionProfileId)
	{
		if(count($this->conversionProfileNameToId) == 0) //the name to id profiles weren't initialized
		{
			$this->initConversionProfileNameToId();
		}
		
		if(!in_array($converionProfileId, $this->conversionProfileNameToId))
		{
			throw new KalturaBatchException("conversion profile Id [$converionProfileId] not found", KalturaBatchJobAppErrors::BULK_ITEM_VALIDATION_FAILED);
		}
	}
	
	/**
	 * Validates a given storage profile id for the current partner
	 * @param int $storageProfileId
	 * @throws KalturaBatchJobAppErrors::BULK_ITEM_VALIDATION_FAILED
	 */
	protected function validateStorageProfileId($storageProfileId)
	{
		if(count($this->storageProfileNameToId) == 0) //the name to id profiles weren't initialized
		{
			$this->initStorageProfileNameToId();
		}
		
		if(!in_array($storageProfileId, $this->storageProfileNameToId))
		{
			throw new KalturaBatchException("Storage profile id [$storageProfileId] not found", KalturaBatchJobAppErrors::BULK_ITEM_VALIDATION_FAILED);
		}
	}
	
	/**
	 * Gets the coversion profile id from the given element
	 * @param $elementToSearchIn - The element to search in
	 * @return int - The id of the ingestion profile params
	 */
	protected function getConversionProfileIdFromElement(SimpleXMLElement $elementToSearchIn)
	{
		if(isset($elementToSearchIn->conversionProfileId))
		{
			$this->validateConversionProfileId((int)$elementToSearchIn->conversionProfileId);
			return (int)$elementToSearchIn->conversionProfileId;
		}

		if(!isset($elementToSearchIn->conversionProfile))
			return null;
			
		if(!isset($this->conversionProfileNameToId["$elementToSearchIn->conversionProfile"]))
		{
			$this->initConversionProfileNameToId();
		}
			
		if(isset($this->conversionProfileNameToId["$elementToSearchIn->conversionProfile"]))
			return $this->conversionProfileNameToId["$elementToSearchIn->conversionProfile"];

		throw new KalturaBatchException("conversion profile system name [{$elementToSearchIn->conversionProfile}] not valid", KalturaBatchJobAppErrors::BULK_ITEM_VALIDATION_FAILED);
	}
		
	/**
	 * Gets the thumb params id from the given element
	 * @param $elementToSearchIn - The element to search in
	 * @param $conversionProfileId - The conversion profile id
	 * @param $isAttribute - bool
	 * @return int - The id of the thumb params
	 */
	protected function getThumbParamsId(SimpleXMLElement $elementToSearchIn, $conversionProfileId, $isAttribute = true)
	{
		return $this->getAssetParamsId($elementToSearchIn, $conversionProfileId, $isAttribute, 'thumb');
	}
		
	/**
	 * Validates a given access control id for the current partner
	 * @param int $accessControlId
	 * @throws KalturaBatchJobAppErrors::BULK_ITEM_VALIDATION_FAILED
	 */
	protected function validateAccessControlId($accessControlId)
	{
		if(count($this->accessControlNameToId) == 0) //the name to id profiles weren't initialized
		{
			$this->initAccessControlNameToId();
		}
		
		if(!in_array($accessControlId, $this->accessControlNameToId))
		{
			throw new KalturaBatchException("access control Id [$accessControlId] not valid", KalturaBatchJobAppErrors::BULK_ITEM_VALIDATION_FAILED);
		}
	}
	
	/**
	 * Gets the flavor params id from the source content element
	 * @param $elementToSearchIn - The element to search in
	 * @return int - The id of the flavor params
	 */
	protected function getAccessControlId(SimpleXMLElement $elementToSearchIn)
	{
		if(isset($elementToSearchIn->accessControlId))
		{
			$this->validateAccessControlId($elementToSearchIn->accessControlId);
			return (int)$elementToSearchIn->accessControlId;
		}

		if(!isset($elementToSearchIn->accessControl))
			return null;
			
		if(is_null($this->accessControlNameToId))
		{
			$this->initAccessControlNameToId();
		}
			
		if(isset($this->accessControlNameToId["$elementToSearchIn->accessControl"]))
			return trim($this->accessControlNameToId["$elementToSearchIn->accessControl"]);
			
		throw new KalturaBatchException("access control system name [{$elementToSearchIn->accessControl}] not found", KalturaBatchJobAppErrors::BULK_ITEM_VALIDATION_FAILED);
	}
		
	/**
	 * Gets the storage profile id from the source content element
	 * @param $elementToSearchIn - The element to search in
	 * @return int - The id of the storage profile
	 */
	protected function getStorageProfileId(SimpleXMLElement $elementToSearchIn)
	{
		if(isset($elementToSearchIn->storageProfileId))
		{
			$this->validateStorageProfileId($elementToSearchIn->storageProfileId);
			return (int)$elementToSearchIn->storageProfileId;
		}

		if(!isset($elementToSearchIn->storageProfile))
			return null;
			
		if(is_null($this->storageProfileNameToId))
		{
			$this->initStorageProfileNameToId();
		}
			
		if(isset($this->storageProfileNameToId["$elementToSearchIn->storageProfile"]))
			return trim($this->storageProfileNameToId["$elementToSearchIn->storageProfile"]);
			
		throw new KalturaBatchException("storage profile system name [{$elementToSearchIn->storageProfileId}] not found", KalturaBatchJobAppErrors::BULK_ITEM_VALIDATION_FAILED);
	}
	
	/**
	 * Inits the array of flavor params name to Id (with all given flavor params)
	 * @param $coversionProfileId - The conversion profile for which we ini the arrays for
	 */
	protected function initAssetParamsNameToId($conversionProfileId)
	{
		$conversionProfileFilter = new KalturaConversionProfileAssetParamsFilter();
		$conversionProfileFilter->conversionProfileIdEqual = $conversionProfileId;
		
		$pager = new KalturaFilterPager();
		$pager->pageSize = 500;
		
		KBatchBase::impersonate($this->currentPartnerId);;
		$allFlavorParams = KBatchBase::$kClient->conversionProfileAssetParams->listAction($conversionProfileFilter, $pager);
		KBatchBase::unimpersonate();
		$allFlavorParams = $allFlavorParams->objects;
		
		foreach ($allFlavorParams as $flavorParams)
		{
			if($flavorParams->systemName)
				$this->assetParamsNameToIdPerConversionProfile[$conversionProfileId][$flavorParams->systemName] = $flavorParams->assetParamsId;
			else //NO system name so we add them to a default name
				$this->assetParamsNameToIdPerConversionProfile[$conversionProfileId]["NO SYSTEM NAME $flavorParams->assetParamsId"] = $flavorParams->assetParamsId;
		}
	}
	
	/**
	 * Inits the array of access control name to Id (with all given flavor params)
	 */
	protected function initAccessControlNameToId()
	{
		$pager = new KalturaFilterPager();
		$pager->pageSize = 500;
		
		KBatchBase::impersonate($this->currentPartnerId);;
		$allAccessControl = KBatchBase::$kClient->accessControl->listAction(null, $pager);
		KBatchBase::unimpersonate();
		$allAccessControl = $allAccessControl->objects;
		
		foreach ($allAccessControl as $accessControl)
		{
			if($accessControl->systemName)
				$this->accessControlNameToId[$accessControl->systemName] = $accessControl->id;
			else //NO system name so we add them to a default name
				$this->accessControlNameToId["No system name " ."$accessControl->id"] = $accessControl->id;
			
		}
	}

	/**
 	 * Inits the array of access control name to Id (with all given flavor params)
 	 * @param $entryId - the entry id to take the flavor assets from
	 */
	protected function initAssetIdToAssetParamsId($entryId)
	{
		KBatchBase::impersonate($this->currentPartnerId);;
		$allFlavorAssets = KBatchBase::$kClient->flavorAsset->getByEntryId($entryId);
		$allThumbAssets = KBatchBase::$kClient->thumbAsset->getByEntryId($entryId);
		KBatchBase::unimpersonate();
						
		foreach ($allFlavorAssets as $flavorAsset)
		{
			if(!is_null($flavorAsset->id)) //Should always have an id
				$this->assetIdToAssetParamsId[$entryId][$flavorAsset->id] = $flavorAsset->flavorParamsId;
		}
		
		foreach ($allThumbAssets as $thumbAsset)
		{
			if(!is_null($thumbAsset->id)) //Should always have an id
				$this->assetIdToAssetParamsId[$entryId][$thumbAsset->id] = $thumbAsset->thumbParamsId;
		}
	}
	
	/**
	 * Inits the array of conversion profile name to Id (with all given flavor params)
	 */
	protected function initConversionProfileNameToId()
	{
		$pager = new KalturaFilterPager();
		$pager->pageSize = 500;
		
		KBatchBase::impersonate($this->currentPartnerId);;
		$allConversionProfile = KBatchBase::$kClient->conversionProfile->listAction(null, $pager);
		KBatchBase::unimpersonate();
		$allConversionProfile = $allConversionProfile->objects;
		
		foreach ($allConversionProfile as $conversionProfile)
		{
			$systemName = $conversionProfile->systemName;
			if($systemName)
				$this->conversionProfileNameToId[$systemName] = $conversionProfile->id;
			else //NO system name so we add them to a default name
				$this->conversionProfileNameToId["No system name " ."{$conversionProfile->id}"] = $conversionProfile->id;
		}
	}

	/**
	 * Inits the array of storage profile to Id (with all given flavor params)
	 */
	protected function initStorageProfileNameToId()
	{
		$pager = new KalturaFilterPager();
		$pager->pageSize = 500;
		
		KBatchBase::impersonate($this->currentPartnerId);;
		$allStorageProfiles = KBatchBase::$kClient->storageProfile->listAction(null, $pager);
		KBatchBase::unimpersonate();
		$allStorageProfiles = $allStorageProfiles->objects;
		
		foreach ($allStorageProfiles as $storageProfile)
		{
			if($storageProfile->systemName)
				$this->storageProfileNameToId["$storageProfile->systemName"] = $storageProfile->id;
			else //NO system name so we add them to a default name
				$this->storageProfileNameToId["No system name " ."{$storageProfile->id}"] = $storageProfile->id;
		}
	}
		
	/**
  	 * Creates and returns a new media entry for the given job data and bulk upload result object
	 * @param SimpleXMLElement $bulkUploadResult
	 * @param int $type
	 * @return KalturaBaseEntry
	 */
	protected function createEntryFromItem(SimpleXMLElement $item, $type = null)
	{
		//Create the new media entry and set basic values
		if(isset($item->type))
			$entryType = $item->type;
		elseif($type)
			$entryType = $type;
		else
			throw new KalturaBulkUploadXmlException("entry type must be set to a value on item [$item->name] ", KalturaBatchJobAppErrors::BULK_ITEM_VALIDATION_FAILED);
		
		$entry = $this->getEntryInstanceByType($entryType);
		$entry->type = (int)$item->type;
        $entry->userId = $this->data->userId;
		if(isset($item->referenceId))
			$entry->referenceId = (string)$item->referenceId;
		if(isset($item->name))
			$entry->name = (string)$item->name;
		if(isset($item->description))
			$entry->description = (string)$item->description;
		if(isset($item->tags))
			$entry->tags = $this->implodeChildElements($item->tags);
//		if(isset($item->categories))
//			$entry->categories = $this->implodeChildElements($item->categories);
		if(isset($item->licenseType))
			$entry->licenseType = (string)$item->licenseType;
		if(isset($item->partnerData))
			$entry->partnerData = (string)$item->partnerData;
		if(isset($item->partnerSortData))
			$entry->partnerSortValue = (string)$item->partnerSortData;
		if(isset($item->accessControlId) || isset($item->accessControl))
			$entry->accessControlId =  $this->getAccessControlId($item);
		if(isset($item->startDate))
			$entry->startDate = self::parseFormatedDate((string)$item->startDate);
		if(isset($item->endDate))
			$entry->endDate = self::parseFormatedDate((string)$item->endDate);
		if(isset($item->conversionProfileId) || isset($item->conversionProfile))
			$entry->conversionProfileId = $this->getConversionProfileId($item);
		if(($entry instanceof KalturaPlayableEntry) && isset($item->msDuration))
			$entry->msDuration = (int)$item->msDuration;
		if(isset($item->templateEntryId))
			$entry->templateEntryId = $item->templateEntryId;
		if(isset($item->templateEntry))
		{
			$templateEntryId = $this->getEntryIdFromReference("{$item->templateEntry}");
			if($templateEntryId)
				$entry->templateEntryId = $templateEntryId;
			else 
				throw new KalturaBulkUploadXmlException("Template entry id with reference id [$item->templateEntry] not found ", KalturaBatchJobAppErrors::BULK_ITEM_VALIDATION_FAILED);
		}

		if($entry->templateEntryId)
			$entry->userId = null;
		
		if(isset($item->userId))
			$entry->userId = (string)$item->userId;
		
		if(isset($item->parentReferenceId))
		{
			$parentEntryId = $this->getEntryIdFromReference("{$item->parentReferenceId}");
			if($parentEntryId)
				$entry->parentEntryId = $parentEntryId;
			else 
				throw new KalturaBulkUploadXmlException("Parent entry id with reference id [$item->parentReferenceId] not found ", KalturaBatchJobAppErrors::BULK_ITEM_VALIDATION_FAILED);
		}
		
		return $entry;
	}
	
	/**
	 * Returns the right entry instace by the given item type
	 * @param int $item
	 * @return KalturaBaseEntry
	 */
	protected function getEntryInstanceByType($type)
	{
		switch(trim($type))
		{
			case KalturaEntryType::MEDIA_CLIP :
				return new KalturaMediaEntry();
			case KalturaEntryType::DATA:
				return new KalturaDataEntry();
			case KalturaEntryType::LIVE_STREAM:
				return new KalturaLiveStreamEntry();
			case KalturaEntryType::MIX:
				return new KalturaMixEntry();
			case KalturaEntryType::PLAYLIST:
				return new KalturaPlaylist();
			default:
				return new KalturaBaseEntry();
		}
	}

	/**
	 * Handles the type additional data for the given item
	 * @param KalturaBaseEntry $media
	 * @param SimpleXMLElement $item
	 */
	protected function handleTypedElement(KalturaBaseEntry $entry, SimpleXMLElement $item)
	{
		// TODO take type from ingestion profile default entry
		switch ($entry->type)
		{
			case KalturaEntryType::MEDIA_CLIP:
				$this->setMediaElementValues($entry, $item);
				break;
				
			case KalturaEntryType::MIX:
				$this->setMixElementValues($entry, $item);
				break;
				
			case KalturaEntryType::DATA:
				$this->setDataElementValues($entry, $item);
				break;
				
			case KalturaEntryType::LIVE_STREAM:
				$this->setLiveStreamElementValues($entry, $item);
				break;
			
			case KalturaEntryType::PLAYLIST:
				$this->setPlaylistElementValues($entry, $item);
				break;
				
			default:
				$entry->type = KalturaEntryType::AUTOMATIC;
				break;
		}
	}

	/**
	 * Check if the item type and the type element are matching
	 * @param SimpleXMLElement $item
	 * @throws KalturaBatchException - KalturaBatchJobAppErrors::BULK_ITEM_VALIDATION_FAILED ;
	 */
	protected function validateTypeToTypedElement(SimpleXMLElement $item)
	{
		$typeNumber = $item->type;
		$typeNumber = trim($typeNumber);
		
		if(isset($item->media) && $item->type != KalturaEntryType::MEDIA_CLIP)
			throw new KalturaBulkUploadXmlException("Conflicted typed element for type [$typeNumber] on item [$item->name] ", KalturaBatchJobAppErrors::BULK_ITEM_VALIDATION_FAILED);
			
		if(isset($item->mix) && $item->type != KalturaEntryType::MIX)
			throw new KalturaBulkUploadXmlException("Conflicted typed element for type [$typeNumber] on item [$item->name] ", KalturaBatchJobAppErrors::BULK_ITEM_VALIDATION_FAILED);
			
		if(isset($item->playlist) && $item->type != KalturaEntryType::PLAYLIST)
			throw new KalturaBulkUploadXmlException("Conflicted typed element for type [$typeNumber] on item [$item->name] ", KalturaBatchJobAppErrors::BULK_ITEM_VALIDATION_FAILED);

		if(isset($item->liveStream) && $item->type != KalturaEntryType::LIVE_STREAM)
			throw new KalturaBulkUploadXmlException("Conflicted typed element for type [$typeNumber] on item [$item->name] ", KalturaBatchJobAppErrors::BULK_ITEM_VALIDATION_FAILED);
		
		if(isset($item->data) && $item->type != KalturaEntryType::DATA)
			throw new KalturaBulkUploadXmlException("Conflicted typed element for type [$typeNumber] on item [$item->name] ", KalturaBatchJobAppErrors::BULK_ITEM_VALIDATION_FAILED);
	}

	/**
	 * Sets the media values in the media entry according to the given item node
	 * @param KalturaMediaEntry $media
	 * @param SimpleXMLElement $itemElement
	 */
	protected function setMediaElementValues(KalturaMediaEntry $media, SimpleXMLElement $itemElement)
	{
		if(!isset($itemElement->media))
			return;
		
		$mediaElement = $itemElement->media;
		if(isset($mediaElement->mediaType))
		{
			$media->mediaType = (int)$mediaElement->mediaType;
			$this->validateMediaTypes($media->mediaType);
		}
	}

	/**
	 * Sets the playlist values in the live stream entry according to the given item node
	 * @param KalturaPlaylist $playlistEntry
	 * @param SimpleXMLElement $itemElement
	 */
	protected function setPlaylistElementValues(KalturaPlaylist $playlistEntry, SimpleXMLElement $itemElement)
	{
		$playlistElement = $itemElement->playlist;
		$playlistEntry->playlistType = (int)$playlistElement->playlistType;
		$playlistEntry->playlistContent = (string)$playlistElement->playlistContent;
	}
	
	/**
	 * Sets the live stream values in the live stream entry according to the given item node
	 * @param KalturaLiveStreamEntry $liveStreamEntry
	 * @param SimpleXMLElement $itemElement
	 */
	protected function setLiveStreamElementValues(KalturaLiveStreamEntry $liveStreamEntry, SimpleXMLElement $itemElement)
	{
		$liveStreamElement = $itemElement->liveStream;
		$liveStreamEntry->bitrates = (int)$liveStreamElement->bitrates;
		//What to do with those?
//		$liveStreamEntry->encodingIP1 = $dataElement->encodingIP1;
//		$liveStreamEntry->encodingIP2 = $dataElement->encodingIP2;
//		$liveStreamEntry->streamPassword = $dataElement->streamPassword
	}
	
	/**
	 * Sets the data values in the data entry according to the given item node
	 * @param KalturaDataEntry $dataEntry
	 * @param SimpleXMLElement $itemElement
	 */
	protected function setDataElementValues(KalturaDataEntry $dataEntry, SimpleXMLElement $itemElement)
	{
		$dataElement = $itemElement->media;
		$dataEntry->dataContent = (string)$dataElement->dataContent;
		$dataEntry->retrieveDataContentByGet = (bool)$dataElement->retrieveDataContentByGet;
	}
	
	/**
	 * Sets the mix values in the mix entry according to the given item node
	 * @param KalturaMixEntry $mix
	 * @param SimpleXMLElement $itemElement
	 */
	protected function setMixElementValues(KalturaMixEntry $mix, SimpleXMLElement $itemElement)
	{
		//TOOD: add support for the mix elements
		$mixElement = $itemElement->mix;
		$mix->editorType = $mixElement->editorType;
		$mix->dataContent = $mixElement->dataContent;
	}
		
	/**
	 * Checks if the media type and the type are valid
	 * @param KalturaMediaType $mediaType
	 */
	protected function validateMediaTypes($mediaType)
	{
		$mediaTypes = array(
			KalturaMediaType::LIVE_STREAM_FLASH,
			KalturaMediaType::LIVE_STREAM_QUICKTIME,
			KalturaMediaType::LIVE_STREAM_REAL_MEDIA,
			KalturaMediaType::LIVE_STREAM_WINDOWS_MEDIA
		);
		 
		//TODO: use this function
		if(in_array($mediaType, $mediaTypes))
			return false;
		
		if($mediaType == KalturaMediaType::IMAGE)
		{
			// TODO - make sure that there are no flavors or thumbnails in the XML
		}
		
		return true;
	}
	
	/**
	 * Adds the given media entry to the given playlists in the element
	 * @param SimpleXMLElement $playlistsElement
	 */
	protected function addToPlaylists(SimpleXMLElement $playlistsElement)
	{
		foreach ($playlistsElement->children() as $playlistElement)
		{
			//TODO: Roni - add the media to the play list not supported
			//AddToPlaylist();
		}
	}
	
	/**
	 * Returns a comma seperated string with the values of the child nodes of the given element
	 * @param SimpleXMLElement $element
	 */
	public function implodeChildElements(SimpleXMLElement $element, $baseValues = null)
	{
		$ret = array();
		if($baseValues)
			$ret = explode(',', $baseValues);
		
		if(empty($element))
			return $baseValues;
		
		foreach ($element->children() as $child)
		{
			if(is_null($child))
				continue;
				
			$value = trim("$child");
			if($value)
				$ret[] = $value;
		}
		
		$ret = implode(',', $ret);
		return $ret;
	}

	/**
	 * Gets the entry status from the given item
	 * @param unknown_type $item
	 * @return KalturaEntryStatus - the new entry status
	 */
	protected function getEntryStatusFromItem(SimpleXMLElement $item)
	{
		$status = KalturaEntryStatus::IMPORT;
		if($item->action == self::$actionsMap[KalturaBulkUploadAction::ADD])
			$status = KalturaEntryStatus::DELETED;
		
		return $status;
	}
		
	/**
	 * Creates a new upload result object from the given SimpleXMLElement item
	 * @param SimpleXMLElement $item
	 * @param KalturaBulkUploadAction $action
	 * @return KalturaBulkUploadResult
	 */
	protected function createUploadResult(SimpleXMLElement $item, $action)
	{
		if(($this->handledRecordsThisRun >= $this->maxRecordsEachRun) || ($this->maxRecords && $this->currentItem > $this->maxRecords))
		{
			$this->exceededMaxRecordsEachRun = true;
			return;
		}
		
		//TODO: What should we write in the bulk upload result for update?
		//only the changed parameters or just the one theat was changed
					
		$bulkUploadResult = new KalturaBulkUploadResultEntry();
		$bulkUploadResult->status = KalturaBulkUploadResultStatus::IN_PROGRESS;
		$bulkUploadResult->action = $action;
		$bulkUploadResult->bulkUploadJobId = $this->job->id;
		
		$bulkUploadResult->lineIndex = $this->currentItem;
		$bulkUploadResult->partnerId = $this->job->partnerId;
		$bulkUploadResult->rowData = $item->asXml();
		$bulkUploadResult->entryStatus = $this->getEntryStatusFromItem($item);
		$bulkUploadResult->conversionProfileId = $this->getConversionProfileId($item);
		$bulkUploadResult->accessControlProfileId = $this->getAccessControlId($item);
		
		if(!is_numeric($bulkUploadResult->conversionProfileId))
			$bulkUploadResult->conversionProfileId = null;
			
		if(!is_numeric($bulkUploadResult->accessControlProfileId))
			$bulkUploadResult->accessControlProfileId = null;
			
		if(isset($item->startDate))
		{
			if((string)$item->startDate && !self::isFormatedDate((string)$item->startDate))
			{
				$bulkUploadResult->entryStatus = KalturaEntryStatus::ERROR_IMPORTING;
				$bulkUploadResult->errorDescription = "Invalid schedule start date {$item->startDate} on item $item->name";
			}
		}
		
		if(isset($item->endDate))
		{
			if((string)$item->endDate && !self::isFormatedDate((string)$item->endDate))
			{
				$bulkUploadResult->entryStatus = KalturaEntryStatus::ERROR_IMPORTING;
				$bulkUploadResult->errorDescription = "Invalid schedule end date {$item->endDate} on item $item->name";
			}
		}
		
		if($bulkUploadResult->entryStatus == KalturaEntryStatus::ERROR_IMPORTING)
		{
			$this->addBulkUploadResult($bulkUploadResult);
			return;
		}
		
		$bulkUploadResult->scheduleStartDate = self::parseFormatedDate((string)$item->startDate);
		$bulkUploadResult->scheduleEndDate = self::parseFormatedDate((string)$item->endDate);
		
		return $bulkUploadResult;
	}
	
	protected function updateObjectsResults($requestResults, $bulkUploadResults)
	{
	    KBatchBase::$kClient->startMultiRequest();
		KalturaLog::info("Updating " . count($requestResults) . " results");
		
		// checking the created entries
		foreach($requestResults as $index => $requestResult)
		{
			$bulkUploadResult = $bulkUploadResults[$index];
			/**
			 * @var KalturaBulkUploadResult $bulkUploadResult
			 */
			if(is_array($requestResult) && isset($requestResult['code']))
			{
			    $bulkUploadResult->status = KalturaBulkUploadResultStatus::ERROR;
			    $bulkUploadResult->errorType = KalturaBatchJobErrorTypes::KALTURA_API;
				$bulkUploadResult->entryStatus = $requestResult['code'];
				$bulkUploadResult->errorDescription = $requestResult['message'];
				$this->addBulkUploadResult($bulkUploadResult);
				continue;
			}
			
			if($requestResult instanceof Exception)
			{
				$bulkUploadResult->entryStatus = KalturaEntryStatus::ERROR_IMPORTING;
				$bulkUploadResult->status = KalturaBulkUploadResultStatus::ERROR;
				$bulkUploadResult->errorType = KalturaBatchJobErrorTypes::KALTURA_API;
				$bulkUploadResult->errorDescription = $requestResult->getMessage();
				$this->addBulkUploadResult($bulkUploadResult);
				continue;
			}
			
			if(! ($requestResult instanceof KalturaBaseEntry))
			{
				$bulkUploadResult->entryStatus = KalturaEntryStatus::ERROR_IMPORTING;
				$bulkUploadResult->status = KalturaBulkUploadResultStatus::ERROR;
				$bulkUploadResult->errorType = KalturaBatchJobErrorTypes::KALTURA_API;
				$bulkUploadResult->errorDescription = "Returned type is " . get_class($requestResult) . ', KalturaMediaEntry was expected';
				$this->addBulkUploadResult($bulkUploadResult);
				continue;
			}
			
			// update the results with the new entry id
			$bulkUploadResult->entryId = $requestResult->id;
			$bulkUploadResult->objectId = $requestResult->id;
			$this->addBulkUploadResult($bulkUploadResult);
		}
		
		KBatchBase::$kClient->doMultiRequest();
	}
	
	public function getObjectTypeTitle()
	{
		return self::OBJECT_TYPE_TITLE;
	}

	/**
	 * @param SimpleXMLElement $item
	 * @param $entry
	 * @throws Exception
	 */
	protected function handlePluginAddedData(SimpleXMLElement $item, $entry)
	{
		//Handles the plugin added data
		$pluginsErrorResults = array();
		$pluginsInstances = KalturaPluginManager::getPluginInstances('IKalturaBulkUploadXmlHandler');
		foreach ($pluginsInstances as $pluginsInstance)
		{
			/* @var $pluginsInstance IKalturaBulkUploadXmlHandler */
			try
			{
				$pluginsInstance->configureBulkUploadXmlHandler($this);
				$pluginsInstance->handleItemUpdated($entry, $item);
			} catch (Exception $e)
			{
				KalturaLog::err($pluginsInstance->getContainerName() . ' failed: ' . $e->getMessage());
				$pluginsErrorResults[] = $pluginsInstance->getContainerName() . ' failed: ' . $e->getMessage();
			}
		}

		if (count($pluginsErrorResults))
			throw new Exception(implode(', ', $pluginsErrorResults));
	}
}
