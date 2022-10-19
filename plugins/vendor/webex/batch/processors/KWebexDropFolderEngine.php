<?php
/**
 * @package plugins.WebexAPIDropFolder
 */
class KWebexAPIDropFolderEngine extends KDropFolderFileTransferEngine
{
	
	
	/**
	 * @var kWebexAPIClient
	 */
	protected $webexClient;
	
	public function watchFolder(KalturaDropFolder $dropFolder)
	{
	}

	protected function handleExistingDropFolderFile (KalturaDropFolderFile $dropFolderFile)
	{
	}
	
	protected function purgeFile(KalturaDropFolderFile $dropFolderFile)
	{
	}

	public function processFolder (KalturaBatchJob $job, KalturaDropFolderContentProcessorJobData $data)
	{
	}
}
