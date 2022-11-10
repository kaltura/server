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
		
		$response = $this->webexClient->getRecordingsList();
		KalturaLog::info('Response from Webex recordings: ' . print_r($response));
		$recordingsList = json_decode($response, true);
		$items = $recordingsList['items'];
		foreach ($items as $item)
		{
			KalturaLog::info($item['meetingId']);
			KalturaLog::info($item['createTime']);
			KalturaLog::info($item['topic']);
			KalturaLog::info($item['format']);
			KalturaLog::info($item['serviceType']);
			
			$response = $this->webexClient->getRecording($item['id']);
			KalturaLog::info('Response from Webex recordings: ' . print_r($response));
			$recordingInfo = json_decode($response, true);
			KalturaLog::info(print_r($recordingInfo));
			
			$recordingFileName = $recordingInfo['topic'];
			$dropFolderFilesMap = $this->loadDropFolderFiles($recordingFileName);
			if (count($dropFolderFilesMap) === 0)
			{
				$this->addDropFolderFile($recordingInfo);
			}
		}
		
		
		
		self::updateDropFolderLastMeetingHandled(time());
		
		//$this->handleExistingDropFolderFiles();
	}
	
	protected function addDropFolderFile($recordingInfo)
	{
		try
		{
			$webexDropFolderFile = $this->allocateWebexDropFolderFile($recordingInfo);
			
			KalturaLog::debug("Adding new WebexDropFolderFile: " . print_r($webexDropFolderFile, true));
			$dropFolderFile = $this->dropFolderFileService->add($webexDropFolderFile);
			return $dropFolderFile;
		}
		catch (Exception $e)
		{
			KalturaLog::err('Cannot add new drop folder file with name ['. $recordingInfo['topic'] .'] - '.$e->getMessage());
			return null;
		}
	}
	
	protected function allocateWebexDropFolderFile($recordingInfo)
	{
		$webexDropFolderFile = new KalturaWebexDropFolderFile();
		$webexDropFolderFile->dropFolderId = $this->dropFolder->id;
		$webexDropFolderFile->fileName = $recordingInfo['topic'];
		$webexDropFolderFile->fileSize = $recordingInfo['sizeBytes'];
		$webexDropFolderFile->contentUrl = $recordingInfo['temporaryDirectDownloadLinks']['recordingDownloadLink'];
		return $webexDropFolderFile;
	}
	
	protected function updateDropFolderLastMeetingHandled($lastHandledMeetingTime)
	{
		$updateDropFolder = new KalturaWebexAPIDropFolder();
		$updateDropFolder->lastHandledMeetingTime = $lastHandledMeetingTime;
		$this->dropFolderPlugin->dropFolder->update($this->dropFolder->id, $updateDropFolder);
		KalturaLog::debug("Last handled meetings time is: $lastHandledMeetingTime");
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
