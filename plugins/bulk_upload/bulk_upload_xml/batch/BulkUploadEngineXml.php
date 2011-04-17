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
	
	/**
	 * 
	 * The engine xsd file path
	 * @var string
	 */
	private $xsdFilePath = "/../lib/ingestion.xsd";
	
	/**
	 * 
	 * The element for the source content
	 * @var DOMElement
	 */
	private $sourceContent;
	
	/**
	 *  //TODO: Roni - maybe support more then 1 id
	 * The current flavor id
	 * @var int
	 */
	private $currentFlavorId;
	
	/**
	 * 
	 * Maps the flavor params name to id
	 * @var array()
	 */
	private $flavorParamsNameToId = null;
	
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
	
	/**
	 * 
	 * Maps the thumb params name to id
	 * @var array()
	 */
	private $thumbParamsNameToId = null;
	
	/**
	 * 
	 * The current handled content element
	 * @var DOMElement
	 */
	private $currentContentElement;
	 
	/**
	 * @return string
	 */
	public function getName()
	{
		return get_class($this);
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
	 * 
	 * Validates that the xml is valid using the XSD
	 */
	protected function validate() 
	{
		$xdoc = new DomDocument;
		$xdoc->Load($this->data->filePath);
		//Validate the XML file against the schema
		if(!$xdoc->schemaValidate(dirname(__FILE__) . $this->xsdFilePath)) 
		{
			throw new KalturaException("Validate files failed on job [{$this->job->id}]", KalturaBatchJobAppErrors::BULK_VALIDATION_FAILED);
		}
		
		return true;
	}

	/**
	 * 
	 * Parses the Xml file lines and creates the right actions in the system
	 */
	protected function parse() 
	{
		$xdoc = new DomDocument;
		//Load the xml document in the DOMDocument object
		$xdoc->Load($this->data->filePath);
//		$this->startMultiRequest(true);
		
		foreach( $xdoc->getElementsByTagName("channel") as $channel)
		{
//			KalturaLog::debug("Channel name: {$channel->nodeName}, Channel Type = {$channel->nodeType}, Channel Value = {$channel->nodeValue}");
			$this->handleChannel($channel);
		}
	}

	/**
	 * 
	 * Gets and handles a channel from the mrss
	 * @param DOMElement $channel
	 */
	private function handleChannel(DOMElement $channel)
	{
		//Gets all items from the channel
		foreach( $channel->getElementsByTagName("item") as $item)
		{
			//TODO: add check if the bulk count has reached its max size and send the data
//			KalturaLog::debug("Item name: {$item->nodeName}, Item Type = {$item->nodeType}, Item Value = {$item->nodeValue}");
			$this->handleItem($item);
		}	
	}
	
	/**
	 * 
	 * Gets and handles an item from the channel
	 * @param DOMElement $item
	 */
	private function handleItem(DOMElement$item)
	{
		$actionToPerform  = $item->getElementsByTagName("action")->item(0)->nodeValue;
		switch(strtolower($actionToPerform))
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
				throw new KalturaException("Action: {$actionToPerform} is not supported", KalturaBatchJobAppErrors::BULK_ACTION_NOT_SUPPORTED);
		}
	}

	/**
	 * 
	 * Handles xml bulk upload update
	 * @param DOMElement $item
	 * @throws KalturaException
	 */
	private function handleItemUpdate(DOMElement $item)
	{
		throw new KalturaException("Action: Update is not supported", KalturaBatchJobAppErrors::BULK_ACTION_NOT_SUPPORTED);
	}

	/**
	 * 
	 * Handles xml bulk upload delete
	 * @param DOMElement $item
	 * @throws KalturaException
	 */
	private function handleItemDelete(DOMElement $item)
	{
		throw new KalturaException("Action: Delete is not supported", KalturaBatchJobAppErrors::BULK_ACTION_NOT_SUPPORTED);
	}
	
	/**
	 * 
	 * Gets an item and insert it into the system
	 * @param DOMElement $item
	 */
	private function handleItemAdd(DOMElement $item)
	{
		KalturaLog::debug("In handleItemAdd");
		$entryToInsert = $this->createMediaEntryFromItem($item);
		
		foreach ($item->getElementsByTagName("content") as $contentElement)
		{
			$this->currentContentElement = $contentElement;
			$resource = $this->getResource($contentElement);
			
			KalturaLog::debug("Entry to add is: {$entryToInsert->name}");
			$result = $this->kClient->media->add($entryToInsert, $resource);
			KalturaLog::debug("result is: " .var_dump($result));
		}
	}

	/**
	 * 
	 * Gets an item and returns the resource
	 * @param DOMElement $item
	 */
	private function getResource()
	{
		$resource = $this->getResourceInstance(); 
				
		if(is_null($resource))
		{
			throw new KalturaBatchException("Resource is not supported: {$this->currentContentElement->textContent}", KalturaBatchJobAppErrors::BULK_FILE_NOT_FOUND); //The job was aborted
		}
	}
		
	/**
	 * 
	 * Gets the element name
	 * @param string $elementName
	 * @param DOMElement $elementToSearchIn
	 * @param bool $isThrowException
	 * @throws KalturaBatchException - KalturaBatchJobAppErrors::BULK_OBJECT_NOT_FOUND
	 */
	private function getElement($elementName, DOMElement $elementToSearchIn, $isThrowException = true)
	{
		$elements = $elementToSearchIn->getElementsByTagName($elementName);
		if($elements->length > 0)
		{
			return $elements->item(0);  
		}
		
		if($isThrowException)
		{
			throw new KalturaBatchException("Unable to get Element [$elementName] in parnet element[$elementToSearchIn->nodeName] ", KalturaBatchJobAppErrors::BULK_OBJECT_NOT_FOUND);
		}
		
		return null;
	} 
	
	/**
	 * 
	 * Checks if the given element to search in has the wanted element
	 * @param string $elementName
	 * @param DOMElement $elementToSearchIn
	 */
	private function hasElement($elementName, DOMElement $elementToSearchIn)
	{
		$elements = $elementToSearchIn->getElementsByTagName($elementName);
		if($elements->length > 0)
		{
			return true;
		}
				
		return false;
	}
	
	/**
	 * 
	 * Returns the right resource instance for the source content of the item
	 */
	private function getResourceInstance()
	{
		KalturaLog::debug("In getResourceInstance");
		
		$resource = null;
			
		if($this->hasElement("localFileContentResource", $this->currentContentElement))
		{
			KalturaLog::debug("Resource is : localFileContentResource");
			$resource = new KalturaLocalFileResource();
			$localContentResorce = $this->getElement("localFileContentResource", $this->currentContentElement, true);
			$resource->localFilePath = $localContentResorce ->getAttribute("filePath");
			
			//TODO: Roni - what to do with those?
//		<xs:choice minOccurs="1" maxOccurs="1">
//			<xs:element name="fileSize" type="xs:int" minOccurs="1" maxOccurs="1"/>
//			<xs:element name="fileChecksum" type="xs:string" minOccurs="1" maxOccurs="1"/>
//		</xs:choice>
		}
		elseif($this->hasElement("urlContentResource", $this->currentContentElement))
		{
			KalturaLog::debug("Resource is : urlContentResource");
			$resource = new KalturaUrlResource();
			$urlContentResource = $this->getElement("urlContentResource", $this->currentContentElement, true);
			$resource->url = $urlContentResource->getAttribute("url");
		}
		elseif($this->hasElement("remoteStorageContentResource", $this->currentContentElement))
		{
			KalturaLog::debug("Resource is : remoteStorageContentResource");
			$resource = new KalturaRemoteStorageResource();
			$remoteContentResource = $this->getElement("urlContentResource", $this->currentContentElement, true);
			$resource->url = $remoteContentResource->getAttribute("url");
			$resource->storageProfileId = $this->getStorageProfileId($remoteContentResource);
		}
		elseif($this->hasElement("entryContentResource", $this->currentContentElement))
		{
			KalturaLog::debug("Resource is : entryContentResource");
			$resource = new KalturaEntryResource();
			
			$resource->entryId = $this->currentContentElement->getAttribute("entryId");
			
			$resource->flavorParamsId = $this->getFlavorParamsId();
		}
		elseif($this->hasElement("assetContentResource", $this->currentContentElement))
		{
			KalturaLog::debug("Resource is : assetContentResource");
			$resource = new KalturaAssetResource();
			$resource->assetId = $this->currentContentElement->getAttribute("assetId");
		}
		
		return $resource;
	}
	
	/**
	 * 
	 * Gets the flavor params id from the source content element
	 * @param $elementToSearchIn - The element to search in
	 * @return int - The id of the flavor params
	 */
	private function getFlavorParamsId(DOMElement $elementToSearchIn, $isAttribute = true)
	{
		if($isAttribute) //Gets value from attributes
		{
			$flavorParamsId = $elementToSearchIn->getAttribute("flavorParamsId"); 
			$flavorParamsName = $elementToSearchIn->getAttribute("flavorParams");
		}
		else //Gets value from elements
		{
			$flavorParamsId = $this->getElement("flavorParamsId", $elementToSearchIn, false)->nodeValue; 
			$flavorParamsName = $this->getElement("flavorParams", $elementToSearchIn, false)->nodeValue;
		}
			
		return $this->getFlavorParamsByIdAndName($flavorParamsId, $flavorParamsName);
	}
	
	/**
	 * 
	 * Returns from the given object it's flavor params ids
	 * @param DOMElement $elementToSearchIn
	 */
	private function getFlavorParamsIds(DOMElement $elementToSearchIn)
	{
		KalturaLog::debug("In getFlavorParamsIds");
		
		$flavorParamsIds = "";
		
		//Can be null
		$flavorParamsIdsElement = $this->getElement("flavorParamsIds", $elementToSearchIn, false);
		 
		if(is_null($flavorParamsIdsElement)) // id is null so we get by names
		{
			//get the names
			$flavorParamsElement = $this->getElement("flavorParams", $elementToSearchIn, false);
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
		
		KalturaLog::debug("In getFlavorParamsIds - flavorParamsIds []$flavorParamsIds ");
		return $flavorParamsIds;
	}
		
	/**
	 * 
	 * Gets the flavor params id from the source content element
	 * @param $elementToSearchIn - The element to search in
	 * @return int - The id of the flavor params
	 */
	private function getAccessControlId(DOMElement $elementToSearchIn)
	{
		//TODO: fix this
		$accessControlIdElement = $this->getElement("accessControlId", $elementToSearchIn, false);
		$accessControlElement = $this->getElement("accessControl", $elementToSearchIn, false);
		
		return $this->getAccessControlIdByIdAndName($accessControlIdElement->nodeValue ,$accessControlElement->nodeValue);
	}
		
	/**
	 * 
	 * Gets the access control id by it's id or name   
	 * @param $accessControlId - the storage profile id
	 * @param string $accessControlName - the storage profile system name 
	 * @throws KalturaBatchException - in case ther is not such storage profile with the given name
	 */
	private function getAccessControlIdByIdAndName($accessControlId, $accessControlName)
	{
		if(isset($accessControlId) && !empty($accessControlId))
		{
			$this->currentFlavorId = trim($accessControlId);
			return;
		}
		
		if(!empty($accessControlName))//if we have no id then we search by name
		{
			if(is_null($this->flavorParamsNameToId))
			{
				$this->initFlavorParamsNameToId();
			}
			
			if(isset($this->flavorParamsNameToId[$accessControlName]))
			{
				$this->currentFlavorId = trim($this->flavorParamsNameToId[$accessControlName]);
				return;
			}
		}

		//If we got here then the id or name weren't found
		throw new KalturaBatchException("Can't find flavor params with id [$accessControlId], name [$accessControlName]", KalturaBatchJobAppErrors::BULK_OBJECT_NOT_FOUND);	
	}
		
	/**
	 * 
	 * Gets the storage profile id from the source content element
	 * @param $elementToSearchIn - The element to search in
	 * @return int - The id of the storage profile
	 */
	private function getStorageProfileId(DOMElement $elementToSearchIn)
	{
		$storageProfileId = $elementToSearchIn->getAttribute("storageProfileId"); 
		$storageProfileName = $elementToSearchIn->getAttribute("storageProfile");
			
		//TODO: implement this (after validation of the flavor params)
		return $this->getStorageProfileByIdAndName($storageProfileId, $storageProfileName);
	}
	
	/**
	 * 
	 * Gets the storage profile id by it's id or name   
	 * @param $storageProfileId - the storage profile id
	 * @param string $storageProfileName - the storage profile system name 
	 * @throws KalturaBatchException - in case ther is not such storage profile with the given name
	 */
	private function getStorageProfileByIdAndName($storageProfileId, $storageProfileName)
	{
		if(isset($storageProfileId) && !empty($storageProfileId))
		{
			$this->currentFlavorId = trim($storageProfileId);
			return;
		}
		
		if(!empty($storageProfileName))//if we have no id then we search by name
		{
			if(is_null($this->flavorParamsNameToId))
			{
				$this->initFlavorParamsNameToId();
			}
			
			if(isset($this->flavorParamsNameToId[$storageProfileName]))
			{
				$this->currentFlavorId = trim($this->flavorParamsNameToId[$storageProfileName]);
				return;
			}
		}

		//If we got here then the id or name weren't found
		throw new KalturaBatchException("Can't find flavor params with id [$storageProfileId], name [$storageProfileName]", KalturaBatchJobAppErrors::BULK_OBJECT_NOT_FOUND);
	}
	
	/**
	 * 
	 * Gets the flavor params id by it's id or name   
	 * @param $flavorParamsId - the flavor params id
	 * @param $flavorParamsName - the flavor params name
	 * @throws KalturaBatchException - in case there is not flavor params by the given name
	 */
	private function getFlavorParamsByIdAndName($flavorParamsId, $flavorParamsName)
	{
		if(isset($flavorParamsId) && !empty($flavorParamsId))
		{
			$this->currentFlavorId = trim($flavorParamsId);
			return;
		}
		
		if(!empty($flavorParamsName)) //If we have no id then we search by name
		{
			if(is_null($this->flavorParamsNameToId))
			{
				$this->initFlavorParamsNameToId();
			}
			
			if(isset($this->flavorParamsNameToId[$flavorParamsName]))
			{
				$this->currentFlavorId = trim($this->flavorParamsNameToId[$flavorParamsName]);
				return;
			}
		}

		//If we got here then the id or name weren't found
		throw new KalturaBatchException("Can't find flavor params with id [], name [$flavorParamsName]", KalturaBatchJobAppErrors::BULK_OBJECT_NOT_FOUND);
	}
	
	/**
	 * 
	 * Inits the array of flavor params to Id (with all given flavor params)
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
	 * Inits the array of flavor params to Id (with all given flavor params)
	 */
	private function initThumbParamsNameToId()
	{
		$allThumbParams = $this->kClient->thumbParams->listAction(null, null);
		
		foreach ($allThumbParams as $thumbParam)
		{
			$this->flavorParamsNameToId[$thumbParam->systemName] = $thumbParam->id;
		}
	}
		
	/**
	 * 
	 * Inits the array of conversion profile to Id (with all given flavor params)
	 */
	private function initConversionProfileNameToId()
	{
		$allConversionProfile = $this->kClient->conversioProfile->listAction(null, null);
		
		foreach ($allConversionProfile as $conversionProfile)
		{
			$this->flavorParamsNameToId[$conversionProfile->systemName] = $conversionProfile->id;
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
			$this->flavorParamsNameToId[$storageProfile->systemName] = $storageProfile->id;
		}
	}
		
	/**
  	 * Creates and returns a new media entry for the given job data and bulk upload result object
	 * @param DOMElement $bulkUploadResult
	 */
	private function createMediaEntryFromItem(DOMElement $item)
	{
		//Create the new media entry and set basic values
		$mediaEntry = new KalturaMediaEntry();

		$nameElement = $this->getElement("name", $item);
		$mediaEntry->name = $nameElement->nodeValue;
		
		$descriptionElement = $this->getElement("description", $item);
		$mediaEntry->description = $descriptionElement->nodeValue;
		
		$tagsElement = $this->getElement("tags", $item);
		$mediaEntry->tags = $this->getStringFromElement($tagsElement);
		
		$categoriesElement = $this->getElement("categories", $item);
		$mediaEntry->categories = $this->getStringFromElement($categoriesElement);
				
		$userIdElement = $this->getElement("userId", $item);
		$mediaEntry->userId = $userIdElement->nodeValue;

		$mediaEntry->accessControlId =  $this->getAccessControlId($item);
				
		//TODO: Roni - Parse the date
		$dateElement = $this->getElement("startDate", $item);
		$mediaEntry->startDate = $dateElement->nodeValue;

		$mediaElement = $this->getElement("media", $item);
		$mediaEntry->type = $this->getEntryTypeByName($mediaTypeElement->nodeValue); 
		
		//Adds to the media entry the media element data
		$this->setMediaElementValues(&$mediaEntry, $mediaElement);
		
		foreach ($item->getElementsByTagName("content") as $contentElement)
		{
			$this->setContentElementValues($mediaEntry, $contentElement);
		}
	
		return $mediaEntry;
	}
	
	/**
	 * 
	 * Sets values in the media entry by the given content element
	 * @param DOMElement $mediaEntry
	 * @param KalturaMediaEntry $contentElement
	 */
	private function setContentElementValues(KalturaMediaEntry $mediaEntry, DOMElement $contentElement)
	{
		//TODO: Roni - handle content element logic
		
	}
	
	/**
	 * 
	 * Sets the media values in the media entry according to the given media node
	 * @param KalturaMediaEntry $mediaEntry
	 * @param DOMElement $mediaElement
	 */
	private function setMediaElementValues(KalturaMediaEntry &$mediaEntry, DOMElement $mediaElement)
	{
		$mediaTypeElement = $this->getElement(mediaType, $mediaElement);
		$mediaEntry->mediaType = $this->getMediaTypeByName($mediaTypeElement->nodeValue);
		 
		$this->checkMediaTypes($mediaEntry->type ,$mediaEntry->mediaType);
		
		$mediaEntry->flavorParamsIds = $this->getFlavorParamsIds($mediaElement);
		
		//$mediaEntry->thumbParamsId = $mediaElement->getElementsByTagName("thumbParamsIds")->nodeValue;
		
//		$mediaEntry->playList = $this->addToPlaylists($mediaEntry, $mediaElement->getElementsByTagName("playlists"));
		
		//TODO: Roni - not found in the entry object what to do for each one
//		$mediaEntry->conversionProfileId = $this->getTypeByName($mediaElement->nodeValue);
		
		//TODO: Roni maybe add conversion quality to the xml
//		$mediaEntry->conversionQuality = $bulkUploadJobData->conversionProfileId;
	}
	
	/**
	 * 
	 * Checks if the media type and the type are valid
	 * @param KalturaEntryType $type
	 * @param KalturaMediaType $mediaType
	 */
	private function checkMediaTypes($type ,$mediaType)
	{
		//TODO: Roni - add support for all other types
		switch ($type)
		{
			case KalturaEntryType::MEDIA_CLIP:
				if($mediaType != KalturaMediaType::VIDEO)
				{
					throw new KalturaBatchException("the entry type [$type] is not supported with media type [$mediaType]", KalturaBatchJobAppErrors::BULK_VALIDATION_FAILED);
				}
				break;
			default:
				throw new KalturaBatchException("The type [$type] is not supported", KalturaBatchJobAppErrors::BULK_NOT_SUPPORTED_EXCEPTION);
		}
	}
	
	/**
	 * 
	 * Adds the given media entry to the given playlists in the element
	 * @param KalturaMediaEntry $mediaEntry
	 * @param DOMElement $playlistsElement
	 */
	private function addToPlaylists(KalturaMediaEntry $mediaEntry, DOMElement $playlistsElement)
	{
		foreach ($playlistsElement->childNodes as $playlist)
		{
			//TODO: Roni - add the media to the play list 
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
	private function getEntryTypeByName($typeName)
	{
		$entryType = null;
		
		//TODO: Roni - Fix this to use numbers instead of string 
		//Set the content type
		switch(strtolower($typeName))
		{
			case 'image':
				$entryType = KalturaEntryType::MEDIA_CLIP;
				break;
			
			case 'audio':
				$entryType = KalturaEntryType::MEDIA_CLIP;
				break;
			
			default:
				$entryType = KalturaEntryType::MEDIA_CLIP;
				break;
		}	
		
		return $entryType;
	}
	
	/**
	 * 
	 * Returns a comma seperated string with the values of the child nodes of the given element 
	 * @param DOMElement $element
	 */
	private function getStringFromElement(DOMElement $element)
	{
		$commaSeperatedString = ""; 
		
		//TODO: Roni - check if the ',' in the end is bad 
		foreach ($element->childNodes as $child)
		{
			if(!is_null($child) || $child != "")
			{
				KalturaLog::debug("In getStringFromElement - child value [{$child->nodeValue}]");
				$commaSeperatedString = $commaSeperatedString . trim($child->nodeValue) .',';
			}
		}
		
		return $commaSeperatedString;
	}
}