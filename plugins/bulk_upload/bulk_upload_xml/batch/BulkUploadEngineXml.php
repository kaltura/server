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
			default:
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
			throw new KalturaBatchException("Resource is not supported: {$this->currentContentElement}", KalturaBatchJobAppErrors::BULK_FILE_NOT_FOUND); //The job was aborted
		}
	}
	
	/**
	 * 
	 * Returns the right resource instance for the source content of the item
	 */
	private function getResourceInstance()
	{
		KalturaLog::debug("In getResourceInstance");
		
		$resource = null;
	
		if($this->currentContentElement->hasAttribute("localFileContentResource"))
		{
			KalturaLog::debug("Resource is : localFileContentResource");
			$resource = new KalturaLocalFileResource();
			$resource->localFilePath =$this->currentContentElement->getAttribute("filePath");
			//TODO: Roni - what to do with those?
//		<xs:choice minOccurs="1" maxOccurs="1">
//			<xs:element name="fileSize" type="xs:int" minOccurs="1" maxOccurs="1"/>
//			<xs:element name="fileChecksum" type="xs:string" minOccurs="1" maxOccurs="1"/>
//		</xs:choice>
		}
		elseif($this->currentContentElement->hasAttribute("urlContentResource"))
		{
			KalturaLog::debug("Resource is : urlContentResource");
			$resource = new KalturaUrlResource();
			$resource->url = $this->currentContentElement->getAttribute("url");
		}
		elseif($this->currentContentElement->hasAttribute("remoteStorageContentResource"))
		{
			KalturaLog::debug("Resource is : remoteStorageContentResource");
			$resource = new KalturaRemoteStorageResource();
			$resource->url = $this->currentContentElement->getAttribute("url");
			$resource->storageProfileId = $this->getStorageProfileId($this->currentContentElement, "storageProfile", "storageProfile");
		}
		elseif($this->currentContentElement->hasAttribute("entryContentResource"))
		{
			KalturaLog::debug("Resource is : entryContentResource");
			$resource = new KalturaEntryResource();
			$resource->entryId = $this->currentContentElement->getAttribute("entryId");
			
			$resource->flavorParamsId = $this->getFlavorParamsId();
		}
		elseif($this->currentContentElement->hasAttribute("assetContentResource"))
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
	 */
	private function getFlavorParamsId()
	{
		//TODO: fix this
		$flavorParamsId = $this->sourceContent->getAttribute("flavorParamsId"); 
		$flavorParamsName = $this->sourceContent->getAttribute("flavorParams");
			
		return $this->getFlavorParamsByIdAndName($flavorParamsId, $flavorParamsName);
	}
	
	/**
	 * 
	 * Gets the storage profile id from the source content element  
	 */
	private function getStorageProfileId()
	{
		$storageProfileId = $this->sourceContent->getAttribute("storageProfileId"); 
		$storageProfileName = $this->sourceContent->getAttribute("storageProfile");
			
		//TODO: implement this
		return $this->getStorageProfileByIdAndName($storageProfileId, $storageProfileName);
	}
	
	/**
	 * 
	 * Tries to get the given attribute / Element name form the given element by iud or name   
	 * @param $elementToSearchIn
	 * @param $attributeName
	 * @throws KalturaBatchException - in case the id and name reference different objects
	 */
	private function getFlavorParamsByIdAndName($flavorParamsId, $flavorParamsName)
	{
		if(isset($flavorParamsId) && !empty($flavorParamsId))
		{
			$this->currentFlavorId = trim($flavorParamsId);
			return;
		}
		
		if(!empty($flavorParamsName))//if we have no id then we search by name
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
		$mediaEntry->name = $item->getElementsByTagName("name")->item(0)->nodeValue;
		$mediaEntry->description = $item->getElementsByTagName("description")->item(0)->nodeValue;

		$tagsElement = $item->getElementsByTagName("tags")->item(0);
		$mediaEntry->tags = $this->getStringFromElement($tagsElement);
		
		$categoriesElement = $item->getElementsByTagName("categories")->item(0);
		$mediaEntry->categories = $this->getStringFromElement($categoriesElement);
		$mediaEntry->userId = $item->getElementsByTagName("userId")->item(0)->nodeValue;

		$mediaEntry->accessControlId =  $item->getElementsByTagName("accessControlId")->item(0)->nodeValue;
		
		//TODO: Roni - Parse the date
		$mediaEntry->startDate = $item->getElementsByTagName("startDate")->item(0)->nodeValue;

		//Adds to the media entry the media element data
		$this->setMediaElementValues(&$mediaEntry, $item->getElementsByTagName("media")->item(0));
		
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
		$mediaEntry->type = $this->getTypeByName($mediaElement->getElementsByTagName("mediaType")->item(0)->nodeValue);
		$mediaEntry->flavorParamsIds = $this->getStringFromElement($mediaElement->getElementsByTagName("flavorParamsIds")->item(0));

		//$mediaEntry->thumbParamsId = $mediaElement->getElementsByTagName("thumbParamsIds")->nodeValue;
		
//		$mediaEntry->playList = $this->addToPlaylists($mediaEntry, $mediaElement->getElementsByTagName("playlists"));
		
		//TODO: Roni - not found in the entry object what to do for each one
//		$mediaEntry->conversionProfileId = $this->getTypeByName($mediaElement->nodeValue);
		
		//TODO: Roni maybe add conversion quality to the xml
//		$mediaEntry->conversionQuality = $bulkUploadJobData->conversionProfileId;
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
	 * Retutrns the right type by its name
	 * @param string $typeName
	 */
	private function getTypeByName($typeName)
	{
		$mediaType = null;
		
		//TODO: Roni - Fix this to use numbers instead of string 
		//Set the content type
		switch(strtolower($typeName))
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
	 * Returns a comma seperated string with the values of the child nodes of the given element 
	 * @param DOMElement $element
	 */
	private function getStringFromElement($element)
	{
		$commaSeperatedString = ""; 
		
		//TODO: Roni - check if the ',' in the end is bad 
		foreach ($element->childNodes as $child)
		{
			if(!is_null($child) || $child != "")
			{
				$commaSeperatedString = $commaSeperatedString . trim($child->nodeValue) .',';
			}
		}
		
		return $commaSeperatedString;
	}
}
