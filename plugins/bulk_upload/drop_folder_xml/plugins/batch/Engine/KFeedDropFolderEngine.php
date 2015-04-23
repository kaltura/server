 <?php
/**
 * @package plugins.FeedDropFolder
 */
class KFeedDropFolderEngine extends KDropFolderEngine 
{
	const DEFAULT_CONTENT_ITEM_SIZE = 1;
	
	/**
	 * @var array
	 */
	protected $feedNamespaces;
	
	static $searchCharacters = array ('/');
	static $replaceCharacters = array('_');
	
	/* (non-PHPdoc)
	 * @see KDropFolderEngine::watchFolder()
	 */
	public function watchFolder(KalturaDropFolder $dropFolder) {
		/* @var $dropFolder KalturaFeedDropFolder */		
		KalturaLog::info("Watching drop folder with ID [" . $dropFolder->id . "]");
		$this->dropFolder = $dropFolder;
		
		//Get Drop Folder feed and import it into a SimpleXMLElement
		
		$feed = new SimpleXMLElement (file_get_contents($dropFolder->path));
		$this->feedNamespaces = $feed->getNamespaces(true);
		
		//get items
		$feedItems = $feed->xpath ($this->dropFolder->feedItemInfo->itemXPath);
		if ($this->dropFolder->itemHandlingLimit > 0 && count ($feedItems) > $this->dropFolder->itemHandlingLimit)
		{
			KalturaLog::err("Reached pulling limit for drop folder ID [" . $this->dropFolder->id . "].");
			
			array_splice ($feedItems, $this->dropFolder->itemHandlingLimit);
			
			$dropFolderUpdate = new KalturaFeedDropFolder();
			$dropFolderUpdate->errorDescription = FeedDropFolderPlugin::ERROR_MESSAGE_INCOMPLETE_HANDLING . $this->dropFolder->id;
			$this->dropFolderPlugin->dropFolder->update($this->dropFolder->id, $dropFolderUpdate);
		}
		
		$existingDropFolderFilesMap = $this->loadDropFolderFiles();
		
		$counter = 0;
		foreach ($feedItems as $feedItem)
		{
			if ($counter > intval ( KBatchBase::$taskConfig->params->mrss->limitProcessEachRun))
			{
				KalturaLog::info('Finished current run.');
				break;
			}
			
			/* @var $feedItem SimpleXMLElement */
			$uniqueId = strval($this->getSingleXPathResult($this->dropFolder->feedItemInfo->itemUniqueIdentifierXPath, $feedItem));
			// The unique feed item identifier is the GUID, so that is what we set as the drop folder file name.
			if (!array_key_exists($uniqueId, $existingDropFolderFilesMap))
			{
				//In this case, we are required to add this item as a new drop folder file
				$this->handleItemAdded ($uniqueId, $feedItem);
				$counter++;
			}
			else
			{
				$dropFolderFile = $existingDropFolderFilesMap[$uniqueId];
				//if file exist in the folder remove it from the map
				//all the files that are left in a map will be marked as PURGED					
				unset($existingDropFolderFilesMap[$uniqueId]);
				$this->handleExistingItem($dropFolderFile, $feedItem);
				
				$counter++;
			}
		}
		
		foreach ($existingDropFolderFilesMap as $existingDropFolderFile)
		{
			$this->handleFilePurged($existingDropFolderFile->id);
		}
		
	}
	
	
	/**
	 * Add a new item from the MRSS feed
	 * @param string $uniqueId
	 * @param SimpleXMLElement $feedItem
	 * @param bool $contentUpdateRequired
	 * @return Ambigous <KalturaDropFolderFile, MultiRequestSubResult, unknown, NULL, multitype:, multitype:string unknown , multitype:mixed string >|NULL
	 */
	protected function handleItemAdded ($uniqueId, SimpleXMLElement $feedItem, $contentUpdateRequired = true)
	{
		KalturaLog::debug('Add drop folder file ['.$uniqueId.']');
		try 
		{
			//Register MRSS media namespaces on the separate <item>
			foreach ($this->feedNamespaces as $nameSpace => $url)
			{
				KalturaLog::debug("Add original namespace $nameSpace with URL $url to separate <item>");
				//This is a PHP weakness- the only way to prettily add a namespace to an XML
				$feedItem->addAttribute("xmlns:xmlns:$nameSpace", $url);
			}
			
			$feedPath = $this->saveFeedItemToDisk ($feedItem, $contentUpdateRequired);
			
			$newDropFolderFile = new KalturaFeedDropFolderFile();
	    	$newDropFolderFile->dropFolderId = $this->dropFolder->id;
	    	$newDropFolderFile->fileName = $uniqueId;
	    	$newDropFolderFile->lastModificationTime = strval($this->getSingleXPathResult($this->dropFolder->feedItemInfo->itemPublishDateXPath, $feedItem)); 
	    	$newDropFolderFile->hash = strval($this->getSingleXPathResult($this->dropFolder->feedItemInfo->itemHashXPath, $feedItem));
	    	$newDropFolderFile->fileSize = self::DEFAULT_CONTENT_ITEM_SIZE;
	    	
	    	// Disabled this code for the time being, until there is a requirement for MRSS feed updates. Drop folder file size will be set to 1. 
//	    	$fileSize = $this->getSingleXPathResult($this->dropFolder->feedItemInfo->itemContentFileSizeXPath, $feedItem); 
//	    	if (!is_null ($fileSize))
//	    	{
//	    		$newDropFolderFile->fileSize = intval($fileSize);
//	    	}
//	    	else 
//	    	{
//	    		$url = $this->getSingleXPathResult($this->dropFolder->feedItemInfo->itemContentUrlXPath, $feedItem);
//	    		if (is_null ($url))
//	    		{
//	    			throw new Exception ("Cannot add drop folder file - content URL does not exist");
//	    		}
//	    		$contentUrl = strval($url);
//	
//				$curl = curl_init($contentUrl);
//				curl_setopt($curl, CURLOPT_HEADER, true);
//			    curl_setopt($curl, CURLOPT_FILETIME, true);
//			    curl_setopt($curl, CURLOPT_NOBODY, true);
//				$res = curl_exec($curl);
//				if ($res)
//				{
//					$curlInfo = curl_getinfo($curl);
//					$newDropFolderFile->fileSize = intval($curlInfo['download_content_length']);
//				}
//				
//				curl_close($curl);
//	    	}
	    	
	    	$newDropFolderFile->feedXmlPath = $feedPath;
			//No such thing as an 'uploading' MRSS drop folder file - if the file is detected, it is ready for upload. Immediately update status to 'pending'
	    	KBatchBase::$kClient->startMultiRequest();
			$dropFolderFile = $this->dropFolderFileService->add($newDropFolderFile);
			$this->dropFolderFileService->updateStatus($dropFolderFile->id, KalturaDropFolderFileStatus::PENDING);
			$result = KBatchBase::$kClient->doMultiRequest();
			
			return $dropFolderFile;
		}
		catch(Exception $e)
		{
			KalturaLog::err('Cannot add new drop folder file with name ['.$feedItem->guid.'] - '.$e->getMessage());
			return null;
		}
	}
	
	protected function saveFeedItemToDisk (SimpleXMLElement $feedItem, $contentUpdateRequired)
	{
		if (!$contentUpdateRequired)
		{
			KalturaLog::debug("Removing content tags from feed");
			$contentItems = $feedItem->xpath ($this->dropFolder->feedItemInfo->itemContentXpath);
			foreach ($contentItems as $contentItem)
			{
				unset ($contentItem[0]);
			}
		}
		elseif ($this->dropFolder->feedItemInfo->itemContentBitrateXPath && $this->dropFolder->feedItemInfo->contentBitrateAttributeName)
		{
			$maxBitrate = $this->getMaxFeedBitrate($feedItem);
			if ($maxBitrate)
			{
				$contentItems = $feedItem->xpath ($this->dropFolder->feedItemInfo->itemContentXpath);
				foreach ($contentItems as $contentItem)
				{
					$bitrateAttributeName = $this->dropFolder->feedItemInfo->contentBitrateAttributeName;
					if (intval($contentItem->attributes()->$bitrateAttributeName) != $maxBitrate)
						unset ($contentItem[0]);
				}
			}
			
		}
		
		$updatedGuid = str_replace (self::$searchCharacters, self::$replaceCharacters, strval ($feedItem->guid));
		
		$feedItemPath = KBatchBase::$taskConfig->params->mrss->xmlPath . DIRECTORY_SEPARATOR. $updatedGuid . '_' . time();
		$res = file_put_contents($feedItemPath, $feedItem->saveXML());
		chmod($feedItemPath, 0660);
		return $feedItemPath;
	}
	
	/**
	 * Decide whether to update content/metadata of an existing drop folder file
	 * @param KalturaDropFolderFile $existingDropFolderFile
	 * @param SimpleXMLElement $feedItem
	 */
	protected function handleExistingItem (KalturaFeedDropFolderFile $existingDropFolderFile, SimpleXMLElement $feedItem)
	{
		//check whether the hash has changed - in this case the content needs to be updated.
		$feedItemHash = strval($this->getSingleXPathResult($this->dropFolder->feedItemInfo->itemHashXPath, $feedItem));
		if ($feedItemHash)
		{
			KalturaLog::info('Hash found- checking whether content needs to be updated');
			if ($feedItemHash != $existingDropFolderFile->hash)
			{
				KalturaLog::info('Hash has changed- content will be updated.');
				$this->handleItemAdded($feedItem);
				return true;
			}
		}
		
		//check whether the publish date has changed - in this case the metadata needs to be updated
		if ($feedItem->pubDate != $existingDropFolderFile->lastModificationTime)
		{
			$this->handleItemAdded($feedItem, false);
			return true;
		}
		
		$retryStatuses = array (KalturaDropFolderFileStatus::DELETED, KalturaDropFolderFileStatus::PURGED, KalturaDropFolderFileStatus::ERROR_HANDLING);
		if (in_array ($existingDropFolderFile->status, $retryStatuses))
		{
			KalturaLog::info("File status condition met- retrying");
			$this->handleItemAdded($feedItem, false);
			return true;
		}
		
		//If neither of the conditions above were true, neither the metadata nor the content were changed- do nothing.
		return false;
	}

	/* (non-PHPdoc)
	 * @see KDropFolderEngine::processFolder()
	 */
	public function processFolder(KalturaBatchJob $job, KalturaDropFolderContentProcessorJobData $data) {
		// TODO Auto-generated method stub
		
	}
	
	/**
	 * @param string $fieldName
	 * @return SimpleXMLElement 
	 */
	protected function getSingleXPathResult ($fieldXpath, SimpleXMLElement $element)
	{
		if (!$fieldXpath)
		{
			KalturaLog::info("XPath not provided.");
			return null;
		}
		$itemXPathRes = $element->xpath ($fieldXpath);
		if (count ($itemXPathRes))
			return $itemXPathRes[0];
			
		return null;
	}

	protected function getMaxFeedBitrate (SimpleXMLElement $feedItem)
	{
		$allBitrates = $feedItem->xpath ($this->dropFolder->feedItemInfo->itemContentBitrateXPath);
		if (!count($allBitrates))
		{
			KalturaLog::info("No bitrate tags found ");
		}
		
		$bitrates = array();
		foreach ($allBitrates as $bitrate)
		{
			$bitrates[] = intval ($bitrate);
		}
		
		return max ($bitrates);
	}
}