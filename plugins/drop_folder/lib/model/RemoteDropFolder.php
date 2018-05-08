<?php

/**
* @package plugins.dropFolder
* @subpackage model
*/
abstract class RemoteDropFolder extends DropFolder
{
	abstract protected function getRemoteFileTransferMgrType();
	
	/**
	 * @return kFileTransferMgrType
	 */
	public function getFileTransferMgrType()
	{
		return $this->getRemoteFileTransferMgrType();
	}
			
	public function loginByCredentialsType(kFileTransferMgr $fileTransferMgr)
	{
		return false;
	}
		
	/**
	 * @return kDropFolderImportJobData
	 */
	abstract public function getImportJobData();
	
	/**
	 * @return string
	 */
	abstract public function getFolderUrl();
	
	public function getLocalFilePath($fileName, $fileId, kFileTransferMgr $fileTransferMgr, $fileSizeRemoteFile)
	{
		$getFileRetries = 0 ;
		$dropFolderFilePath = $this->getPath().'/'.$fileName;
		$tempDirectory = sys_get_temp_dir();
		if (is_dir($tempDirectory)) 
		{
			$tempFilePath = tempnam($tempDirectory, 'parse_dropFolderFileId_'.$fileId.'_');		

			do
			{
				$fileTransferResult = $fileTransferMgr->getFile($dropFolderFilePath, $tempFilePath, $fileSizeRemoteFile);
				$getFileRetries++;

			} while ( ($fileTransferResult == kFileTransferMgr::FILETRANSFERMGR_RES_ERR) && ($getFileRetries < 3) );

			return $tempFilePath;
		}
		else
		{
			KalturaLog::err('Missing temporary directory');
			return null;			
		}
	}
	
	
}
