<?php
/**
 * 
 */
class KMRSSDropFolderEngine extends KDropFolderEngine 
{
	const MRSS_NS = 'http://search.yahoo.com/mrss/';
	/* (non-PHPdoc)
	 * @see KDropFolderEngine::watchFolder()
	 */
	public function watchFolder(KalturaDropFolder $dropFolder) {
		KalturaLog::info("Watching drop folder with ID [" . $dropFolder->id . "]");
		$this->dropFolder = $dropFolder;
		
		//Get Drop Folder feed and import it into a SimpleXMLElement
		/* @var KalturaMRSSDropFolder $dropFolder */
		$feed = new SimpleXMLElement (file_get_contents($dropFolder->mrssUrl));
		
		//get items
		$feedItems = $feed->xpath ('/rss/channel/item');
		$existingDropFolderFilesMap = $this->loadDropFolderFiles();
		
		foreach ($feedItems as $feedItem)
		{
			// The unique feed item identifier is the GUID, so that is what we set as the drop folder file name.
			if (!array_key_exists($feedItem->guid, $existingDropFolderFilesMap))
			{
				//In this case, we are required to add this item as a new drop folder file
				$this->handleItemAdded ($feedItem);
			}
			else
			{
				if (isset ($this->dropFolder->fileHandlerConfig->contentMatchPolicy) )
				{
					if ($this->fileHandlerConfig->contentMatchPolicy != KalturaDropFolderContentFileHandlerMatchPolicy::ADD_AS_NEW)
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
			$feedPath = $this->saveFeedItemToDisk ($feedItem, $contentUpdateRequired);
			
			$newDropFolderFile = new KalturaMRSSDropFolderFile();
	    	$newDropFolderFile->dropFolderId = $this->dropFolder->id;
	    	$newDropFolderFile->fileName = strval($feedItem->guid);
	    	$newDropFolderFile->lastModificationTime = strval($feedItem->pubDate); 
	    	$newDropFolderFile->hash = strval($feedItem->children(self::MRSS_NS)->hash);
	    	$newDropFolderFile->xmlLocalPath = $feedPath;
			$dropFolderFile = $this->dropFolderFileService->add($newDropFolderFile);
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
			KalturaLog::debug();
		}
		
		$feedItemPath = KBatchBase::$taskConfig->params->mrss->xmlPath . DIRECTORY_SEPARATOR. $feedItem->guid . '_' . date();
		file_put_contents($feedItemPath, $feedItem);
		
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
	}

	/* (non-PHPdoc)
	 * @see KDropFolderEngine::processFolder()
	 */
	public function processFolder(KalturaBatchJob $job, KalturaDropFolderContentProcessorJobData $data) {
		// TODO Auto-generated method stub
		
	}

	
}