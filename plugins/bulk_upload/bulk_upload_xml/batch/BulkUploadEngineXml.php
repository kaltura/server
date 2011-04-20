<?php
/**
 * Class for the handling Bulk upload using XML in the system 
 * 
 * @package Scheduler
 * @subpackage Provision
 */
class BulkUploadEngineXml extends KBulkUploadEngine
{
	//(not the final version i still checking the code please don't kill me :) )
	const ADD_ACTION_STRING = "add";
	 
	/**
	 * 
	 * The engine xsd file path
	 * @var string
	 */
	private $xsdFilePath = "/../lib/ingestion.xsd";

	/**
	 * 
	 * The result of the mutlirequest for the item 
	 * @var unknown_type
	 */
	private $requestResults = null;
	
	/**
	 * 
	 * Holds all the bulk upload results
	 * @var array<KalturaBulkUploadResult>
	 * @todo consider to delete it
	 */
	private $bulkUploadResults = array();
	 
	/**
	 * 
	 * The current proccessed entry
	 * @var KalturaMediaEntry
	 * @todo consider to pass within functions
	 */
	private $entry = null;
	
	/**
	 * 
	 * The current item flavor assests
	 * @var array<KalturaFlavorAssest>
	 * @todo consider to pass within functions
	 */
	private $flavorAssets = array();
	
	/**
	 * 
	 * The current thumb assests
	 * @var array<KalturaThumbAssest>
	 * @todo consider to pass within functions
	 */
	private $thumbAssets = array();

	/**
	 * The thumb assets resuorces
	 * @var array<KalturaResource>
	 * @todo consider to pass within functions
	 */
	private $thumbResources = array();
	
	/**
	 * The thumb assets resuorces
	 * @var array<KalturaResource> 
	 * @todo consider to pass within functions
	 */
	private $flavorResources = array();

	/**
	 * 
	 * The typed elemenet (the additional data needed for the element)
	 * such as: Media, Mix ...
	 * @var unknown_type
	 * @todo try to manage without it
	 */
	private $typedElement = null;
	
	/**
	 * 
	 * Maps the flavor params name to id
	 * @var array()
	 */
	private $flavorParamsNameToId = null;
	
	/**
	 * 
	 * Maps the thumb params name to id
	 * @var array()
	 */
	private $thumbParamsNameToId = null;
	
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
	private $conversionProfileNameToId = null;
	
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
	 * @todo check err desc like in kMetadataManager::validateMetadata
	 */
	protected function validate() 
	{
		$xdoc = new DomDocument;
		$xdoc->Load($this->data->filePath);
		//Validate the XML file against the schema
		if(!$xdoc->schemaValidate(dirname(__FILE__) . $this->xsdFilePath)) 
		{
			throw new KalturaBatchException("Validate files failed on job [{$this->job->id}]", KalturaBatchJobAppErrors::BULK_VALIDATION_FAILED);
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
		//Gets all items from the channel
		foreach( $channel->item as $item)
		{
			KalturaLog::debug("Validating item [{$item->name}]");
			try{
				$this->validateItem($item);
			}
			catch (BulkUploadXmlException $e)
			{
				// TODO create bulk upload results with falure
			}
			
			//TODO: add check if the bulk count has reached its max size and send the data
			KalturaLog::debug("Handling item [{$item->name}]");
			$this->handleItem($item);
		}	
	}
	
	/**
	 * 
	 * Validates the given item so it's valid (some validation can't be enforced in the schema)
	 * @param SimpleXMLElement $item
	 * @return bool
	 */
	private function validateItem(SimpleXMLElement $item)
	{
		//Validates that the item type has a matching type element
		if(!$this->validateTypeToTypedElement($item))
			return false;
			
		return true;
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
	 * Initializes all item relevant data structures
	 */
	private function initForItem()
	{
		$this->flavorAssets = array();
		$this->entry = null;
		$this->thumbAssets = array();
		$this->thumbResources = array();
		$this->flavorResources = array();
		$this->bulkUploadResults = array();
	}
	
	/**
	 * 
	 * Gets an item and insert it into the system
	 * @param SimpleXMLElement $item
	 */
	private function handleItemAdd(SimpleXMLElement $item)
	{
		KalturaLog::debug("xml [" . $item->asXML() . "]");
		$this->initForItem();
	
		$this->entry = $this->createEntryFromItem($item);
		KalturaLog::debug("current entry is: " .var_dump($this->entry));
		
		//Handles the type element additional data 
		$this->handleTypedElement($item);

		$thumbAssets = array();
		$flavorAssets = array();
		
		$noParamsThumbAssets = array();
		$noParamsThumbResources = array();
		$noParamsFlavorAssets = array();
		$noParamsFlavorResources = array();
		
		$resource = new KalturaAssetsParamsResourceContainers();
		$resource->resources = array();
		
		//For each content in the item element we add a new flavor asset
		foreach ($item->content as $contentElement)
		{
			$flavorAsset = $this->getFlavorAsset($contentElement);
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
			$thumbAsset = $this->getThumbAsset($thumbElement);
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
		
		if(!count($resource->resources))
			$resource = null;
			
		$this->startMultiRequest(true);
		$this->kClient->baseEntry->add($this->entry, $resource, $this->entry->type);
		$newEntryId = "{1:result:id}";
		
		foreach($noParamsFlavorAssets as $index => $flavorAsset)
		{
			$flavorResource = $noParamsFlavorResources[$index];
			$this->kClient->flavorAsset->add($newEntryId, $flavorAsset, $flavorResource);
		}
	
		foreach($noParamsThumbAssets as $index => $thumbAsset)
		{
			$thumbResource = $noParamsThumbResources[$index];
			$this->kClient->thumbAsset->add($newEntryId, $thumbAsset, $thumbResource);
		}
		
		$requestResults = $this->doMultiRequestForPartner();
		$createdEntry = reset($requestResults);
		
		$this->updateEntriesResults(array($createdEntry), $this->bulkUploadResults);
		
		$createdFlavorAssets = $this->kClient->flavorAsset->getByEntryId($createdEntry->id);
		foreach($createdFlavorAssets as $createdFlavorAsset)
		{
			if(is_null($createdFlavorAsset->flavorParamsId))
				continue;
				
			if(!isset($flavorAssets[$createdFlavorAsset->flavorParamsId]))
				continue;
				
			$flavorAsset = $flavorAssets[$createdFlavorAsset->flavorParamsId];
			$this->kClient->flavorAsset->update($createdFlavorAsset->id, $flavorAsset);
		}
	
		$createdThumbAssets = $this->kClient->thumbAsset->getByEntryId($createdEntry->id);
		foreach($createdThumbAssets as $createdThumbAsset)
		{
			if(is_null($createdThumbAsset->thumbParamsId))
				continue;
				
			if(!isset($thumbAssets[$createdThumbAsset->thumbParamsId]))
				continue;
				
			$thumbAsset = $thumbAssets[$createdThumbAsset->thumbParamsId];
			$this->kClient->thumbAsset->update($createdThumbAsset->id, $thumbAsset);
		}
		
		$pluginsInstances = KalturaPluginManager::getPluginInstances('IKalturaBulkUploadXmlHandler');
		foreach($pluginsInstances as $pluginsInstance)
			$pluginsInstance->handleAddedItem($createdEntry, $item);
	}
	
	/**
	 * 
	 * returns a flavor asset form the current content element
	 * @param SimpleXMLElement $contentElement
	 * @return KalturaFlavorAsset
	 */
	private function getFlavorAsset(SimpleXMLElement $contentElement)
	{
		$flavorAsset = new KalturaFlavorAsset();
		$flavorAsset->flavorParamsId = $this->getFlavorParamsId($contentElement);
		$flavorAsset->tags = $this->getStringFromElement($contentElement->tags);
		
		if(is_null($flavorAsset->flavorParamsId) && is_null($flavorAsset->tags))
			return null;
			
		return $flavorAsset;
	}
	
	/**
	 * 
	 * returns a thumbnail asset form the current thumbnail element
	 * @param SimpleXMLElement $thumbElement
	 * @return KalturaThumbAsset
	 */
	private function getThumbAsset(SimpleXMLElement $thumbElement)
	{
		$thumbAsset = new KalturaThumbAsset();
		$thumbAsset->thumbParamsId = $this->getThumbParamsId($thumbElement);
	
		// TODO check if it works
		if($thumbElement->isDefault)
			$thumbAsset->tags = 'default_thumb'; // TODO use const
		
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
		KalturaLog::debug("In getResourceInstance");
		
		$resource = null;
			
		if(!empty($elementToSearchIn->localFileContentResource))
		{
			KalturaLog::debug("Resource is : localFileContentResource");
			$resource = new KalturaLocalFileResource();
			$localContentResource = $elementToSearchIn->localFileContentResource;
			$resource->localFilePath = kXml::getXmlAttributeAsString($localContentResource, "filePath");
			
			//TODO: Roni - what to do with those?
//		<xs:choice minOccurs="1" maxOccurs="1">
//			<xs:element name="fileSize" type="xs:int" minOccurs="1" maxOccurs="1"/>
//			<xs:element name="fileChecksum" type="xs:string" minOccurs="1" maxOccurs="1"/>
//		</xs:choice>
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
	 * @return int - The id of the flavor params
	 */
	private function getFlavorParamsId(SimpleXMLElement $elementToSearchIn, $isAttribute = true)
	{
		if($isAttribute) //Gets value from attributes
		{
			$flavorParamsId = kXml::getXmlAttributeAsString($elementToSearchIn, "flavorParamsId"); 
			$flavorParamsName = kXml::getXmlAttributeAsString($elementToSearchIn,"flavorParams");
		}
		else //Gets value from elements
		{
			$flavorParamsId = (string)$elementToSearchIn->flavorParamsId; 
			$flavorParamsName = (string)$elementToSearchIn->flavorParams;
		}
			
		return $this->getFlavorParamsByIdAndName($flavorParamsId, $flavorParamsName);
	}
	
	/**
	 * 
	 * Gets the coversion profile id from the given element
	 * @param $elementToSearchIn - The element to search in
	 * @return int - The id of the flavor params
	 */
	private function getConversionProfileId(SimpleXMLElement $elementToSearchIn, $isAttribute = true)
	{
		if($isAttribute) //Gets value from attributes
		{
			$conversionProfileId = kXml::getXmlAttributeAsString($elementToSearchIn, "conversionProfileId"); 
			$conversionProfileName = kXml::getXmlAttributeAsString($elementToSearchIn,"conversioProfile");
		}
		else //Gets value from elements
		{
			$conversionProfileId = (string)$elementToSearchIn->conversionProfileId; 
			$conversionProfileName = (string)$elementToSearchIn->conversionProfile;
		}
			
		return $this->getConversionProfileByIdAndName($conversionProfileId, $conversionProfileName);
	}
		
	/**
	 * 
	 * Gets the thumb params id from the given element
	 * @param $elementToSearchIn - The element to search in
	 * @return int - The id of the thumb params
	 * @todo enable to return null
	 */
	private function getThumbParamsId(SimpleXMLElement $elementToSearchIn, $isAttribute = true)
	{
		if($isAttribute) //Gets value from attributes
		{
			$thumbParamsId = kXml::getXmlAttributeAsString($elementToSearchIn, "thumbParamsId"); 
			$thumbParamsName = kXml::getXmlAttributeAsString($elementToSearchIn,"thumbParams");
		}
		else //Gets value from elements
		{
			$thumbParamsId = (string)$elementToSearchIn->thumbParamsId; 
			$thumbParamsName = (string)$elementToSearchIn->thumbParams;
		}
			
		return $this->getThumbParamsByIdAndName($thumbParamsId, $thumbParamsName);
	}
	
	/**
	 * 
	 * Returns from the given object it's flavor params ids
	 * @param SimpleXMLElement $elementToSearchIn
	 */
	private function getFlavorParamsIds(SimpleXMLElement $elementToSearchIn)
	{
		KalturaLog::debug("In getFlavorParamsIds");
		
		$flavorParamsIds = "";
		
		//Can be null
		$flavorParamsIdsElement = $elementToSearchIn->flavorParamsIds;
		 
		if(is_null($flavorParamsIdsElement)) // id is null so we get by names
		{
			//get the names
			$flavorParamsElement = $elementToSearchIn->flavorParam;
			$flavorParamsNamesArray = explode(",", $this->getStringFromElement($flavorParamsElement));
			
			//Load the names to id array
			$this->initFlavorParamsNameToId();
			
			foreach ($flavorParamsNamesArray as $flavorParamName) 
			{
				if(!is_null($flavorParamName) || empty($flavorParamName))
				{
					$flavorParamsIds = $flavorParamsIds . trim($this->flavorParamsNameToId[$flavorParamName]) .',';
				}
				KalturaLog::debug("Flavor params name [$flavorParamName]");
			}
		}
		else
		{
			$flavorParamsIds = $this->getStringFromElement($flavorParamsIdsElement);
		}
		
		KalturaLog::debug("In getFlavorParamsIds - flavorParamsIds [$flavorParamsIds]");
		return $flavorParamsIds;
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

		if(empty($elementToSearchIn->accessControl)) // TODO rephrase
			throw new KalturaBatchException("Can't find access control with empty name and empty id", KalturaBatchJobAppErrors::BULK_ITEM_VALIDATION_FAILED);
			
		if(is_null($this->accessControlNameToId))
		{
			$allAccessControl = $this->kClient->accessControl->listAction(null, null);
			foreach ($allAccessControl as $accessControl)
			{
				if(!is_null($accessControl->systemName)) // TODO the same for the others
					$this->accessControlNameToId[$accessControl->systemName] = $accessControl->id;
			}
		}
			
		if(isset($this->accessControlNameToId[$elementToSearchIn->accessControl]))
			return trim($this->accessControlNameToId[$elementToSearchIn->accessControl]);
			
		throw new KalturaBatchException("Can't find access control with name [$elementToSearchIn->accessControl]", KalturaBatchJobAppErrors::BULK_ITEM_VALIDATION_FAILED);
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
		$storageProfileId = kXml::getXmlAttributeAsString($elementToSearchIn, "storageProfileId"); 
		$storageProfileName = kXml::getXmlAttributeAsString($elementToSearchIn, "storageProfile");
			
		//TODO: implement this (after validation of the flavor params)
		return $this->getStorageProfileByIdAndName($storageProfileId, $storageProfileName);
	}
	
	/**
	 * 
	 * Gets the storage profile id by it's id or name   
	 * @param $storageProfileId - the storage profile id
	 * @param string $storageProfileName - the storage profile system name
	 * @return int - The id of the storage profile 
	 * @throws KalturaBatchException - in case ther is not such storage profile with the given name
	 */
	private function getStorageProfileByIdAndName($storageProfileId, $storageProfileName)
	{
		if(!empty($storageProfileId) || $storageProfileId == 0 || $storageProfileId == '0')
		{
			return trim($storageProfileId);
			
		}
		
		if(!empty($storageProfileName))//if we have no id then we search by name
		{
			if(is_null($this->storageProfileNameToId))
			{
				$this->initStorageProfileNameToId();
			}
			
			if(isset($this->storageProfileNameToId[$storageProfileName]))
			{
				return trim($this->storageProfileNameToId[$storageProfileName]);
			}
		}

		//If we got here then the id or name weren't found
		throw new KalturaBatchException("Can't find storage profile with id [$storageProfileId], name [$storageProfileName]", KalturaBatchJobAppErrors::BULK_ITEM_VALIDATION_FAILED);
	}
	
	/**
	 * 
	 * Gets the conversion profile id by it's id or name   
	 * @param $conversionProfileId - the conversion profile id
	 * @param $conversionProfileName - the conversion profile name
	 * @return int - The id of the conversion profile
	 * @throws KalturaBatchException - in case there is not flavor params by the given name
	 */
	private function getConversionProfileByIdAndName($conversionProfileId, $conversionProfileName)
	{
		KalturaLog::info("In getConversionProfileByIdAndName - conversionProfileId [$conversionProfileId] conversionProfileName [$conversionProfileId]");
		
		if(!empty($conversionProfileId) ||  $conversionProfileId == 0 || $conversionProfileId == '0')
		{
			return trim($conversionProfileId);
		}
		
		if(!empty($conversionProfileName)) //If we have no id then we search by name
		{
			if(is_null($this->conversionProfileNameToId))
			{
				$this->initFlavorParamsNameToId();
			}
			
			if(isset($this->conversionProfileNameToId[$conversionProfileName]))
			{
				return trim($this->conversionProfileNameToId[$conversionProfileName]);
			}
		}

		//If we got here then the id or name weren't found
		throw new KalturaBatchException("Can't find conversion profile with id [$conversionProfileId], name [$conversionProfileName]", KalturaBatchJobAppErrors::BULK_ITEM_VALIDATION_FAILED);
	}
	
	/**
	 * 
	 * Gets the flavor params id by it's id or name   
	 * @param $flavorParamsId - the flavor params id
	 * @param $flavorParamsName - the flavor params name
	 * @return int - The id of the flaovr params
	 * @throws KalturaBatchException - in case there is not flavor params by the given name
	 */
	private function getFlavorParamsByIdAndName($flavorParamsId, $flavorParamsName)
	{
		KalturaLog::info("In getFlavorParamsByIdAndName - flavorParamsId [$flavorParamsId] flavorParamsName [$flavorParamsName]");
		
		if(!empty($flavorParamsId) ||  $flavorParamsId == 0 || $flavorParamsId == '0')
		{
			return trim($flavorParamsId);
		}
		
		if(!empty($flavorParamsName)) //If we have no id then we search by name
		{
			if(is_null($this->flavorParamsNameToId))
			{
				$this->initFlavorParamsNameToId();
			}
			
			if(isset($this->flavorParamsNameToId[$flavorParamsName]))
			{
				return trim($this->flavorParamsNameToId[$flavorParamsName]);
			}
		}

		//If we got here then the id or name weren't found
		throw new KalturaBatchException("Can't find flavor params with id [$flavorParamsId], name [$flavorParamsName]", KalturaBatchJobAppErrors::BULK_ITEM_VALIDATION_FAILED);
	}
	
	/**
	 * 
	 * Gets the thumb params id by it's id or name   
	 * @param $thumbParamsId - the thumb params id
	 * @param $thumbParamsName - the thumb params name
	 * @return int - The id of the thumb params
	 * @throws KalturaBatchException - in case there is not thumb params by the given name
	 */
	private function getThumbParamsByIdAndName($thumbParamsId, $thumbParamsName)
	{
		if(!empty($thumbParamsId) || $thumbParamsId == 0 || $thumbParamsId == '0')
		{
			return trim($thumbParamsId);
		}
		
		if(!empty($thumbParamsName)) //If we have no id then we search by name
		{
			if(is_null($this->thumbParamsNameToId))
			{
				$this->initThumbParamsNameToId();
			}
			
			if(isset($this->thumbParamsNameToId[$thumbParamsName]))
			{
				return trim($this->thumbParamsNameToId[$thumbParamsName]);
			}
		}

		//If we got here then the id or name weren't found
		throw new KalturaBatchException("Can't find thumb params with id [], name [$thumbParamsName]", KalturaBatchJobAppErrors::BULK_ITEM_VALIDATION_FAILED);
	}
	
	/**
	 * 
	 * Inits the array of flavor params name to Id (with all given flavor params)
	 */
	private function initFlavorParamsNameToId()
	{
		$allFlavorParams = $this->kClient->flavorParams->listAction(null, null);
		
		foreach ($allFlavorParams as $flavorParam)
		{
			$this->flavorParamsNameToId[$flavorParam->systemName] = $flavorParam->id;
		}
	}

	/**
	 * 
	 * Inits the array of access control name to Id (with all given flavor params)
	 */
	private function initAccessControlNameToId()
	{
	}
	
	/**
	 * 
	 * Inits the array of thumb params name to Id (with all given flavor params)
	 */
	private function initThumbParamsNameToId()
	{
		$allThumbParams = $this->kClient->thumbParams->listAction(null, null);
		
		foreach ($allThumbParams as $thumbParam)
		{
			$this->thumbParamsNameToId[$thumbParam->systemName] = $thumbParam->id;
		}
	}
		
	/**
	 * 
	 * Inits the array of conversion profile name to Id (with all given flavor params)
	 */
	private function initConversionProfileNameToId()
	{
		$allConversionProfile = $this->kClient->conversioProfile->listAction(null, null);
		
		foreach ($allConversionProfile as $conversionProfile)
		{
			$this->conversionProfileNameToId[$conversionProfile->systemName] = $conversionProfile->id;
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
			$this->storageProfileNameToId[$storageProfile->systemName] = $storageProfile->id;
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
		// TODO switch $item->type
		$mediaEntry = new KalturaMediaEntry();

		$mediaEntry->name = (string)$item->name;
		$mediaEntry->description = (string)$item->description;
		$mediaEntry->tags = $this->getStringFromElement($item->tags);
		$categoriesElement = $item->categories;
		$mediaEntry->categories = $this->getStringFromElement($categoriesElement);
		$mediaEntry->userId = (string)$item->userId;;
		$mediaEntry->licenseType = (string)$item->licenseType;
		$mediaEntry->accessControlId =  $this->getAccessControlId($item);
				
		//TODO: Roni - Parse the date
		$mediaEntry->startDate = $item->startDate;
		$mediaEntry->type = $this->getEntryTypeByNumber($item->type);
		$mediaEntry->ingestionProfileId = $this->getConversionProfileId($item);	
		
		return $mediaEntry;
	}
	
	/**
	 * 
	 * Handles the type additional data for the given item
	 * @param SimpleXMLElement $item
	 */
	private function handleTypedElement(SimpleXMLElement $item)
	{
		//TODO: handle other types / use switch
		if($this->entry->type == KalturaEntryType::MEDIA_CLIP)
		{
			$mediaElement = $item->media;
			$this->setMediaElementValues($mediaElement);
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
		if(!empty($item->media) && $item->type != KalturaEntryType::MEDIA_CLIP)
			throw new KalturaBatchException("Conflicted typed element for type [$typeNumber] on item [$item->name] ", KalturaBatchJobAppErrors::BULK_ITEM_VALIDATION_FAILED);
			
		if(!empty($item->mix) && $item->type != KalturaEntryType::MIX)
			throw new KalturaBatchException("Conflicted typed element for type [$typeNumber] on item [$item->name] ", KalturaBatchJobAppErrors::BULK_ITEM_VALIDATION_FAILED);
			
		// TODO ....
		
		//Gets all the possible elements 
		$mediaElement = $item->media;
		$mixElement = $item->mix;
		$playlistElement = $item->playlist;
		$documentElement = $item->document;
		$liveStreamElement = $item->liveStream;
		$dataElement = $item->data;

		KalturaLog::info("Test - " . empty($mixElement) . empty($documentElement) . isset($liveStreamElement) .isset($playlistElement) . isset($dataElement));
		//Now we get the entry type and check if only the rigth element is not null
		$typeNumber = $item->type;
		
		switch(trim($typeNumber))
		{
			case KalturaEntryType::MEDIA_CLIP :
				if(empty($mediaElement))
				{
					KalturaLog::info("Media Element is missing for type [$typeNumber], using nulls / defaults");
				}
				if( !(empty($mixElement) &&
					  empty($documentElement) &&
					  empty($liveStreamElement) &&
					  empty($playlistElement) &&
					  empty($dataElement)
					 )
				  )
				{
					throw new KalturaBatchException("Conflicted typed element for type [$typeNumber] on item [$item->name] ", KalturaBatchJobAppErrors::BULK_ITEM_VALIDATION_FAILED);
				}
				break;
			case KalturaEntryType::AUTOMATIC:
				//What to do here?
				
				break;
			case KalturaEntryType::DATA:
				if(is_null($dataElement))
				{
					KalturaLog::info("Data Element is missing for type [$typeNumber], using nulls / defaults");
				}
				if(! (is_null($mixElement) &&
					  is_null($documentElement) &&
					  is_null($liveStreamElement) &&
					  is_null($playlistElement) && 
					  is_null($mediaElement)
					 )
				  )
				{
					throw new KalturaBatchException("Conflicted typed element for type [$typeNumber] on item [$item->name] ", KalturaBatchJobAppErrors::BULK_ITEM_VALIDATION_FAILED);
				}
				break;
			case KalturaEntryType::DOCUMENT:
				if(is_null($documentElement))
				{
					KalturaLog::info("Document Element is missing for type [$typeNumber], using nulls / defaults");
				}
				if(! (is_null($mixElement) &&
					  is_null($dataElement) &&
					  is_null($liveStreamElement) &&
					  is_null($playlistElement) && 
					  is_null($mediaElement)
					 )
				  )
				{
					throw new KalturaBatchException("Conflicted typed element for type [$typeNumber] on item [$item->name] ", KalturaBatchJobAppErrors::BULK_ITEM_VALIDATION_FAILED);
				}
				break;
			case KalturaEntryType::LIVE_STREAM:
				if(is_null($liveStreamElement))
				{
					KalturaLog::info("Live Stream Element is missing for type [$typeNumber], using nulls / defaults");
				}
				if(! (is_null($mixElement) &&
					  is_null($documentElement) &&
					  is_null($dataElement) &&
					  is_null($playlistElement) && 
					  is_null($mediaElement)
					 )
				  )
				{
					throw new KalturaBatchException("Conflicted typed element for type [$typeNumber] on item [$item->name] ", KalturaBatchJobAppErrors::BULK_ITEM_VALIDATION_FAILED);
				}
				break;
			case KalturaEntryType::MIX:
				if(is_null($mixElement))
				{
					KalturaLog::info("Mix Element is missing for type [$typeNumber], using nulls / defaults");
				}
				if(! (is_null($liveStreamElement) &&
					  is_null($documentElement) &&
					  is_null($dataElement) &&
					  is_null($playlistElement) && 
					  is_null($mediaElement)
					 )
				  )
				{
					throw new KalturaBatchException("Conflicted typed element for type [$typeNumber] on item [$item->name] ", KalturaBatchJobAppErrors::BULK_ITEM_VALIDATION_FAILED);
				}
				break;
			case KalturaEntryType::PLAYLIST:
				if(is_null($playlistElement))
				{
					KalturaLog::info("Playlist Element is missing for type [$typeNumber], using nulls / defaults");
				}
				if(! (is_null($liveStreamElement) &&
					  is_null($documentElement) &&
					  is_null($dataElement) &&
					  is_null($mixElement) && 
					  is_null($mediaElement)
					 )
				  )
				{
					throw new KalturaBatchException("Conflicted typed element for type [$typeNumber] on item [$item->name] ", KalturaBatchJobAppErrors::BULK_ITEM_VALIDATION_FAILED);
				}
				break;
			default:
				throw new KalturaBatchException("type [$typeNumber] is not supported ", KalturaBatchJobAppErrors::BULK_ITEM_VALIDATION_FAILED); 
		}
	}

	/**
	 * 
	 * Sets the media values in the media entry according to the given media node
	 * @param SimpleXMLElement $mediaElement
	 */
	private function setMediaElementValues(SimpleXMLElement $mediaElement)
	{
		$this->typedElement->mediaType = $mediaElement->mediaType;
		$this->typedElement->ingestionProfileId = $this->getConversionProfileId($mediaElement);
		$this->validateMediaTypes($this->typedElement->mediaType);
	}
	
	/**
	 * 
	 * Checks if the media type and the type are valid
	 * @param KalturaEntryType $type
	 * @param KalturaMediaType $mediaType
	 */
	private function validateMediaTypes($mediaType)
	{
		// TODO validate only media (not live stream) types
		
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
	 * Retutrns the right media type by its name
	 * @param string $typeName
	 */
	private function getMediaTypeByName($mediaTypeName)
	{
		$mediaType = null;
		
		//TODO: Roni - Fix this to use numbers instead of string 
		//Set the content type
		switch(strtolower($mediaTypeName))
		{
			case 'image':
				$mediaType = KalturaMediaType::IMAGE;
				break;
			
			case 'audio':
				$mediaType = KalturaMediaType::AUDIO;
				break;
			
			default:
				$mediaType = KalturaMediaType::VIDEO;
				break;
		}	
		
		return $mediaType;
	}
	
	/**
	 * 
	 * Returns the right entry type by its name
	 * @param string $typeName
	 */
	private function getEntryTypeByNumber($typeNumber)
	{
		$entryType = null;
		
		//TODO: Roni - Fix this to use numbers instead of string 
		//Set the content type
		switch(trim($typeNumber))
		{
			case KalturaEntryType::MEDIA_CLIP :
				$entryType = KalturaEntryType::MEDIA_CLIP;
				break;
			case KalturaEntryType::AUTOMATIC:
				$entryType = KalturaEntryType::AUTOMATIC;
				break;
			case KalturaEntryType::DATA:
				$entryType = KalturaEntryType::DATA;
				break;
			case KalturaEntryType::DOCUMENT:
				$entryType = KalturaEntryType::DOCUMENT;
				break;
			case KalturaEntryType::LIVE_STREAM:
				$entryType = KalturaEntryType::LIVE_STREAM;
				break;
			case KalturaEntryType::MIX:
				$entryType = KalturaEntryType::MIX;
				break;
			case KalturaEntryType::PLAYLIST:
				$entryType = KalturaEntryType::PLAYLIST;
				break;
			default:
				throw new KalturaBatchException("type [$typeNumber] is not supported ", KalturaBatchJobAppErrors::BULK_ITEM_VALIDATION_FAILED); 
		}	
		
		return $entryType;
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
		
		//TODO: Roni - check if the ',' in the end is bad 
		foreach ($element->children() as $child)
		{
			if($child != null)
			{
				$childNodeValue = trim($child);
				if(!empty($childNodeValue))
				{
					KalturaLog::debug("In getStringFromElement - child value [". $childNodeValue . "]");
					$ret[] = $childNodeValue;
				}
			}
		}
		
		$ret = implode(',', $ret);
		KalturaLog::debug("In getStringFromElement - The created string [$ret]");
		return $ret;
	}

	/**
	 * 
	 * Creates a new upload result object from the given SimpleXMLElement item
	 * @param SimpleXMLElement $item
	 */
	protected function createUploadResult($item)
	{
		if($this->handledRecordsThisRun > $this->maxRecordsEachRun)
		{
			$this->exceededMaxRecordsEachRun = true;
			return;
		}
		
		$this->handledRecordsThisRun++;
		
		$bulkUploadResult = new KalturaBulkUploadResult();
		$bulkUploadResult->bulkUploadJobId = $this->job->id;
		
		//TODO: maybe change to be item name / id (on a new object KalturaBulkUploadXMLResult)
//		$bulkUploadResult->lineIndex = $this->lineNumber;
		$bulkUploadResult->partnerId = $this->job->partnerId;
		$bulkUploadResult->rowData = join($item);
				
		//TODO: handle plugin data in the bulk result 
//		$bulkUploadPlugin = new KalturaBulkUploadPluginData();
//		$bulkUploadPlugin->field = $column;
//		$bulkUploadPlugin->value = iconv_strlen($values[$index], 'UTF-8') ? $values[$index] : null;
//		$bulkUploadPlugins[] = $bulkUploadPlugin;
//		$bulkUploadResult->pluginsData = $bulkUploadPlugins;
	
		$bulkUploadResult->entryStatus = KalturaEntryStatus::IMPORT;
		
		if(!is_numeric($bulkUploadResult->conversionProfileId))
			$bulkUploadResult->conversionProfileId = null;
			
		if(!is_numeric($bulkUploadResult->accessControlProfileId))
			$bulkUploadResult->accessControlProfileId = null;
			
		if(!empty($item->startDate))
		{
			if($item->startDate && !$this->isFormatedDate($item->startDate))
			{
				$bulkUploadResult->entryStatus = KalturaEntryStatus::ERROR_IMPORTING;
				$bulkUploadResult->errorDescription = "Invalid schedule start date {$item->startDate} on item $item->name";
			}
		}
		
		if(!empty($item->endDate))
		{
			if($item->endDate && !$this->isFormatedDate($item->endDate))
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
		
		$bulkUploadResult->scheduleStartDate = $this->parseFormatedDate($item->startDate);
		$bulkUploadResult->scheduleEndDate = $this->parseFormatedDate($item->endDate);
			
		$this->addBulkUploadResult($bulkUploadResult);
	}
}