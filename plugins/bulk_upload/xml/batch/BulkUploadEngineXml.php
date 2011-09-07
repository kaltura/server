<?php
/**
 * Class for the handling Bulk upload using XML in the system 
 * 
 * @package Scheduler
 * @subpackage Provision
 */
class BulkUploadEngineXml extends KBulkUploadEngine
{
	/**
	 * The defalut thumbnail tag
	 * @var string
	 */
	const DEFAULT_THUMB_TAG = 'default_thumb';
	
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
	 * The engine xsd file path
	 * @var string
	 */
	protected $xsdFilePath = null;
	
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
	 * @param KSchedularTaskConfig $taskConfig
	 * @param KalturaClient $kClient
	 * @param KalturaBatchJob $job
	 */
	public function __construct( KSchedularTaskConfig $taskConfig, KalturaClient $kClient, KalturaBatchJob $job)
	{
		parent::__construct($taskConfig, $kClient, $job);
		
		$this->xsdFilePath = 'http://' . kConf::get('cdn_host') . '/api_v3/index.php/service/schema/action/serve/type/' . KalturaSchemaType::BULK_UPLOAD_XML;
		if($taskConfig->params->xsdFilePath) 
			$this->xsdFilePath = $taskConfig->params->xsdFilePath;
			
		if($taskConfig->params->allowServerResource) 
			$this->allowServerResource = (bool) $taskConfig->params->allowServerResource;
	}
	
	/* (non-PHPdoc)
	 * @see KBulkUploadEngine::HandleBulkUpload()
	 */
	public function handleBulkUpload() 
	{
		$this->validate();
	    $this->parse();
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
		libxml_clear_errors();
		
		$this->loadXslt();
			
		$xdoc = new DomDocument();
		if(!$xdoc->loadXML($this->xslTransform($this->data->filePath))){
			$errorMessage = 'Could not load transformed xsl';
			KalturaLog::debug("Could not load transformed xsl");
			throw new KalturaBatchException("Could not load transformed xsl [{$this->job->id}], $errorMessage", KalturaBatchJobAppErrors::BULK_VALIDATION_FAILED);
		}
		//Validate the XML file against the schema
		if(!$xdoc->schemaValidate($this->xsdFilePath)) 
		{
			$errorMessage = kXml::getLibXmlErrorDescription(file_get_contents($this->xslTransform($this->data->filePath)));
			KalturaLog::debug("XML is invalid:\n$errorMessage");
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
		$conversionProfileId = $data->conversionProfileId;
		if($data->conversionProfileId == -1){
			$conversionProfileId = PartnerPeer::retrieveByPK($this->currentPartnerId)->getDefaultConversionProfileId();
		}
		
		$this->impersonate();
		$conversionProfile = $this->kClient->conversionProfile->get($conversionProfileId);
		$this->unimpersonate();
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
			
		$xml = new DOMDocument();
		if(!$xml->loadXML($xdoc)){
			KalturaLog::debug("Could not load xml");
			return $xdoc;
		}
		
		$proc = new XSLTProcessor;
		$xsl = new DOMDocument();
		if(!$xsl->loadXML($this->conversionProfileXsl)){
			KalturaLog::debug("Could not load xsl".$this->conversionProfileXsl);
			return $xdoc;
		}
		$proc->importStyleSheet($xsl);
		
		KalturaLog::debug("transformed xml ".$proc->transformToXML($xml));
		return $proc->transformToXML($xml);
	}
	
	/**
	 * Parses the Xml file lines and creates the right actions in the system
	 */
	protected function parse()
	{
		$xdoc = new SimpleXMLElement($this->xslTransform($this->data->filePath));
		
		foreach( $xdoc->channel as $channel)
		{
//			KalturaLog::debug("Handling channel");
			$this->handleChannel($channel);
			if($this->exceededMaxRecordsEachRun) // exit if we have proccessed max num of items
				return;
		}
	}

	/**
	 * Gets and handles a channel from the mrss
	 * @param SimpleXMLElement $channel 
	 */
	protected function handleChannel(SimpleXMLElement $channel)
	{
		$this->currentItem = 0;
		$startIndex = $this->getStartIndex();
		KalturaLog::debug("startIndex [$startIndex]");
		
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
//				KalturaLog::debug("Validating item [{$item->name}]");
				$this->validateItem($item);
			
				$this->checkAborted();
				
				$actionToPerform = self::$actionsMap[KalturaBulkUploadAction::ADD];
						
				$action = KalturaBulkUploadAction::ADD;
				if(isset($item->action))
					$actionToPerform = strtolower($item->action);
				
				switch($actionToPerform)
				{
					case self::$actionsMap[KalturaBulkUploadAction::ADD]:
						$this->handleItemAdd($item);
						$action = KalturaBulkUploadAction::ADD;
						break;
					case self::$actionsMap[KalturaBulkUploadAction::UPDATE]:
						$this->handleItemUpdate($item);
						$action = KalturaBulkUploadAction::UPDATE;
						break;
					case self::$actionsMap[KalturaBulkUploadAction::DELETE]:
						$this->handleItemDelete($item);
						$action = KalturaBulkUploadAction::DELETE;
						break;
					default :
						throw new KalturaBatchException("Action: {$actionToPerform} is not supported", KalturaBatchJobAppErrors::BULK_ACTION_NOT_SUPPORTED);
				}
			}
			catch (KalturaBulkUploadXmlException $e)
			{
				KalturaLog::err("Item failed because excpetion was raised': " . $e->getMessage());
				$bulkUploadResult = $this->createUploadResult($item, $action);
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
		$entry->conversionProfileId = null;
		return $entry;
	}
	
	/**
	 * Handles xml bulk upload update
	 * @param SimpleXMLElement $item
	 * @throws KalturaException
	 */
	protected function handleItemUpdate(SimpleXMLElement $item)
	{
		KalturaLog::debug("xml [" . $item->asXML() . "]");
		
		$entryId = null;
		if(isset($item->entryId))
		{
			$entryId = "{$item->entryId}";
		}
		elseif(isset($item->referenceId))
		{
			$referenceId = "{$item->referenceId}";
			$filter = new KalturaBaseEntryFilter();
			$filter->referenceIdEqual = $referenceId;
			$pager = new KalturaFilterPager();
			$pager->pageSize = 1;
			
			$this->impersonate();
			$entries = $this->kClient->baseEntry->listAction($filter, $pager);
			$this->unimpersonate();
			
			/* @var $entries KalturaBaseEntryListResponse */
			if(!$entries->totalCount)
				throw new KalturaBatchException("Reference id [$referenceId] not found", KalturaBatchJobAppErrors::BULK_ITEM_NOT_FOUND);
			
			$existingEntry = reset($entries->objects);
			$entryId = $existingEntry->id;
		}
		else
		{
			throw new KalturaBatchException("Missing entry id element", KalturaBatchJobAppErrors::BULK_MISSING_MANDATORY_PARAMETER);
		}

		$entry = $this->createEntryFromItem($item); //Creates the entry from the item element
		$entry = $this->removeNonUpdatbleFields($entry);
		
		$this->handleTypedElement($entry, $item); //Sets the typed element values (Mix, Media, ...)
		KalturaLog::debug("current entry is: " . print_r($entry, true));
				
		$thumbAssets = array();
		$flavorAssets = array();
		$noParamsThumbAssets = array(); //Holds the no flavor params thumb assests
		$noParamsThumbResources = array(); //Holds the no flavor params resources assests
		$noParamsFlavorAssets = array();  //Holds the no flavor params flavor assests
		$noParamsFlavorResources = array(); //Holds the no flavor params flavor resources
		$resource = new KalturaAssetsParamsResourceContainers(); // holds all teh needed resources for the conversion
		$resource->resources = array();
			
		//For each content in the item element we add a new flavor asset
		foreach ($item->content as $contentElement)
		{
			KalturaLog::debug("contentElement [" . print_r($contentElement->asXml(), true). "]");
			
			if(empty($contentElement)) // if the content is empty skip
			{
				continue;
			}
							
			$flavorAsset = $this->getFlavorAsset($contentElement, $entry->conversionProfileId);
			$flavorAssetResource = $this->getResource($contentElement, $entry->conversionProfileId);
			if(!$flavorAssetResource)
				continue;
			
			$assetParamsId = $flavorAsset->flavorParamsId;

			$assetId = kXml::getXmlAttributeAsString($contentElement, "assetId");
			if($assetId) // if we have an asset id then we need to update the asset
			{
				KalturaLog::debug("Asset id [ $assetId]");
				$assetParamsId = $this->getAssetParamsIdFromAssetId($assetId, $entryId);
			}
		
			KalturaLog::debug("assetParamsId [$assetParamsId]");
						
			if(is_null($assetParamsId)) // no params resource
			{
				$noParamsFlavorAssets[] = $flavorAsset;
				$noParamsFlavorResources[] = $flavorAssetResource;
				continue;
			}
			
			$flavorAssets[$flavorAsset->flavorParamsId] = $flavorAsset;
			$assetResource = new KalturaAssetParamsResourceContainer();
			$assetResource->resource = $flavorAssetResource;
			$assetResource->assetParamsId = $assetParamsId;
			$resource->resources[] = $assetResource;
		}

		//For each thumbnail in the item element we create a new thumb asset
		foreach ($item->thumbnail as $thumbElement)
		{
			if(empty($thumbElement)) // if the content is empty
			{
				continue;
			}
			
			KalturaLog::debug("thumbElement [" . print_r($thumbElement->asXml(), true). "]");
						
			$thumbAsset = $this->getThumbAsset($thumbElement, $entry->conversionProfileId);
			$thumbAssetResource = $this->getResource($thumbElement, $entry->conversionProfileId);
			if(!$thumbAssetResource)
				continue;
									
			$assetParamsId = $thumbAsset->thumbParamsId;

			$assetId = kXml::getXmlAttributeAsString($thumbElement, "assetId");
			if($assetId) // if we have an asset id then we need to update the asset
			{
				KalturaLog::debug("Asset id [ $assetId]");
				$assetParamsId = $this->getAssetParamsIdFromAssetId($assetId, $entryId);
			}
						
			KalturaLog::debug("assetParamsId [$assetParamsId]");
			
			if(is_null($assetParamsId))
			{
				$noParamsThumbAssets[] = $thumbAsset;
				$noParamsThumbResources[] = $thumbAssetResource;
				continue;
			}
			
			$thumbAssets[$thumbAsset->thumbParamsId] = $thumbAsset;
			$assetResource = new KalturaAssetParamsResourceContainer();
			$assetResource->resource = $thumbAssetResource;
			$assetResource->assetParamsId = $assetParamsId;
			$resource->resources[] = $assetResource;
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

		$updatedEntry = $this->sendItemUpdateData($entryId, $entry, $resource, 
												  $noParamsFlavorAssets, $noParamsFlavorResources, 
												  $noParamsThumbAssets, $noParamsThumbResources);
												  
		//Adds the additional data for the flavors and thumbs
		$this->handleFlavorAndThumbsAdditionalData($updatedEntry->id, $flavorAssets, $thumbAssets);
				
		//Handles the plugin added data
		$pluginsInstances = KalturaPluginManager::getPluginInstances('IKalturaBulkUploadXmlHandler');
		foreach($pluginsInstances as $pluginsInstance)
		{
			/* @var $pluginsInstance IKalturaBulkUploadXmlHandler */
			$pluginsInstance->configureBulkUploadXmlHandler($this);
			$pluginsInstance->handleItemUpdated($updatedEntry, $item);
		}
	
		//Throw exception in case of max proccessed items and handle all exceptions there
		$updatedEntryBulkUploadResult = $this->createUploadResult($item, KalturaBulkUploadAction::UPDATE);
		
		//Updates the bulk upload result for the given entry (with the status and other data)
		$this->updateEntriesResults(array($updatedEntry), array($updatedEntryBulkUploadResult));
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
	 * @return $requestResults - the multi request result
	 */
	protected function sendItemUpdateData($entryId, KalturaBaseEntry $entry ,KalturaResource $resource = null, 
										array $noParamsFlavorAssets, array $noParamsFlavorResources, 
										array $noParamsThumbAssets, array $noParamsThumbResources)
	{
		
		KalturaLog::debug("Resource is: " . print_r($resource, true));
		
		$this->impersonate();
		$updatedEntry = $this->kClient->baseEntry->update($entryId, $entry);
		
		$this->kClient->startMultiRequest();
		
		$updatedEntryId = $updatedEntry->id;   	
		
		if(!is_null($updatedEntry->replacingEntryId))
		{
			$updatedEntryId = $updatedEntry->replacingEntryId;
		}
					
		if($resource)
			$this->kClient->baseEntry->updateContent($updatedEntryId ,$resource);
		
		foreach($noParamsFlavorAssets as $index => $flavorAsset) // Adds all the entry flavors
		{
			$flavorResource = $noParamsFlavorResources[$index];
			$flavor = $this->kClient->flavorAsset->add($updatedEntryId, $flavorAsset);
			$this->kClient->flavorAsset->setContent($this->kClient->getMultiRequestResult()->id, $flavorResource);		// TODO: use flavor instead of getMultiRequestResult
		}

		foreach($noParamsThumbAssets as $index => $thumbAsset) //Adds the entry thumb assests
		{
			$thumbResource = $noParamsThumbResources[$index];
			$thumb = $this->kClient->thumbAsset->add($updatedEntryId, $thumbAsset);
			$this->kClient->thumbAsset->setContent($this->kClient->getMultiRequestResult()->id, $thumbResource);		// TODO: use thumb instead of getMultiRequestResult
		}
		
		$requestResults = $this->kClient->doMultiRequest();
		$this->unimpersonate();
			
		//TODO: handle the update array
		KalturaLog::debug("Updated entry [". print_r($updatedEntry,true) ."]");
		
		//Make thi closer to request
		if(is_null($updatedEntry)) //checks that the entry was created
		{
			throw new KalturaBulkUploadXmlException("The entry wasn't created requestResults [$requestResults]", KalturaBatchJobAppErrors::BULK_ITEM_VALIDATION_FAILED);
		}
		
		return $updatedEntry;
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
			$referenceId = "{$item->referenceId}";
			$filter = new KalturaBaseEntryFilter();
			$filter->referenceIdEqual = $referenceId;
			$pager = new KalturaFilterPager();
			$pager->pageSize = 1;
			
			$this->impersonate();
			$entries = $this->kClient->baseEntry->listAction($filter, $pager);
			$this->unimpersonate();
			
			/* @var $entries KalturaBaseEntryListResponse */
			if(!$entries->totalCount)
				throw new KalturaBatchException("Reference id [$referenceId] not found", KalturaBatchJobAppErrors::BULK_ITEM_NOT_FOUND);
				
			$existingEntry = reset($entries->objects);
			$entryId = $existingEntry->id;
		}
		else
		{
			throw new KalturaBatchException("Missing entry id element", KalturaBatchJobAppErrors::BULK_MISSING_MANDATORY_PARAMETER);
		}
		
		$this->impersonate();
		$result = $this->kClient->baseEntry->delete($entryId);
		$this->unimpersonate();
		
		$bulkUploadResult = $this->createUploadResult($item, KalturaBulkUploadAction::DELETE);
		$bulkUploadResult->entryId = $entryId;
		$this->addBulkUploadResult($bulkUploadResult);
	}

	/**
	 * Gets an item and insert it into the system
	 * @param SimpleXMLElement $item
	 */
	protected function handleItemAdd(SimpleXMLElement $item)
	{
		KalturaLog::debug("xml [" . $item->asXML() . "]");
			
		$entry = $this->createEntryFromItem($item); //Creates the entry from the item element
		$this->handleTypedElement($entry, $item); //Sets the typed element values (Mix, Media, ...)
		KalturaLog::debug("current entry is: " . print_r($entry, true));
				
		$thumbAssets = array();
		$flavorAssets = array();
		$noParamsThumbAssets = array(); //Holds the no flavor params thumb assests
		$noParamsThumbResources = array(); //Holds the no flavor params resources assests
		$noParamsFlavorAssets = array();  //Holds the no flavor params flavor assests
		$noParamsFlavorResources = array(); //Holds the no flavor params flavor resources
		$resource = new KalturaAssetsParamsResourceContainers(); // holds all teh needed resources for the conversion
		$resource->resources = array();
		
		//For each content in the item element we add a new flavor asset
		foreach ($item->content as $contentElement)
		{
			$assetResource = $this->getResource($contentElement, $entry->conversionProfileId);
			if(!$assetResource)
				continue;
				
			$assetResourceContainer = new KalturaAssetParamsResourceContainer();
			$flavorAsset = $this->getFlavorAsset($contentElement, $entry->conversionProfileId);
			
			if(is_null($flavorAsset->flavorParamsId))
			{
				KalturaLog::debug("flavorAsset [". print_r($flavorAsset, true) . "]");
				$noParamsFlavorAssets[] = $flavorAsset;
				$noParamsFlavorResources[] = $assetResource;
			}
			else 
			{
				KalturaLog::debug("flavorAsset->flavorParamsId [$flavorAsset->flavorParamsId]");
				$flavorAssets[$flavorAsset->flavorParamsId] = $flavorAsset;
				$assetResourceContainer->assetParamsId = $flavorAsset->flavorParamsId;
				$assetResourceContainer->resource = $assetResource;
				$resource->resources[] = $assetResourceContainer;
			}
		}

		//For each thumbnail in the item element we create a new thumb asset
		foreach ($item->thumbnail as $thumbElement)
		{
			$assetResource = $this->getResource($thumbElement, $entry->conversionProfileId);
			if(!$assetResource)
				continue;
				
			$assetResourceContainer = new KalturaAssetParamsResourceContainer();
			$thumbAsset = $this->getThumbAsset($thumbElement, $entry->conversionProfileId);
			
			if(is_null($thumbAsset->thumbParamsId))
			{
				KalturaLog::debug("thumbAsset [". print_r($thumbAsset, true) . "]");
				$noParamsThumbAssets[] = $thumbAsset;
				$noParamsThumbResources[] = $assetResource;
			}
			else //we have a thumbParamsId so we add to the resources
			{
				KalturaLog::debug("thumbAsset->thumbParamsId [$thumbAsset->thumbParamsId]");
				$thumbAssets[$thumbAsset->thumbParamsId] = $thumbAsset;
				$assetResourceContainer->assetParamsId = $thumbAsset->thumbParamsId;
				$assetResourceContainer->resource = $assetResource;
				$resource->resources[] = $assetResourceContainer;
			}
		}

		//Throw exception in case of  max proccessed items and handle all exceptions there
		$createdEntryBulkUploadResult = $this->createUploadResult($item, KalturaBulkUploadAction::ADD);

		if($this->exceededMaxRecordsEachRun) // exit if we have proccessed max num of items
			return;
			
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

		$createdEntry = $this->sendItemAddData($entry, $resource, $noParamsFlavorAssets, $noParamsFlavorResources, $noParamsThumbAssets, $noParamsThumbResources);
			
		//Updates the bulk upload result for the given entry (with the status and other data)
		$this->updateEntriesResults(array($createdEntry), array($createdEntryBulkUploadResult));
		
		//Adds the additional data for the flavors and thumbs
		$this->handleFlavorAndThumbsAdditionalData($createdEntry->id, $flavorAssets, $thumbAssets);
				
		//Handles the plugin added data
		$pluginsInstances = KalturaPluginManager::getPluginInstances('IKalturaBulkUploadXmlHandler');
		foreach($pluginsInstances as $pluginsInstance)
		{
			/* @var $pluginsInstance IKalturaBulkUploadXmlHandler */
			$pluginsInstance->configureBulkUploadXmlHandler($this);
			$pluginsInstance->handleItemAdded($createdEntry, $item);
		}
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
	protected function sendItemAddData(KalturaBaseEntry $entry ,KalturaResource $resource = null, array $noParamsFlavorAssets, array $noParamsFlavorResources, array $noParamsThumbAssets, array $noParamsThumbResources)
	{	
		$this->impersonate();
		$this->kClient->startMultiRequest();
		
		KalturaLog::debug("Resource is: " . print_r($resource, true));
		
		$this->kClient->baseEntry->add($entry); //Adds the entry 
		$newEntryId = $this->kClient->getMultiRequestResult()->id;							// TODO: use the return value of add instead of getMultiRequestResult
		
		if($resource)
			$this->kClient->baseEntry->addContent($newEntryId, $resource); // adds the entry resources
		
		foreach($noParamsFlavorAssets as $index => $flavorAsset) // Adds all the entry flavors
		{
			$flavorResource = $noParamsFlavorResources[$index];
			$flavor = $this->kClient->flavorAsset->add($newEntryId, $flavorAsset);
			$this->kClient->flavorAsset->setContent($this->kClient->getMultiRequestResult()->id, $flavorResource);			// TODO: use flavor instead of getMultiRequestResult
		}
	
		foreach($noParamsThumbAssets as $index => $thumbAsset) //Adds the entry thumb assests
		{
			$thumbResource = $noParamsThumbResources[$index];
			$thumb = $this->kClient->thumbAsset->add($newEntryId, $thumbAsset, $thumbResource);
			$this->kClient->thumbAsset->setContent($this->kClient->getMultiRequestResult()->id, $thumbResource);			// TODO: use thumb instead of getMultiRequestResult
		}
							
		$requestResults = $this->kClient->doMultiRequest();;
		$this->unimpersonate();
		
		$createdEntry = reset($requestResults);
		
		if(is_null($createdEntry)) //checks that the entry was created
		{
			throw new KalturaBulkUploadXmlException("The entry wasn't created", KalturaBatchJobAppErrors::BULK_ITEM_VALIDATION_FAILED);
		}
		
		if(!($createdEntry instanceof KalturaObjectBase)) // if the entry is not kaltura object (in case of errors)
		{
			throw new KalturaBulkUploadXmlException("The entry wasn't created requestResults [$requestResults]", KalturaBatchJobAppErrors::BULK_ITEM_VALIDATION_FAILED);
		}
		
		if(!isset($createdEntry->id) || empty($createdEntry->id)) //checks that the entry id was set and it is not empty
		{
			throw new KalturaBulkUploadXmlException("The entry id [$createdEntry->id] wasn't set", KalturaBatchJobAppErrors::BULK_ITEM_VALIDATION_FAILED);
		}
		
		return $createdEntry;
	}
	
	/**
	 * Handles the adding od additional data to the preciously created flavors and thumbs 
	 * @param int $createdEntryId
	 * @param array $flavorAssets
	 * @param array $thumbAssets
	 */
	protected function handleFlavorAndThumbsAdditionalData($createdEntryId, $flavorAssets, $thumbAssets)
	{
		$this->impersonate();
		$this->kClient->startMultiRequest();
		$this->kClient->flavorAsset->getByEntryId($createdEntryId);
		$this->kClient->thumbAsset->getByEntryId($createdEntryId);
		$result = $this->kClient->doMultiRequest();
			
		$createdFlavorAssets = $result[0]; 
		$createdThumbAssets =  $result[1];
				
		$this->kClient->startMultiRequest();
		///For each flavor asset that we just added without his data then we need to update his additional data
		foreach($createdFlavorAssets as $createdFlavorAsset)
		{
			if(is_null($createdFlavorAsset->flavorParamsId)) //no flavor params to the flavor asset
				continue;
				
			if(!isset($flavorAssets[$createdFlavorAsset->flavorParamsId])) // We don't have the flavor in our dictionary
				continue;
				
			$flavorAsset = $flavorAssets[$createdFlavorAsset->flavorParamsId];
			$this->kClient->flavorAsset->update($createdFlavorAsset->id, $flavorAsset);
		}
			
		foreach($createdThumbAssets as $createdThumbAsset)
		{
			if(is_null($createdThumbAsset->thumbParamsId))
				continue;
				
			if(!isset($thumbAssets[$createdThumbAsset->thumbParamsId]))
				continue;
				
			$thumbAsset = $thumbAssets[$createdThumbAsset->thumbParamsId];
			$this->kClient->thumbAsset->update($createdThumbAsset->id, $thumbAsset);
		}
		
		$requestResults = $this->kClient->doMultiRequest();
		$this->unimpersonate();
				
		return $requestResults;
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
				KalturaLog::debug("Validating checksum");
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
				KalturaLog::debug("Validating file size");
				
				$fileSize = filesize($filePath);
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
				KalturaLog::debug("Resource is : serverFileContentResource");
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
			KalturaLog::debug("Resource is : urlContentResource");
			$resource = new KalturaUrlResource();
			$urlContentResource = $elementToSearchIn->urlContentResource;
			$resource->url = kXml::getXmlAttributeAsString($urlContentResource, "url");
		}
		elseif(isset($elementToSearchIn->remoteStorageContentResource))
		{
			KalturaLog::debug("Resource is : remoteStorageContentResource");
			$resource = new KalturaRemoteStorageResource();
			$remoteContentResource = $elementToSearchIn->remoteStorageContentResource;
			$resource->url = kXml::getXmlAttributeAsString($remoteContentResource, "url");
			$resource->storageProfileId = $this->getStorageProfileId($remoteContentResource);
		}
		elseif(isset($elementToSearchIn->remoteStorageContentResources))
		{
			KalturaLog::debug("Resource is : remoteStorageContentResources");
			$resource = new KalturaRemoteStorageResources();
			$resource->resources = array();
			$remoteContentResources = $elementToSearchIn->remoteStorageContentResources;
			
			foreach($remoteContentResources->remoteStorageContentResource as $remoteContentResource)
			{
				/* @var $remoteContentResource SimpleXMLElement */
				KalturaLog::debug("Resources name [" . $remoteContentResource->getName() . "] url [" . $remoteContentResource['url'] . "] storage [$remoteContentResource->storageProfile]");
				$childResource = new KalturaRemoteStorageResource();
				$childResource->url = kXml::getXmlAttributeAsString($remoteContentResource, "url");
				$childResource->storageProfileId = $this->getStorageProfileId($remoteContentResource);
				$resource->resources[] = $childResource;
			}
		}
		elseif(isset($elementToSearchIn->entryContentResource))
		{
			KalturaLog::debug("Resource is : entryContentResource");
			$resource = new KalturaEntryResource();
			$entryContentResource = $elementToSearchIn->entryContentResource;
			$resource->entryId = kXml::getXmlAttributeAsString($entryContentResource, "entryId");
			$resource->flavorParamsId = $this->getFlavorParamsId($entryContentResource, $conversionProfileId, false);
		}
		elseif(isset($elementToSearchIn->assetContentResource))
		{
			KalturaLog::debug("Resource is : assetContentResource");
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
		
		KalturaLog::debug("conversionProfileid from element [ $conversionProfileId ]");
		
		if(is_null($conversionProfileId)) // if we didn't set it in the item element
		{
			$conversionProfileId = $this->data->conversionProfileId;
			KalturaLog::debug("conversionProfileid from data [ $conversionProfileId ]");
		}
		
		if(is_null($conversionProfileId)) // if we didn't set it in the item element
		{
			//Gets the user default conversion
			if(!isset($this->defaultConversionProfileId))
			{
				$this->impersonate();
				$conversionProfile = $this->kClient->conversionProfile->getDefault();
				$this->unimpersonate();
				$this->defaultConversionProfileId = $conversionProfile->id;
			}
			
			$conversionProfileId = $this->defaultConversionProfileId;
			KalturaLog::debug("conversionProfileid from default [ $conversionProfileId ]"); 
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
		
		$this->impersonate();
		$allFlavorParams = $this->kClient->conversionProfileAssetParams->listAction($conversionProfileFilter);
		$this->unimpersonate();
		$allFlavorParams = $allFlavorParams->objects;
		
//		KalturaLog::debug("allFlavorParams [" . print_r($allFlavorParams, true). "]");
		
		foreach ($allFlavorParams as $flavorParams)
		{
			if($flavorParams->systemName)
				$this->assetParamsNameToIdPerConversionProfile[$conversionProfileId][$flavorParams->systemName] = $flavorParams->assetParamsId;
			else //NO system name so we add them to a default name
				$this->assetParamsNameToIdPerConversionProfile[$conversionProfileId]["NO SYSTEM NAME $flavorParams->assetParamsId"] = $flavorParams->assetParamsId;
		}
		
//		KalturaLog::debug("new assetParamsNameToIdPerConversionProfile [" . print_r($this->assetParamsNameToIdPerConversionProfile, true). "]");
	}
	
	/**
	 * Inits the array of access control name to Id (with all given flavor params)
	 */
	protected function initAccessControlNameToId()
	{
		$this->impersonate();
		$allAccessControl = $this->kClient->accessControl->listAction(null, null);
		$this->unimpersonate();
		$allAccessControl = $allAccessControl->objects;
		
//		KalturaLog::debug("allAccessControl [" . print_r($allAccessControl, true). "]");
		
		foreach ($allAccessControl as $accessControl)
		{
			if($accessControl->systemName)
				$this->accessControlNameToId[$accessControl->systemName] = $accessControl->id;
			else //NO system name so we add them to a default name
				$this->accessControlNameToId["No system name " ."$accessControl->id"] = $accessControl->id;
			
		}
		
//		KalturaLog::debug("new accessControlNameToId [" . print_r($this->accessControlNameToId, true). "]");
	}

	/**
 	 * Inits the array of access control name to Id (with all given flavor params)
 	 * @param $entryId - the entry id to take the flavor assets from
	 */
	protected function initAssetIdToAssetParamsId($entryId)
	{
		$this->impersonate();
		$allFlavorAssets = $this->kClient->flavorAsset->getByEntryId($entryId);
		$allThumbAssets = $this->kClient->thumbAsset->getByEntryId($entryId);
		$this->unimpersonate();
						
//		KalturaLog::debug("allFlavorAssets [" . print_r($allFlavorAssets, true). "]");
//		KalturaLog::debug("allThumbAssets [" . print_r($allThumbAssets, true). "]");
		
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
		
//		KalturaLog::debug("new assetIdToAssetParamsId [" . print_r($this->assetIdToAssetParamsId, true). "]");
	}
	
	/**
	 * Inits the array of conversion profile name to Id (with all given flavor params)
	 */
	protected function initConversionProfileNameToId()
	{
		$this->impersonate();
		$allConversionProfile = $this->kClient->conversionProfile->listAction(null, null);
		$this->unimpersonate();
		$allConversionProfile = $allConversionProfile->objects;
		
//		KalturaLog::debug("allConversionProfile [" . print_r($allConversionProfile,true) ." ]");
		
		foreach ($allConversionProfile as $conversionProfile)
		{
			$systemName = $conversionProfile->systemName;
			if($systemName)
				$this->conversionProfileNameToId[$systemName] = $conversionProfile->id;
			else //NO system name so we add them to a default name
				$this->conversionProfileNameToId["No system name " ."{$conversionProfile->id}"] = $conversionProfile->id;
		}
		
//		KalturaLog::debug("new conversionProfileNameToId [" . print_r($this->conversionProfileNameToId, true). "]");
	}

	/**
	 * Inits the array of storage profile to Id (with all given flavor params)
	 */
	protected function initStorageProfileNameToId()
	{
		$this->impersonate();
		$allStorageProfiles = $this->kClient->storageProfile->listAction(null, null);
		$this->unimpersonate();
		$allStorageProfiles = $allStorageProfiles->objects;
		
//		KalturaLog::debug("allStorageProfiles [" . print_r($allStorageProfiles,true) ." ]");
		
		foreach ($allStorageProfiles as $storageProfile)
		{
			if($storageProfile->systemName)
				$this->storageProfileNameToId["$storageProfile->systemName"] = $storageProfile->id;
			else //NO system name so we add them to a default name
				$this->storageProfileNameToId["No system name " ."{$storageProfile->id}"] = $storageProfile->id;	
		}
		
//		KalturaLog::debug("new storageProfileNameToId [" . print_r($this->storageProfileNameToId, true). "]");
	}
		
	/**
  	 * Creates and returns a new media entry for the given job data and bulk upload result object
	 * @param SimpleXMLElement $bulkUploadResult
	 * @return KalturaBaseEntry
	 */
	protected function createEntryFromItem(SimpleXMLElement $item)
	{
		//Create the new media entry and set basic values
		$entry = $this->getEntryInstanceByType($item->type);

		$entry->type = (int)$item->type;
		
		if(isset($item->referenceId))
			$entry->referenceId = (string)$item->referenceId;
		if(isset($item->name))
			$entry->name = (string)$item->name;
		if(isset($item->description))
			$entry->description = (string)$item->description;
		if(isset($item->tags))
			$entry->tags = $this->implodeChildElements($item->tags);
		if(isset($item->categories))
			$entry->categories = $this->implodeChildElements($item->categories);
		if(isset($item->userId))
			$entry->userId = (string)$item->userId;;
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
		$mediaElement = $itemElement->media;
		$media->mediaType = (int)$mediaElement->mediaType;
		$this->validateMediaTypes($media->mediaType);
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
		KalturaLog::debug("The created string [$ret]");
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
		//TODO: What should we write in the bulk upload result for update? 
		//only the changed parameters or just the one theat was changed
//		KalturaLog::debug("Creating upload result");
		KalturaLog::debug("this->handledRecordsThisRun [$this->handledRecordsThisRun], this->maxRecordsEachRun [$this->maxRecordsEachRun]");
					
		$bulkUploadResult = new KalturaBulkUploadResult();
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
}
