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
		
		$dropFolders = $this->getDropFoldersList();
		if(isset($dropFolders))
		{
			$dropFolders = $dropFolders->objects;
			KalturaLog::log('['.count($dropFolders).'] folders to watch');
			
			foreach ($dropFolders as $folder)
			{
				/* @var $folder KalturaDropFolder */
			    try 
			    {	
			    	$this->impersonate($folder->partnerId);	
					$engine = KDropFolderEngine::getInstance($folder->type);			    	
				    $engine->watchFolder($folder);					    
				    $this->setDropFolderOK($folder);		
					$this->unimpersonate();					    
			    }
			    catch (kFileTransferMgrException $e)
			    {
			    	if($e->getCode() == kFileTransferMgrException::cantConnect)
			    		$this->setDropFolderError($folder, KalturaDropFolderErrorCode::ERROR_CONNECT, DropFolderPlugin::ERROR_CONNECT_MESSAGE, $e);
			    	else if($e->getCode() == kFileTransferMgrException::cantAuthenticate)
			    		$this->setDropFolderError($folder, KalturaDropFolderErrorCode::ERROR_AUTENTICATE, DropFolderPlugin::ERROR_AUTENTICATE_MESSAGE, $e);
			    	else
			    		$this->setDropFolderError($folder, KalturaDropFolderErrorCode::ERROR_GET_PHISICAL_FILE_LIST, DropFolderPlugin::ERROR_GET_PHISICAL_FILE_LIST_MESSAGE, $e);
			    	$this->unimpersonate();
			    }
			    catch (KalturaException $e)
			    {
			    	$this->setDropFolderError($folder, KalturaDropFolderErrorCode::ERROR_GET_DB_FILE_LIST, DropFolderPlugin::ERROR_GET_DB_FILE_LIST_MESSAGE, $e);
			    	$this->unimpersonate();
			    }
			    catch (Exception $e) 
			    {			        
			        $this->setDropFolderError($folder, KalturaDropFolderErrorCode::DROP_FOLDER_APP_ERROR, DropFolderPlugin::DROP_FOLDER_APP_ERROR_MESSAGE.$e->getMessage(), $e);	
			        $this->unimpersonate();
			    }
			}
		}
	}
	
		
	private function getDropFoldersList() 
	{
		$folderTags = self::$taskConfig->params->tags;
		
		if (strlen($folderTags) == 0) {		
			KalturaLog::err('Tags configuration is empty - cannot continue');			
			return null;
		}
		
		// get list of drop folders according to configuration
		$filter = new KalturaDropFolderFilter();
		
		if ($folderTags != '*') {
			$filter->tagsMultiLikeOr = $folderTags;
		}
			
		$filter->currentDc = KalturaNullableBoolean::TRUE_VALUE;
		$filter->statusIn = KalturaDropFolderStatus::ENABLED. ','. KalturaDropFolderStatus::ERROR;
		
		$pager = new KalturaFilterPager();
		$pager->pageSize = 500;
		if(self::$taskConfig->params->pageSize)
			$pager->pageSize = self::$taskConfig->params->pageSize;	
		
		
		try 
		{
			$dropFolders = $this->dropFolderPlugin->dropFolder->listAction($filter, $pager);
			return $dropFolders;
		}
		catch (Exception $e) 
		{
			KalturaLog::err('Cannot get drop folder list - '.$e->getMessage());
			return null;
		}
	}
	
	private function setDropFolderError(KalturaDropFolder $folder, $errorCode, $errorDescirption, Exception $e)
	{
		KalturaLog::err('Error with folder id ['.$folder->id.'] - '.$e->getMessage());
		try 
		{
			$folder->status = KalturaDropFolderStatus::ERROR;
			$updateDropFolder = new KalturaDropFolder();
			$updateDropFolder->status = KalturaDropFolderStatus::ERROR;
			$updateDropFolder->errorCode = $errorCode;
			$updateDropFolder->errorDescription = $errorDescirption;
			$updateDropFolder->lastAccessedAt = time();
			
    		$this->dropFolderPlugin->dropFolder->update($folder->id, $updateDropFolder);
		}
		catch(Exception $e)
		{
			KalturaLog::err('Error updating drop folder ['.$folder->id.'] - '.$e->getMessage());
		}	
	}	
	
	private function setDropFolderOK(KalturaDropFolder $folder)
	{
		try 
		{
			$updateDropFolder = new KalturaDropFolder();
			$updateDropFolder->status = KalturaDropFolderStatus::ENABLED;
			$updateDropFolder->errorCode__null = '';
			$updateDropFolder->errorDescription__null = '';
			$updateDropFolder->lastAccessedAt = time();
				
	    	$this->dropFolderPlugin->dropFolder->update($folder->id, $updateDropFolder);
		}
		catch(Exception $e)
		{
			KalturaLog::err('Error updating drop folder ['.$folder->id.'] - '.$e->getMessage());
		}	
	}	
			
	function log($message)
	{
		if(!strstr($message, 'KalturaDropFolderListResponse') && !strstr($message, 'KalturaDropFolderFileListResponse'))
			KalturaLog::info($message);
	}	
}
