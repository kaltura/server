<?php
/**
 * @package plugins.ApFeedDropFolder
 */
class KApFeedDropFolderEngine extends KFeedDropFolderEngine
{
	/**
	 * The API Key for the AP feed being processed.
	 * @var string
	 */
	protected $apiKey;
	
	const AP_FEED_PAGE_SIZE = 100;
	
	public function watchFolder(KalturaDropFolder $dropFolder)
	{
		/* @var $dropFolder KalturaApFeedDropFolder */
		KalturaLog::info('Watching drop folder with ID [' . $dropFolder->id . ']');
		$this->dropFolder = $dropFolder;
		$this->apiKey = $dropFolder->apApiKey;
		
		$feedUrl = $this->dropFolder->path;
		
		$existingDropFolderFilesMap = $this->loadDropFolderFiles();
		
		$counter = 0;
		$break = false;
		
		do
		{
			//Get Drop Folder feed and import it into an array
			$feedContent = $this->fetchFeedContent($feedUrl);
			$feed = json_decode($feedContent, true);
			if (!$feed)
			{
				KalturaLog::info('Feed page could not be displayed, no way to continue. Breaking out of the process.');
				break;
			}
			$lastPageCount = $feed['data']['current_item_count'];
			
			$feedItems = $feed['data']['items'];
			
			foreach ($feedItems as $part)
			{
				
				if ($counter > intval ( KBatchBase::$taskConfig->params->mrss->limitProcessEachRun))
				{
					KalturaLog::info('Process limit reached.');
					$break = true;
					break;
				}
				
				$datum = $part['item'];
				$feedItemJson = json_decode($this->fetchFeedContent($datum['uri']), true);
				if (!$feedItemJson)
				{
					KalturaLog::info('Feed item could not be retrieved, continue to the next one.');
					continue;
				}
				
				$feedItem = new DOMDocument();
				$feedItem->loadXML('<item/>');
				
				$this->arrayToXml($feedItemJson['data']['item'], $feedItem->documentElement);
				
				KalturaLog::info ('Single item: ' . print_r($feedItem->saveXML(), true));
				
				$counter += $this->watchProcessSingleItem(simplexml_import_dom($feedItem), $existingDropFolderFilesMap);
				
			}
			
			if($break)
			{
				KalturaLog::info('Finished current run.');
				break;
			}
			
			if (isset($feed['data']['next_page']))
			{
				$feedUrl = $feed['data']['next_page'];
			}
			
		}while ($lastPageCount == self::AP_FEED_PAGE_SIZE);
		
		foreach ($existingDropFolderFilesMap as $existingDropFolderFile)
		{
			$this->handleFeedItemPurged($existingDropFolderFile);
		}
	}
	
	public function processFolder(KalturaBatchJob $job, KalturaDropFolderContentProcessorJobData $data)
	{
		// TODO Auto-generated method stub
	}
	
	/**
	 * @param string $url
	 * @return string
	 */
	protected function fetchFeedContent ($url)
	{
		$ch = curl_init ($url . '&apiKey=' . $this->apiKey);
		
		curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
		
		if (isset(KBatchBase::$taskConfig->params->mrss->curlTimeout))
		{
			curl_setopt($ch, CURLOPT_TIMEOUT, KBatchBase::$taskConfig->params->mrss->curlTimeout);
		}
		
		$res = curl_exec($ch);
		curl_close ($ch);
		
		if(is_string($res))
		{
			KalturaLog::info("For URL [$url], the curl result is: " . substr($res, 0, 1000));
		}
		
		return $res;
	}

	
	protected function arrayToXml ($array, DOMElement $parentElement)
	{
		foreach ($array as $key => $item)
		{
			if (is_numeric($key))
			{
				$newElement = $parentElement->ownerDocument->createElement('arrayItem');
			}
			else
			{
				$newElement = $parentElement->ownerDocument->createElement($key);
			}
			
			if (is_array($item))
			{
				$this->arrayToXml($item, $newElement);
			}
			else
			{
				$value = $newElement->ownerDocument->createTextNode($item);
				$newElement->appendChild($value);
			}
			
			$parentElement->appendChild($newElement);
		}
	}
}