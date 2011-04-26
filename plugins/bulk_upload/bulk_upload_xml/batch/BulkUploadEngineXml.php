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
	 * 
	 * The add action (default) string
	 * @var string
	 */
	const ADD_ACTION_STRING = "add";

	/**
	 * 
	 * The defalut thumbnail tag
	 * @var string
	 */
	const DEFAULT_THUMB_TAG = 'default_thumb';

	/**
	 * 
	 * Holds the number of the current proccessed item
	 * @var int
	 */
	private $currentItem = 0;
	
	/**
	 * 
	 * The engine xsd file path
	 * @var string
	 */
	private $xsdFilePath = "/../xml/ingestion.xsd";

	/**
	 * 
	 * Maps the flavor params name to id
	 * @var array()
	 */
	private $flavorParamsNameToIdPerConversionProfile = null;
	
	/**
	 * 
	 * Maps the thumb params name to id
	 * @var array()
	 */
	private $thumbParamsNameToIdPerConversionProfile = null;
	
	/**
	 * 
	 * Maps the access control name to id
	 * @var array()
	 */
	private $accessControlNameToId = null;
	
	/**
	 * 
	 * Maps the converstion profile name to id
	 * @var array()
	 */
	private $ingestionProfileNameToId = null;
	
	/**
	 * 
	 * Maps the storage profile name to id
	 * @var array()
	 */
	private $storageProfileNameToId = null;
	
	/* (non-PHPdoc)
	 * @see KBulkUploadEngine::HandleBulkUpload()
	 */
	public function handleBulkUpload() 
	{
		$this->validate();
	    $this->parse();
	}
	
	/**
	 * 
	 * Validates that the xml is valid using the XSD
	 *@return bool - if the validation is ok
	 */
	protected function validate() 
	{
		libxml_use_internal_errors(true);
		libxml_clear_errors();
						
		$xdoc = new DomDocument;
		$xdoc->Load($this->data->filePath);
		//Validate the XML file against the schema
		if(!$xdoc->schemaValidate(dirname(__FILE__) . $this->xsdFilePath)) 
		{
			$errorMessage = kXml::getLibXmlErrorDescription(file_get_contents($this->data->filePath));
			KalturaLog::debug("XML is invalid:\n$errorMessage");
			throw new KalturaBatchException("Validate files failed on job [{$this->job->id}], $errorMessage", KalturaBatchJobAppErrors::BULK_VALIDATION_FAILED);
		}
		
		return true;
	}

	/**
	 * 
	 * Parses the Xml file lines and creates the right actions in the system
	 */
	protected function parse() 
	{
		$xdoc = simplexml_load_file($this->data->filePath);
		
		foreach( $xdoc->channel as $channel)
		{
			KalturaLog::debug("Handling channel");
			$this->handleChannel($channel);
		}
	}

	/**
	 * 
	 * Gets and handles a channel from the mrss
	 * @param SimpleXMLElement $channel
	 */
	private function handleChannel(SimpleXMLElement $channel)
	{
		$this->currentItem = 0;
		$startIndex = $this->getStartIndex();
				
		//Gets all items from the channel
		foreach( $channel->item as $item)
		{
			if($this->currentItem < startIndex)
			{
				$this->currentItem++;
				continue;
			}
			
			$this->currentItem++; //move to the next item (first item is 1)
			try
			{
				KalturaLog::debug("Validating item [{$item->name}]");
				$this->validateItem($item);
				
				KalturaLog::debug("Handling item [{$item->name}]");
				$this->handleItem($item);
			}
			catch (KalturaBulkUploadXmlException $e)
			{
				$bulkUploadResult = $this->createUploadResult($item);
				$bulkUploadResult->errorDescription = $e->getMessage();
				$bulkUploadResult->entryStatus = KalturaEntryStatus::ERROR_IMPORTING;
				$this->addBulkUploadResult($bulkUploadResult);
				continue; // move to next item
			}			
		}	
	}
	
	/**
	 * 
	 * Validates the given item so it's valid (some validation can't be enforced in the schema)
	 * @param SimpleXMLElement $item
	 */
	private function validateItem(SimpleXMLElement $item)
	{
		//Validates that the item type has a matching type element
		$this->validateTypeToTypedElement($item);
		
		//TODO: add validation on file size and check sum
//		<xs:choice minOccurs="1" maxOccurs="1">
//			<xs:element name="fileSize" type="xs:int" minOccurs="1" maxOccurs="1"/>
//			<xs:element name="fileChecksum" type="xs:string" minOccurs="1" maxOccurs="1"/>
//		</xs:choice>
	}		
	
	/**
	 * 
	 * Gets and handles an item from the channel
	 * @param SimpleXMLElement $item
	 */
	private function handleItem(SimpleXMLElement $item)
	{
		$actionToPerform = self::ADD_ACTION_STRING;
				
		if(!empty($item->action))
			$actionToPerform = strtolower($item->action);
		
		switch($actionToPerform)
		{
			case "add":
				$this->handleItemAdd($item);
				break;
			case "update":
				$this->handleItemUpdate($item);
				break;
			case "delete":
				$this->handleItemDelete($item);
				break;
			default :
				throw new KalturaBatchException("Action: {$actionToPerform} is not supported", KalturaBatchJobAppErrors::BULK_ACTION_NOT_SUPPORTED);
		}
	}

	/**
	 * 
	 * Handles xml bulk upload update
	 * @param SimpleXMLElement $item
	 * @throws KalturaException
	 */
	private function handleItemUpdate(SimpleXMLElement $item)
	{
		throw new KalturaBatchException("Action: Update is not supported", KalturaBatchJobAppErrors::BULK_ACTION_NOT_SUPPORTED);
	}

	/**
	 * 
	 * Handles xml bulk upload delete
	 * @param SimpleXMLElement $item
	 * @throws KalturaException
	 */
	private function handleItemDelete(SimpleXMLElement $item)
	{
		throw new KalturaBatchException("Action: Delete is not supported", KalturaBatchJobAppErrors::BULK_ACTION_NOT_SUPPORTED);
	}

	/**
	 * 
	 * Gets an item and insert it into the system
	 * @param SimpleXMLElement $item
	 */
	private function handleItemAdd(SimpleXMLElement $item)
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
			$flavorAsset = $this->getFlavorAsset($contentElement, $entry->ingestionProfileId);
			if(is_null($flavorAsset))
			{
				$resource->resources[] = $this->getResource($contentElement);
				continue;
			}
			
			if(is_null($flavorAsset->flavorParamsId))
			{
				$noParamsFlavorAssets[] = $flavorAsset;
				$noParamsFlavorResources[] = $this->getResource($contentElement);
				continue;
			}
			
			$flavorAssets[$flavorAsset->flavorParamsId] = $flavorAsset;
			$assetResource = new KalturaAssetParamsResourceContainer();
			$assetResource->resource = $this->getResource($contentElement);
			$assetResource->assetParamsId = $flavorAsset->flavorParamsId;
			$resource->resources[] = $assetResource;
		}

		//For each thumbnail in the item element we create a new thumb asset
		foreach ($item->thumbnail as $thumbElement)
		{
			$thumbAsset = $this->getThumbAsset($thumbElement, $entry->ingestionProfileId);
			if(is_null($thumbAsset))
			{
				$resource->resources[] = $this->getResource($thumbElement);
				continue;
			}
			
			if(is_null($thumbAsset->thumbParamsId))
			{
				$noParamsThumbAssets[] = $thumbAsset;
				$noParamsThumbResources[] = $this->getResource($thumbElement);
				continue;
			}
			
			$thumbAssets[$thumbAsset->thumbParamsId] = $thumbAsset;
			$assetResource = new KalturaAssetParamsResourceContainer();
			$assetResource->resource = $this->getResource($thumbElement);
			$assetResource->assetParamsId = $thumbAsset->thumbParamsId;
			$resource->resources[] = $assetResource;
		}

		$createdEntry = $this->sendItemAddData($entry, $resource, $noParamsFlavorAssets, $noParamsFlavorResources, $noParamsThumbAssets, $noParamsThumbResources);
									
		//Throw exception in case of  max proccessed items and handle all exceptions there
		$createdEntryBulkUploadResult = $this->createUploadResult($item); 
				
		//Updates the bulk upload result for the given entry (with the status and other data)
		$this->updateEntriesResults(array($createdEntry), array($createdEntryBulkUploadResult));
		
		//Adds the additional data for the flavors and thumbs
		$this->handleFlavorAndThumbsAdditionalData($createdEntry->id, $flavorAssets, $thumbAssets);
				
		//Handles the plugin added data
		$pluginsInstances = KalturaPluginManager::getPluginInstances('IKalturaBulkUploadXmlHandler');
		foreach($pluginsInstances as $pluginsInstance)
			$pluginsInstance->handleItemAdded($this->kClient, $createdEntry, $item);
	}
	
	/**
	 * 
	 * Sends the data using a multi requsest according to the given data
	 * @param KalturaBaseEntry $entry
	 * @param KalturaAssetsParamsResourceContainers $resource
	 * @param array $noParamsFlavorAssets
	 * @param array $noParamsFlavorResources
	 * @param array $noParamsThumbAssets
	 * @param array $noParamsThumbResources
	 * @return $requestResults - the multi request result
	 */
	private function sendItemAddData(KalturaBaseEntry $entry ,KalturaAssetsParamsResourceContainers $resource, array $noParamsFlavorAssets, array $noParamsFlavorResources, array $noParamsThumbAssets, array $noParamsThumbResources)
	{
		$this->startMultiRequest(true);
		
		if(!count($resource->resources))
			$resource = null;
			
		$this->kClient->baseEntry->add($entry, $resource, $entry->type); // Adds the entry
		$newEntryId = "{1:result:id}";
		
		foreach($noParamsFlavorAssets as $index => $flavorAsset) // Adds all the entry flavors
		{
			$flavorResource = $noParamsFlavorResources[$index];
			$this->kClient->flavorAsset->add($newEntryId, $flavorAsset, $flavorResource);
		}
	
		foreach($noParamsThumbAssets as $index => $thumbAsset) //Adds the entry thumb assests
		{
			$thumbResource = $noParamsThumbResources[$index];
			$this->kClient->thumbAsset->add($newEntryId, $thumbAsset, $thumbResource);
		}
				
		$requestResults = $this->kClient->doMultiRequest();;
		$this->impersonate();
		
		$createdEntry = $requestResults;
		if(is_array($requestResults)) // if we got a response of array then we return the first object
		{
			$createdEntry = reset($requestResults);
		}
		
		if(is_null($createdEntry)) //checks that the entry was created
		{
			throw new KalturaBulkUploadXmlException("The entry wasn't created", KalturaBatchJobAppErrors::BULK_ITEM_VALIDATION_FAILED);
		}
		
		return $createdEntry;
	}
	
	/**
	 * 
	 * Handles the adding od additional data to the preciously created flavors and thumbs 
	 * @param int $createdEntryId
	 * @param array $flavorAssets
	 * @param array $thumbAssets
	 */
	private function handleFlavorAndThumbsAdditionalData($createdEntryId, $flavorAssets, $thumbAssets)
	{
		$this->startMultiRequest(true);
		//Gets the created thumbs and flavors
		$this->kClient->flavorAsset->getByEntryId($createdEntryId);
		$this->kClient->thumbAsset->getByEntryId($createdEntryId);
		$result = $this->kClient->doMultiRequest();
			
		$createdFlavorAssets = $result[0]; 
		$createdThumbAssets =  $result[1];
				
		$this->startMultiRequest(true);
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
		
		$requestResults = $requestResults = $this->kClient->doMultiRequest();;
				
		return $requestResults;
	}
	
	/**
	 * 
	 * returns a flavor asset form the current content element
	 * @param SimpleXMLElement $contentElement
	 * @return KalturaFlavorAsset
	 */
	private function getFlavorAsset(SimpleXMLElement $contentElement, $conversionProfileId)
	{
		$flavorAsset = new KalturaFlavorAsset();
		$flavorAsset->flavorParamsId = $this->getFlavorParamsId($contentElement, $conversionProfileId, true);
		$flavorAsset->tags = $this->getStringFromElement($contentElement->tags);
		
		if(is_null($flavorAsset->flavorParamsId) && is_null($flavorAsset->tags))
			return null;
			
		return $flavorAsset;
	}
	
	/**
	 * 
	 * returns a thumbnail asset form the current thumbnail element
	 * @param SimpleXMLElement $thumbElement
	 * @param int $conversionProfileId - The converrsion profile id 
	 * @return KalturaThumbAsset
	 */
	private function getThumbAsset(SimpleXMLElement $thumbElement, $conversionProfileId)
	{
		$thumbAsset = new KalturaThumbAsset();
		$thumbAsset->thumbParamsId = $this->getThumbParamsId($thumbElement, $conversionProfileId);
	
		if($thumbElement->isDefault)
			$thumbAsset->tags = self::DEFAULT_THUMB_TAG;
		
		$thumbAsset->tags = $this->getStringFromElement($thumbElement->tags, $thumbAsset->tags);
			
		if(is_null($thumbAsset->thumbParamsId) && is_null($thumbAsset->tags))
			return null;
			
		return $thumbAsset;
	}
	
	/**
	 * 
	 * Gets an item and returns the resource
	 * @param SimpleXMLElement $elementToSearchIn
	 * @return KalturaResource - the resource located in the given element
	 */
	private function getResource(SimpleXMLElement $elementToSearchIn)
	{
		$resource = $this->getResourceInstance($elementToSearchIn); 
				
		if(is_null($resource))
		{
			throw new KalturaBatchException("Resource is not supported: {$elementToSearchIn->asXml()}", KalturaBatchJobAppErrors::BULK_ITEM_VALIDATION_FAILED); //failed to get teh resource from the given item
		}
		
		return $resource;
	}
	
	/**
	 * 
	 * Returns the right resource instance for the source content of the item
	 * @param SimpleXMLElement $elementToSearchIn
	 * @return KalturaResource - the resource located in the given element
	 */
	private function getResourceInstance(SimpleXMLElement $elementToSearchIn)
	{
		$resource = null;
			
		if(!empty($elementToSearchIn->localFileContentResource))
		{
			KalturaLog::debug("Resource is : localFileContentResource");
			$resource = new KalturaLocalFileResource();
			$localContentResource = $elementToSearchIn->localFileContentResource;
			$resource->localFilePath = kXml::getXmlAttributeAsString($localContentResource, "filePath");
		}
		elseif(!empty($elementToSearchIn->urlContentResource))
		{
			KalturaLog::debug("Resource is : urlContentResource");
			$resource = new KalturaUrlResource();
			$urlContentResource = $elementToSearchIn->urlContentResource;
			$resource->url = kXml::getXmlAttributeAsString($urlContentResource, "url");
		}
		elseif(!empty($elementToSearchIn->remoteStorageContentResource))
		{
			KalturaLog::debug("Resource is : remoteStorageContentResource");
			$resource = new KalturaRemoteStorageResource();
			$remoteContentResource = $elementToSearchIn->remoteStorageContentResource;
			$resource->url = kXml::getXmlAttributeAsString($remoteContentResource, "url");
			$resource->storageProfileId = $this->getStorageProfileId($remoteContentResource);
		}
		elseif(!empty($elementToSearchIn->entryContentResource))
		{
			KalturaLog::debug("Resource is : entryContentResource");
			$resource = new KalturaEntryResource();
			$entryContentResource = $elementToSearchIn->entryContentResource;
			$resource->entryId = kXml::getXmlAttributeAsString($entryContentResource, "entryId");
			$resource->flavorParamsId = $this->getFlavorParamsId($entryContentResource, false);
		}
		elseif(!empty($elementToSearchIn->assetContentResource))
		{
			KalturaLog::debug("Resource is : assetContentResource");
			$resource = new KalturaAssetResource();
			$assetContentResource = $elementToSearchIn->assetContentResource;
			$resource->assetId = kXml::getXmlAttributeAsString($assetContentResource, "assetId");
		}
		
		return $resource;
	}
	
	/**
	 * 
	 * Gets the flavor params id from the given element
	 * @param $elementToSearchIn - The element to search in
	 * @param $conversionProfileId - The conversion profile on the item
	 * @return int - The id of the flavor params
	 */
	private function getFlavorParamsId(SimpleXMLElement $elementToSearchIn, $conversionProfileId, $isAttribute = true)
	{
		if($isAttribute)
		{
			if(!empty($elementToSearchIn["flavorParamsId"]))
				return (int)$elementToSearchIn["flavorParamsId"];
	
			if(empty($elementToSearchIn["flavorParams"]))
				return null;	
				
			if(is_null($this->flavorParamsNameToIdPerConversionProfile[$conversionProfileId]))
			{
				$this->initFlavorParamsNameToId($conversionProfileId);
			}
				
			if(isset($this->flavorParamsNameToIdPerConversionProfile[$conversionProfileId][$elementToSearchIn["flavorParams"]]))
				return trim($this->flavorParamsNameToIdPerConversionProfile[$conversionProfileId][$elementToSearchIn["flavorParams"]]);
		}
		else 
		{
			if(!empty($elementToSearchIn->flavorParamsId))
				return (int)$elementToSearchIn->flavorParamsId;
	
			if(empty($elementToSearchIn->flavorParams))
				return null;	
				
			if(is_null($this->flavorParamsNameToIdPerConversionProfile))
			{
				$this->initFlavorParamsNameToId($conversionProfileId);
			}
				
			if(isset($this->flavorParamsNameToIdPerConversionProfile[$elementToSearchIn->flavorParams]))
				return trim($this->flavorParamsNameToIdPerConversionProfile[$elementToSearchIn->flavorParams]);
		}
			
		return null;
	}
	
	/**
	 * 
	 * Gets the ingestion profile id in this order: 
	 * 1.from the element 2.from the data of the bulk 3.use default)
	 * @param SimpleXMLElement $elementToSearchIn
	 */
	private function getIngestionProfileId(SimpleXMLElement $elementToSearchIn)
	{
		$conversionProfileId = $this->getIngestionProfileIdFromElement($elementToSearchIn);
		
		if(is_null($conversionProfileId)) // if we didn't set it in the item element
		{
			$entry->ingestionProfileId = $this->data->conversionProfileId;
			if(is_null($conversionProfileId)) // if we didn't set it in the item element
			{
				//TODO: Get the user default conversion profile is it good?
				$this->impersonate();
				$conversionProfileId = $this->kClient->conversionProfile->getDefault();
			}
		}
		
		return $conversionProfileId;
	}
	
	/**
	 * 
	 * Gets the coversion profile id from the given element
	 * @param $elementToSearchIn - The element to search in
	 * @return int - The id of the ingestion profile params
	 */
	private function getIngestionProfileIdFromElement(SimpleXMLElement $elementToSearchIn)
	{
		if(!empty($elementToSearchIn->ingestionProfileId))
			return (int)$elementToSearchIn->ingestionProfileId;

		if(empty($elementToSearchIn->ingestionProfile))
			return null;	
			
		if(is_null($this->ingestionProfileNameToId))
		{
			$this->initIngestionProfileNameToId();
		}
			
		if(isset($this->ingestionProfileNameToId[$elementToSearchIn->ingestionProfile]))
			return trim($this->ingestionProfileNameToId[$elementToSearchIn->ingestionProfile]);
			
		return null;
	}
		
	/**
	 * 
	 * Gets the thumb params id from the given element
	 * @param $elementToSearchIn - The element to search in
	 * @param $conversionProfileId - The conversion profile id
	 * @param $isAttribute - bool
	 * @return int - The id of the thumb params
	 */
	private function getThumbParamsId(SimpleXMLElement $elementToSearchIn, $conversionProfileId, $isAttribute = true)
	{
		if(!empty($elementToSearchIn->thumbParamsId))
			return (int)$elementToSearchIn->thumbParamsId;

		if(empty($elementToSearchIn->thumbParams))
			return null;	
			
		if(is_null($this->thumbParamsNameToIdPerConversionProfile))
		{
			$this->initThumbParamsNameToId();
		}
			
		if(isset($this->thumbParamsNameToIdPerConversionProfile[$elementToSearchIn->thumbParams]))
			return trim($this->thumbParamsNameToIdPerConversionProfile[$elementToSearchIn->thumbParams]);
			
		return null;
	}
		
	/**
	 * 
	 * Gets the flavor params id from the source content element
	 * @param $elementToSearchIn - The element to search in
	 * @return int - The id of the flavor params
	 */
	private function getAccessControlId(SimpleXMLElement $elementToSearchIn)
	{
		if(!empty($elementToSearchIn->accessControlId))
			return (int)$elementToSearchIn->accessControlId;

		if(empty($elementToSearchIn->accessControl))
			return null;	
			
		if(is_null($this->accessControlNameToId))
		{
			$this->initAccessControlNameToId();
		}
			
		if(isset($this->accessControlNameToId[$elementToSearchIn->accessControl]))
			return trim($this->accessControlNameToId[$elementToSearchIn->accessControl]);
			
		return null;
	}
		
	/**
	 * 
	 * 
	 * Gets the storage profile id from the source content element
	 * @param $elementToSearchIn - The element to search in
	 * @return int - The id of the storage profile
	 */
	private function getStorageProfileId(SimpleXMLElement $elementToSearchIn)
	{
		if(!empty($elementToSearchIn->storageProfileId))
			return (int)$elementToSearchIn->storageProfileId;

		if(empty($elementToSearchIn->storageProfile))
			return null;	
			
		if(is_null($this->storageProfileNameToId))
		{
			$this->initStorageProfileNameToId();
		}
			
		if(isset($this->storageProfileNameToId[$elementToSearchIn->storageProfile]))
			return trim($this->storageProfileNameToId[$elementToSearchIn->storageProfile]);
			
		return null;
	}
	
	/**
	 * 
	 * Inits the array of flavor params name to Id (with all given flavor params)
	 * @param $coversionProfileId - The conversion profile for which we ini the arrays for
	 */
	private function initFlavorParamsNameToId($conversionProfileId)
	{
		$allFlavorParams = $this->kClient->conversionProfile->listAssetParams($conversionProfileId);
		
		foreach ($allFlavorParams as $flavorParam)
		{
			if(!is_null($flavorParam->systemName))
				$this->flavorParamsNameToIdPerConversionProfile[$conversionProfileId][$flavorParam->systemName] = $flavorParam->id;
		}
	}

	/**
	 * 
	 * Inits the array of access control name to Id (with all given flavor params)
	 */
	private function initAccessControlNameToId()
	{
		$allAccessControl = $this->kClient->accessControl->listAction(null, null);
		foreach ($allAccessControl as $accessControl)
		{
			if(!is_null($accessControl->systemName))
				$this->accessControlNameToId[$accessControl->systemName] = $accessControl->id;
		}
	}
	
	/**
	 * 
	 * Inits the array of thumb params name to Id (with all given flavor params)
	 * @param int $conversionProfileId
	 */
	private function initThumbParamsNameToId($conversionProfileId)
	{
		$allThumbParams = $this->kClient->conversionProfile->listAssetParams($conversionProfileId);
		foreach ($allThumbParams as $thumbParam) // Gets all access controls
		{
			if(!is_null($thumbParams->systemName)) 
				$this->thumbParamsNameToIdPerConversionProfile[$conversionProfileId][$thumbParams->systemName] = $thumbParams->id;
		}
	}
		
	/**
	 * 
	 * Inits the array of conversion profile name to Id (with all given flavor params)
	 */
	private function initIngestionProfileNameToId()
	{
		$allIngestionProfile = $this->kClient->conversioProfile->listAction(null, null);
		
		foreach ($allIngestionProfile as $ingestionProfile)
		{
			if(!is_null($ingestionProfile->systemName))
				$this->ingestionProfileNameToId[$ingestionProfile->systemName] = $ingestionProfile->id;
		}
	}

	/**
	 * 
	 * Inits the array of storage profile to Id (with all given flavor params)
	 */
	private function initStorageProfileNameToId()
	{
		$allStorageProfiles = $this->kClient->storageProfile->listAction(null, null);
		foreach ($allStorageProfiles as $storageProfile)
		{
			if(!is_null($storageProfile->systemName))
				$this->accessControlNameToId[$storageProfile->systemName] = $storageProfile->id;
		}
	}
		
	/**
  	 * Creates and returns a new media entry for the given job data and bulk upload result object
	 * @param SimpleXMLElement $bulkUploadResult
	 * @return KalturaBaseEntry
	 */
	private function createEntryFromItem(SimpleXMLElement $item)
	{
		//Create the new media entry and set basic values
		$entry = $this->getEntryInstanceByType($item->type);

		$entry->name = (string)$item->name;
		$entry->description = (string)$item->description;
		$entry->tags = $this->getStringFromElement($item->tags);
		$entry->categories = $this->getStringFromElement($item->categories);
		$entry->userId = (string)$item->userId;;
		$entry->licenseType = (string)$item->licenseType;
		$entry->accessControlId =  $this->getAccessControlId($item);
				
		//TODO: Roni - Parse the date
		$entry->startDate = (string)$item->startDate;
		$entry->type = (int)$item->type;
		$entry->ingestionProfileId = $this->getIngestionProfileId($item);
		
		return $entry;
	}
	
	/**
	 * 
	 * Returns the right entry instace by the given item type
	 * @param int $item
	 * @return KalturaBaseEntry 
	 */
	private function getEntryInstanceByType($type)
	{
		switch(trim($type))
		{
			case KalturaEntryType::AUTOMATIC:
			case KalturaEntryType::MEDIA_CLIP :
				return new KalturaMediaEntry();
			case KalturaEntryType::DATA:
				return new KalturaDataEntry();
			case KalturaEntryType::DOCUMENT:
				return new KalturaDocumentEntry();
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
	 * 
	 * Handles the type additional data for the given item
	 * @param KalturaBaseEntry $media
	 * @param SimpleXMLElement $item
	 */
	private function handleTypedElement(KalturaBaseEntry $entry, SimpleXMLElement $item)
	{
		switch ($entry->type)
		{
			case KalturaEntryType::AUTOMATIC:
			case KalturaEntryType::MEDIA_CLIP:
				$this->setMediaElementValues($entry, $item);
				break;
				
			case KalturaEntryType::MIX:
				$this->setMixElementValues($entry, $item);
				break;
				
			case KalturaEntryType::DATA:
				$this->setDataElementValues($entry, $item);
				break;
				
			case KalturaEntryType::DOCUMENT:
				$this->setDocumentElementValues($entry, $item);
				break;
				
			case KalturaEntryType::LIVE_STREAM:
				$this->setLiveStreamElementValues($entry, $item);
				break;
			
			case KalturaEntryType::PLAYLIST:
				$this->setPlaylistElementValues($entry, $item);
				break;
				
			default:
				throw new KalturaBatchException("Type is not supported type [$media->type]", KalturaBatchJobAppErrors::BULK_ITEM_VALIDATION_FAILED);
				break;
		}
	}

	/**
	 * 
	 * Check if the item type and the type element are matching
	 * @param SimpleXMLElement $item
	 * @throws KalturaBatchException - KalturaBatchJobAppErrors::BULK_ITEM_VALIDATION_FAILED ; 
	 */
	private function validateTypeToTypedElement(SimpleXMLElement $item) 
	{
		$typeNumber = $item->type;
		$typeNumber = trim($typeNumber);
		
		if(!empty($item->media) && $item->type != KalturaEntryType::MEDIA_CLIP)
			throw new KalturaBulkUploadXmlException("Conflicted typed element for type [$typeNumber] on item [$item->name] ", KalturaBatchJobAppErrors::BULK_ITEM_VALIDATION_FAILED);
			
		if(!empty($item->mix) && $item->type != KalturaEntryType::MIX)
			throw new KalturaBulkUploadXmlException("Conflicted typed element for type [$typeNumber] on item [$item->name] ", KalturaBatchJobAppErrors::BULK_ITEM_VALIDATION_FAILED);
			
		if(!empty($item->playlist) && $item->type != KalturaEntryType::PLAYLIST)
			throw new KalturaBulkUploadXmlException("Conflicted typed element for type [$typeNumber] on item [$item->name] ", KalturaBatchJobAppErrors::BULK_ITEM_VALIDATION_FAILED);

		if(!empty($item->document) && $item->type != KalturaEntryType::DOCUMENT)
			throw new KalturaBulkUploadXmlException("Conflicted typed element for type [$typeNumber] on item [$item->name] ", KalturaBatchJobAppErrors::BULK_ITEM_VALIDATION_FAILED);

		if(!empty($item->liveStream) && $item->type != KalturaEntryType::LIVE_STREAM)
			throw new KalturaBulkUploadXmlException("Conflicted typed element for type [$typeNumber] on item [$item->name] ", KalturaBatchJobAppErrors::BULK_ITEM_VALIDATION_FAILED);
		
		if(!empty($item->data) && $item->type != KalturaEntryType::DATA)
			throw new KalturaBulkUploadXmlException("Conflicted typed element for type [$typeNumber] on item [$item->name] ", KalturaBatchJobAppErrors::BULK_ITEM_VALIDATION_FAILED);
	}

	/**
	 * 
	 * Sets the media values in the media entry according to the given item node
	 * @param KalturaMediaEntry $media 
	 * @param SimpleXMLElement $itemElement
	 */
	private function setMediaElementValues(KalturaMediaEntry $media, SimpleXMLElement $itemElement)
	{
		$mediaElement = $itemElement->media;
		$media->mediaType = (int)$mediaElement->mediaType;
		//TODO: handle exceptions in the handle item method
		$this->validateMediaTypes($media->mediaType);
	}

	/**
	 * 
	 * Sets the playlist values in the live stream entry according to the given item node
	 * @param KalturaPlaylist $playlistEntry 
	 * @param SimpleXMLElement $itemElement
	 */
	private function setPlaylistElementValues(KalturaPlaylist $playlistEntry, SimpleXMLElement $itemElement)
	{
		$playlistElement = $itemElement->playlist;
		$playlistEntry->playlistType = (int)$playlistElement->playlistType;
		$playlistEntry->playlistContent = (string)$playlistElement->playlistContent;
	}
	
	/**
	 * 
	 * Sets the live stream values in the live stream entry according to the given item node
	 * @param KalturaLiveStreamEntry $liveStreamEntry 
	 * @param SimpleXMLElement $itemElement
	 */
	private function setLiveStreamElementValues(KalturaLiveStreamEntry $liveStreamEntry, SimpleXMLElement $itemElement)
	{
		$liveStreamElement = $itemElement->liveStream;
		$liveStreamEntry->bitrates = (int)$liveStreamElement->bitrates;
		//What to do with those?
//		$liveStreamEntry->encodingIP1 = $dataElement->encodingIP1;
//		$liveStreamEntry->encodingIP2 = $dataElement->encodingIP2;
//		$liveStreamEntry->streamPassword = $dataElement->streamPassword
	}
	
	/**
	 * 
	 * Sets the data values in the data entry according to the given item node
	 * @param KalturaDataEntry $dataEntry 
	 * @param SimpleXMLElement $itemElement
	 */
	private function setDataElementValues(KalturaDataEntry $dataEntry, SimpleXMLElement $itemElement)
	{
		$dataElement = $itemElement->media;
		$dataEntry->dataContent = (string)$dataElement->dataContent;
		$dataEntry->retrieveDataContentByGet = (bool)$dataElement->retrieveDataContentByGet;
	}
	
	/**
	 * 
	 * Sets the mix values in the mix entry according to the given item node
	 * @param KalturaMixEntry $mix 
	 * @param SimpleXMLElement $itemElement
	 */
	private function setMixElementValues(KalturaMixEntry $mix, SimpleXMLElement $itemElement)
	{
		//TOOD: add support for the mix elements
		$mixElement = $itemElement->mix;
		$mix->editorType = $mixElement->editorType;
		$mix->dataContent = $mixElement->dataContent;
	}
		
	/**
	 * 
	 * Sets the document values in the media entry according to the given media node
	 * @param KalturaDocumentEntry $media 
	 * @param SimpleXMLElement $itemElement
	 */
	private function setDocumentElementValues(KalturaDocumentEntry $document, SimpleXMLElement $itemElement)
	{
		$documentElement = $itemElement->document;
		$document->documentType = $documentElement->documentType;
	}
	
	/**
	 * 
	 * Checks if the media type and the type are valid
	 * @param KalturaMediaType $mediaType
	 */
	private function validateMediaTypes($mediaType)
	{
		//TODO: use this function
		if( $mediaType == KalturaMediaType::LIVE_STREAM_FLASH ||
			$mediaType == KalturaMediaType::LIVE_STREAM_QUICKTIME ||
			$mediaType == KalturaMediaType::LIVE_STREAM_REAL_MEDIA ||
			$mediaType == KalturaMediaType::LIVE_STREAM_WINDOWS_MEDIA )
			return false;
		
		if($mediaType == KalturaMediaType::IMAGE)
		
			// TODO - make sure that there are no flavors or thumbnails in the XML
		
		return true;
	}
	
	/**
	 * 
	 * Adds the given media entry to the given playlists in the element
	 * @param SimpleXMLElement $playlistsElement
	 */
	private function addToPlaylists(SimpleXMLElement $playlistsElement)
	{
		foreach ($playlistsElement->children() as $playlistElement)
		{
			//TODO: Roni - add the media to the play list not supported 
			//AddToPlaylist();
		}
	}
	
	/**
	 * 
	 * Returns a comma seperated string with the values of the child nodes of the given element 
	 * @param SimpleXMLElement $element
	 */
	private function getStringFromElement(SimpleXMLElement $element, $currentTags = null)
	{
		$ret = array();
		if($currentTags)
			$ret = explode(',', $currentTags);
		
		if(empty($element))
		{
			return "";
		}
		
		foreach ($element->children() as $child)
		{
			if($child != null)
			{
				$childNodeValue = trim($child);
				if(!empty($childNodeValue))
				{
					KalturaLog::debug("child value [". $childNodeValue . "]");
					$ret[] = $childNodeValue;
				}
			}
		}
		
		$ret = implode(',', $ret);
		KalturaLog::debug("The created string [$ret]");
		return $ret;
	}

	/**
	 * 
	 * Creates a new upload result object from the given SimpleXMLElement item
	 * @param SimpleXMLElement $item
	 */
	protected function createUploadResult(SimpleXMLElement $item)
	{
		if($this->handledRecordsThisRun > $this->maxRecordsEachRun)
		{
			$this->exceededMaxRecordsEachRun = true;
			return;
		}
		
		$this->handledRecordsThisRun++;
		
		$bulkUploadResult = new KalturaBulkUploadResult();
		$bulkUploadResult->bulkUploadJobId = $this->job->id;
		
		$bulkUploadResult->lineIndex = $this->currentItem;
		$bulkUploadResult->partnerId = $this->job->partnerId;
		$bulkUploadResult->rowData = $item->asXml();
		$bulkUploadResult->entryStatus = KalturaEntryStatus::IMPORT;
		$bulkUploadResult->conversionProfileId = $this->getIngestionProfileId($item);
		$bulkUploadResult->accessControlProfileId = $this->getAccessControlId($item); 
		
		if(!is_numeric($bulkUploadResult->conversionProfileId))
			$bulkUploadResult->conversionProfileId = null;
			
		if(!is_numeric($bulkUploadResult->accessControlProfileId))
			$bulkUploadResult->accessControlProfileId = null;
			
		if(!empty($item->startDate))
		{
			if((string)$item->startDate && !self::isFormatedDate((string)$item->startDate))
			{
				$bulkUploadResult->entryStatus = KalturaEntryStatus::ERROR_IMPORTING;
				$bulkUploadResult->errorDescription = "Invalid schedule start date {$item->startDate} on item $item->name";
			}
		}
		
		if(!empty($item->endDate))
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