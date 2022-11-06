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

	
	protected function initWebexClient(KalturaDropFolder $dropFolder)
	{
		$refreshToken = isset($dropFolder->refreshToken) ? $dropFolder->refreshToken : null;
		$accessToken = isset($dropFolder->accessToken) ? $dropFolder->accessToken : null;
		$clientId = isset($dropFolder->clientId) ? $dropFolder->clientId : null;
		$clientSecret = isset($dropFolder->clientSecret) ? $dropFolder->clientSecret : null;
		$accessExpiresIn = isset($dropFolder->accessExpiresIn) ? $dropFolder->accessExpiresIn : null;
		return new kWebexAPIClient($dropFolder->baseURL, $refreshToken, $clientId, $clientSecret, $accessToken, $accessExpiresIn);
	}
	
	public function watchFolder(KalturaDropFolder $dropFolder)
	{
		$this->webexClient = $this->initWebexClient($dropFolder);
		$this->dropFolder = $dropFolder;
		KalturaLog::info('Watching folder [' . $this->dropFolder->id . ']');
		
		$list = $this->webexClient->getRecordings($this->accessToken);
		KalturaLog::info(print_r($list));
		
		//$this->handleExistingDropFolderFiles();
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
