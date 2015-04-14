<?php
/**
 * @package plugins.DropFolderMrss
 */
class KMrssDropFolderEngine extends KDropFolderEngine 
{
	/**
	 * @var array
	 */
	protected $mrssNamespaces;
	
	static $searchCharacters = array ('/');
	static $replaceCharacters = array('_');
	
	/* (non-PHPdoc)
	 * @see KDropFolderEngine::watchFolder()
	 */
	public function watchFolder(KalturaDropFolder $dropFolder) {
		KalturaLog::info("Watching drop folder with ID [" . $dropFolder->id . "]");
		$this->dropFolder = $dropFolder;
		
		//Get Drop Folder feed and import it into a SimpleXMLElement
		
		$feed = new SimpleXMLElement (file_get_contents($dropFolder->path));
		$this->mrssNamespaces = $feed->getNamespaces(true);
		
		//get items
		$feedItems = $feed->xpath ('/rss/channel/item');
		$existingDropFolderFilesMap = $this->loadDropFolderFiles();
		
		foreach ($feedItems as $feedItem)
		{
			// The unique feed item identifier is the GUID, so that is what we set as the drop folder file name.
			if (!array_key_exists(strval($feedItem->guid), $existingDropFolderFilesMap))
			{
				//In this case, we are required to add this item as a new drop folder file
				$this->handleItemAdded ($feedItem);
			}
			else
			{
				if (!isset ($this->dropFolder->fileHandlerConfig->contentMatchPolicy) )
				{
					KalturaLog::info('Content match policy is not set for [' . $this->dropFolder->id . '] - assume ADD_AS_NEW.');
					continue;
				}
				else
				{
					if ($this->fileHandlerConfig->contentMatchPolicy == KalturaDropFolderContentFileHandlerMatchPolicy::ADD_AS_NEW)
					{
						KalturaLog::info('No need to process- content match policy for drop folder id [' . $this->dropFolder->id . '] does not include updates.');
						continue;
					}	
				}
				
				$this->handleExistingItem ($existingDropFolderFilesMap[$feedItem->guid], $feedItem);
			}
		}
	}
	
	
	/**
	 * Add a new item from the MRSS feed
	 * @param SimpleXMLElement $feedItem
	 * @param bool $contentUpdateRequired
	 * @return Ambigous <KalturaDropFolderFile, MultiRequestSubResult, unknown, NULL, multitype:, multitype:string unknown , multitype:mixed string >|NULL
	 */
	protected function handleItemAdded (SimpleXMLElement $feedItem, $contentUpdateRequired = true)
	{
		KalturaLog::debug('Add drop folder file ['.$fileName.'] last modification time ['.$lastModificationTime.'] file size ['.$fileSize.']');
		try 
		{
			//Register MRSS media namespace on the separate <item>
			$feedItem->addAttribute('xmlns:xmlns:media', self::MRSS_NS);
			$feedPath = $this->saveFeedItemToDisk ($feedItem, $contentUpdateRequired);
			
			$newDropFolderFile = new KalturaMrssDropFolderFile();
	    	$newDropFolderFile->dropFolderId = $this->dropFolder->id;
	    	$newDropFolderFile->fileName = strval($feedItem->guid);
	    	$newDropFolderFile->lastModificationTime = strval($feedItem->pubDate); 
	    	
	    	if (isset ($feedItem->children('media', true)->content[0]->attributes()->fileSize ))
	    	{
	    		$newDropFolderFile->fileSize = intval($feedItem->children(self::MRSS_NS)->content[0]->attributes()->fileSize);
	    	}
	    	else 
	    	{
	    		if (!isset($feedItem->children(self::MRSS_NS)->content[0]->attributes()->url))
	    		{
	    			throw new Exception ("Cannot add drop folder file - content URL does not exist");
	    		}
	    		$contentUrl = strval($feedItem->children(self::MRSS_NS)->content[0]->attributes()->url);
	
				$curl = curl_init($contentUrl);
				curl_setopt($curl, CURLOPT_HEADER, true);
			    curl_setopt($curl, CURLOPT_FILETIME, true);
			    curl_setopt($curl, CURLOPT_NOBODY, true);
				$res = curl_exec($curl);
				if ($res)
				{
					$curlInfo = curl_getinfo($curl);
					$newDropFolderFile->fileSize = intval($curlInfo['download_content_length']);
				}
				
				curl_close($curl);
	    	}
	    	
	    	$newDropFolderFile->hash = strval($feedItem->children(self::MRSS_NS)->hash);
	    	$newDropFolderFile->mrssXmlPath = $feedPath;
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
			unset($feedItem->content[0][0]);
			KalturaLog::debug("Removing content tags from MRSS");
		}
		
		$updatedGuid = str_replace (self::$searchCharacters, self::$replaceCharacters, strval ($feedItem->guid));
		
		$feedItemPath = KBatchBase::$taskConfig->params->mrss->xmlPath . DIRECTORY_SEPARATOR. $updatedGuid . '_' . time();
		file_put_contents($feedItemPath, $feedItem->saveXML());
		chmod($feedItemPath, 0660);
		
		return $feedItemPath;
	}
	
	/**
	 * Decide whether to update content/metadata of an existing drop folder file
	 * @param KalturaDropFolderFile $existingDropFolderFile
	 * @param SimpleXMLElement $feedItem
	 */
	protected function handleExistingItem (KalturaMRSSDropFolderFile $existingDropFolderFile, SimpleXMLElement $feedItem)
	{
		//check whether the hash has changed - in this case the content needs to be updated.
		$feedItemHash = strval($feedItem->children(self::MRSS_NS)->hash);
		if ($feedItemHash)
		{
			KalturaLog::info('Hash found- checking whether content needs to be updated');
			if ($feedItemHash != $existingDropFolderFile->hash)
			{
				KalturaLog::info('Hash has changed- content will be updated.');
				$this->handleItemAdded($feedItem);
				return;
			}
		}
		
		//check whether the publish date has changed - in this case the metadata needs to be updated
		if ($feedItem->pubDate != $existingDropFolderFile->lastModificationTime)
		{
			$this->handleItemAdded($feedItem, false);
		}
		
		//If neither of the conditions above were true, neither the metadata nor the content were changed- do nothing.
	}

	/* (non-PHPdoc)
	 * @see KDropFolderEngine::processFolder()
	 */
	public function processFolder(KalturaBatchJob $job, KalturaDropFolderContentProcessorJobData $data) {
		// TODO Auto-generated method stub
		
	}

	
}