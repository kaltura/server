<?php

/**
* @package plugins.dropFolder
* @subpackage model
*/
abstract class RemoteDropFolder extends DropFolder
{
	
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