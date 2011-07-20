<?php

/**
* @package plugins.dropFolder
* @subpackage model
*/
abstract class RemoteDropFolder extends DropFolder
{
	
	public function getLocalPath()
	{
	    return $this->getTempLocalPath();
	}
	
	/**
	 * @return kFileTransferMgrType
	 */
	abstract public function getFileTransferMgrType();
	
	/**
	 * @return kDropFolderImportJobData
	 */
	abstract public function getImportJobData();
	
	/**
	 * @return string
	 */
	abstract public function getFolderUrl();
	
}