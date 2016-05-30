<?php
/**
 * @package plugins.scheduleDropFolder
 * @subpackage batch
 */
class BulkUploadEngineDropFolderICal extends BulkUploadEngineICal
{
	/**
	 *
	 * @var KalturaDropFolder
	 */
	private $dropFolder = null;
	
	/**
	 *
	 * @var kFileTransferMgr
	 */
	private $fileTransferMgr = null;
	
	public function __construct(KalturaBatchJob $job)
	{
		parent::__construct($job);
		
		KBatchBase::impersonate($this->currentPartnerId);
		$dropFolderPlugin = KalturaDropFolderClientPlugin::get(KBatchBase::$kClient);
		KBatchBase::$kClient->startMultiRequest();
		$dropFolderFile = $dropFolderPlugin->dropFolderFile->get($this->job->jobObjectId);
		$dropFolderPlugin->dropFolder->get($dropFolderFile->dropFolderId);
		list($dropFolderFile, $this->dropFolder) = KBatchBase::$kClient->doMultiRequest();
		
		$this->fileTransferMgr = KDropFolderFileTransferEngine::getFileTransferManager($this->dropFolder);
		$this->data->filePath = $this->getLocalFilePath($dropFolderFile->fileName, $dropFolderFile->id);
		
		KBatchBase::unimpersonate();
	}
	
	/**
	 * Local drop folder - constract full path
	 * Remote drop folder - download file to a local temp directory and return the temp file path
	 * 
	 * @param string $fileName        	
	 * @param int $fileId        	
	 * @throws Exception
	 */
	protected function getLocalFilePath($fileName, $fileId)
	{
		$dropFolderFilePath = $this->dropFolder->path . '/' . $fileName;
		
		// local drop folder
		if($this->dropFolder->type == KalturaDropFolderType::LOCAL)
		{
			$dropFolderFilePath = realpath($dropFolderFilePath);
			return $dropFolderFilePath;
		}
		else
		{
			// remote drop folder
			$tempFilePath = tempnam(KBatchBase::$taskConfig->params->sharedTempPath, 'parse_dropFolderFileId_' . $fileId . '_');
			$this->fileTransferMgr->getFile($dropFolderFilePath, $tempFilePath);
			$this->setFilePermissions($tempFilePath);
			return $tempFilePath;
		}
	}
	
	protected function setFilePermissions($filepath)
	{
		$chmod = 0640;
		if(KBatchBase::$taskConfig->getChmod())
			$chmod = octdec(KBatchBase::$taskConfig->getChmod());
		
		KalturaLog::info("chmod($filepath, $chmod)");
		@chmod($filepath, $chmod);
		
		$chown_name = KBatchBase::$taskConfig->params->fileOwner;
		if($chown_name)
		{
			KalturaLog::info("Changing owner of file [$filepath] to [$chown_name]");
			@chown($filepath, $chown_name);
		}
	}
}