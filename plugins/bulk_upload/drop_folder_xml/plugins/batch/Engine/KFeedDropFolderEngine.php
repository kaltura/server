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
	
	protected $handledUniqueIds = array();
	
	/* (non-PHPdoc)
	 * @see KDropFolderEngine::watchFolder()
	 */
	public function watchFolder(KalturaDropFolder $dropFolder) {
		/* @var $dropFolder KalturaFeedDropFolder */		
		KalturaLog::info("Watching drop folder with ID [" . $dropFolder->id . "]");
		$this->dropFolder = $dropFolder;
		
		//Get Drop Folder feed and import it into a SimpleXMLElement
		
		$feed = new SimpleXMLElement ($this->fetchFeedContent($dropFolder->path));
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
			//If we already encountered this uniqueId in this run- ignore subsequent iterations.
			if (in_array ($uniqueId, $this->handledUniqueIds))
			{
				KalturaLog::err("The unique identifer value [$uniqueId] was encountered before during this scan of the feed. Ignoring.");
				continue;
			}
			
			// The unique feed item identifier is the GUID, so that is what we set as the drop folder file name.
			if (!array_key_exists($uniqueId, $existingDropFolderFilesMap))
			{
				//In this case, we are required to add this item as a new drop folder file
				KalturaLog::info("Item not found in drop folder file list- adding as new drop folder file.");
				$this->handleItemAdded ($uniqueId, $feedItem);
				$counter++;
			}
			else
			{
				KalturaLog::info("Item found in drop folder file list- adding as existing drop folder file.");
				$dropFolderFile = $existingDropFolderFilesMap[$uniqueId];
				unset ($existingDropFolderFilesMap[$uniqueId]);
				//if file exist in the folder remove it from the map
				//all the files that are left in a map will be marked as PURGED					
				if ($this->handleExistingItem($dropFolderFile, $feedItem))
					$counter++;
			}
			
			$this->handledUniqueIds[] = $uniqueId;
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
		try 
		{
			$url = $this->getSingleXPathResult($this->dropFolder->feedItemInfo->itemContentUrlXPath, $feedItem);
    		if (is_null ($url))
    		{
    			throw new Exception ("Cannot add drop folder file - content URL does not exist");
    		}
			
			//Register MRSS media namespaces on the separate <item>
			foreach ($this->feedNamespaces as $nameSpace => $url)
			{
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
					$bitrateValue = intval($this->getSingleXPathResult($this->dropFolder->feedItemInfo->contentBitrateAttributeName, $contentItem));
					if ($bitrateValue != $maxBitrate)
						unset ($contentItem[0]);
				}
			}
			
		}
		
		$feedFileName = uniqid ("dropFolderFile_{$this->dropFolder->id}_" . time() . '_');
		
		$feedItemPath = KBatchBase::$taskConfig->params->mrss->xmlPath . DIRECTORY_SEPARATOR. $feedFileName;
		$res = file_put_contents($feedItemPath, $feedItem->saveXML());
		chmod($feedItemPath, KBatchBase::$taskConfig->chmod ? octdec(KBatchBase::$taskConfig->chmod) : 0660);
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
			if ($feedItemHash != $existingDropFolderFile->hash)
			{
				KalturaLog::info('Hash has changed for drop folder file named ['. $existingDropFolderFile->fileName .'] - content will be updated.');
				$this->handleItemAdded($existingDropFolderFile->fileName, $feedItem);
				return true;
			}
		}
		
		//check whether the publish date has changed - in this case the metadata needs to be updated
		$pubDate = strval($this->getSingleXPathResult($this->dropFolder->feedItemInfo->itemPublishDateXPath, $feedItem));
		if ($pubDate != $existingDropFolderFile->lastModificationTime)
		{
			KalturaLog::info('Publish date has changed for drop folder file named ['. $existingDropFolderFile->fileName .'] - content will be updated.');
			$this->handleItemAdded($existingDropFolderFile->fileName, $feedItem, false);
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
	
	/**
	 * @param string $url
	 * @return string
	 */
	protected function fetchFeedContent ($url)
	{
		$user = parse_url ($url, PHP_URL_USER);
		if (!is_null ($user))
		{
			$password = parse_url ($url, PHP_URL_PASS);
			$urlComponents = parse_url ($url); 
			$protocol = isset ($urlComponents['scheme']) ? $urlComponents['scheme'] : null;
			$hostname = isset($urlComponents ['host']) ? $urlComponents ['host'] : null;
			$port = isset ($urlComponents['port']) ? $urlComponents['port'] : null;
			$params = isset ($urlComponents['path']) ? $urlComponents['path'] : null;
			$queryArgs = isset ($urlComponents['query']) ? $urlComponents['query'] : null;
			$fragment = isset ($urlComponents ['fragment']) ? $urlComponents ['fragment'] : null;

			$url =  "$protocol://$hostname" .  ($port? ":$port" : "") . ($params ? $params : "") . ($queryArgs ? "?$queryArgs" : "") . ($fragment ? "#$fragment" : "");
		}
		
		$ch = curl_init ($url);
		if (!is_null ($user))
		{
			curl_setopt($ch, CURLOPT_USERPWD, "$user:$password");
			curl_setopt ($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
		}
		curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
		
		$res = curl_exec($ch);
		curl_close ($ch);
		
		KalturaLog::info("For URL [$url], the curl result is: " . print_r($res, true));
		return $res;
	}
	
	
}