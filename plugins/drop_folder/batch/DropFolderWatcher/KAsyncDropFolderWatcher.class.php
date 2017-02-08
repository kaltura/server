<?php
/**
 * Watches drop folder files and executes file handlers as required 
 *
 * @package plugins.dropFolder
 * @subpackage Scheduler
 */
class KAsyncDropFolderWatcher extends KPeriodicWorker
{
	/**
	 * @var KalturaDropFolderClientPlugin
	 */
	protected $dropFolderPlugin = null;
	
			
	/* (non-PHPdoc)
	 * @see KBatchBase::getType()
	 */
	public static function getType()
	{
		return KalturaBatchJobType::DROP_FOLDER_WATCHER;
	}
	
	/* (non-PHPdoc)
	 * @see KBatchBase::run()
	*/
	public function run($jobs = null)
	{
		$this->dropFolderPlugin = KalturaDropFolderClientPlugin::get(self::$kClient);
		
		if(self::$taskConfig->isInitOnly())
			return $this->init();


		$numberOfFoldersEachRun = self::$taskConfig->numberOfFoldersEachRun;
		KalturaLog::log("Start running to watch $numberOfFoldersEachRun folders");
		for ($i = 0; $i < $numberOfFoldersEachRun; $i++)
		{
			try 
			{
				/* @var $folder KalturaDropFolder */
				$folder = $this->getExclusiveDropFolder();
				$this->impersonate($folder->partnerId);	
				$engine = KDropFolderEngine::getInstance($folder->type);			    	
				$engine->watchFolder($folder);					    
				$this->freeExclusiveDropFolder($folder->id);		
				$this->unimpersonate();					    
			}
			catch (kFileTransferMgrException $e)
			{
				KalturaLog::err('Error with folder id ['.$folder->id.'] - '.$e->getMessage());
				if($e->getCode() == kFileTransferMgrException::cantConnect)
					$this->freeExclusiveDropFolder($folder->id, KalturaDropFolderStatus::ERROR,
						KalturaDropFolderErrorCode::ERROR_CONNECT, DropFolderPlugin::ERROR_CONNECT_MESSAGE);
				else if($e->getCode() == kFileTransferMgrException::cantAuthenticate)
					$this->freeExclusiveDropFolder($folder->id, KalturaDropFolderStatus::ERROR,
						KalturaDropFolderErrorCode::ERROR_AUTENTICATE, DropFolderPlugin::ERROR_AUTENTICATE_MESSAGE);
				else
					$this->freeExclusiveDropFolder($folder->id, KalturaDropFolderStatus::ERROR,
						KalturaDropFolderErrorCode::ERROR_GET_PHISICAL_FILE_LIST, DropFolderPlugin::ERROR_GET_PHISICAL_FILE_LIST_MESSAGE);
				$this->unimpersonate();
			}
			catch (KalturaException $e)
			{
				KalturaLog::err('Error with folder id ['.$folder->id.'] - '.$e->getMessage());
				$this->freeExclusiveDropFolder($folder->id, KalturaDropFolderStatus::ERROR, 
					KalturaDropFolderErrorCode::ERROR_GET_DB_FILE_LIST, DropFolderPlugin::ERROR_GET_DB_FILE_LIST_MESSAGE);
				$this->unimpersonate();
			}
			catch (Exception $e) 
			{
				KalturaLog::err('Error with folder id ['.$folder->id.'] - '.$e->getMessage());
				$this->freeExclusiveDropFolder($folder->id, KalturaDropFolderStatus::ERROR, 
					KalturaDropFolderErrorCode::DROP_FOLDER_APP_ERROR, DropFolderPlugin::DROP_FOLDER_APP_ERROR_MESSAGE.$e->getMessage());
				$this->unimpersonate();
			}
		}
		
	}
	
		
	private function getExclusiveDropFolder() 
	{
		$folderTag = self::$taskConfig->params->tags;
		$maxTimeForFolder = self::$taskConfig->params->maxTimeForFolder;
		if (strlen($folderTag) == 0 || $folderTag == '*')
			throw new KalturaException('Tags must be specify in configuration - cannot continue');
		
		try 
		{
			$dropFolders = $this->dropFolderPlugin->dropFolder->getExclusiveDropFolder($folderTag, $maxTimeForFolder);
			return $dropFolders;
		}
		catch (Exception $e) 
		{
			KalturaLog::err('Cannot get drop folder list - '.$e->getMessage());
			return null;
		}
	}
	
	private function freeExclusiveDropFolder($dropFolderId, $status = KalturaDropFolderStatus::ENABLED , $errorCode = null, $errorDescription = null)
	{
		try 
		{
	    	$this->dropFolderPlugin->dropFolder->freeExclusiveDropFolder($dropFolderId, $status, $errorCode, $errorDescription);
		}
		catch(Exception $e)
		{
			KalturaLog::err('Error when trying to free drop folder [$dropFolderId] - '.$e->getMessage());
		}	
	}	
			
	function log($message)
	{
		if(!strstr($message, 'KalturaDropFolderListResponse') && !strstr($message, 'KalturaDropFolderFileListResponse'))
			KalturaLog::info($message);
	}	
}
