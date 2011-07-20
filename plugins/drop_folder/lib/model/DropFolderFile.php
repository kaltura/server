<?php


/**
 * Skeleton subclass for representing a row from the 'drop_folder_file' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package plugins.dropFolder
 * @subpackage model
 */
class DropFolderFile extends BaseDropFolderFile
{

	const CUSTOM_DATA_HANDLE_BATCH_JOB_ID  = 'handle_batch_job_id';
	
	public function setFileSize($size)
	{
		parent::setFileSize($size);
		self::setFileSizeLastSetAt(time());	
	}
	
	public function getFileSizeLastSetAt($format = null)
	{
		return parent::getFileSizeLastSetAt($format);
	}
	
	/**
	 * @return string full local path to file (drop folder path + file name)
	 */
	public function getLocalFullPath()
	{
		$dropFolder = DropFolderPeer::retrieveByPK($this->getDropFolderId());
		
		if ($dropFolder && strlen($dropFolder->getLocalPath()) > 0)
		{
			$fullLocalPath = $dropFolder->getLocalPath().'/'.$this->getFileName();
			return $fullLocalPath;
		}
		return null;
	}
	
		
} // DropFolderFile
