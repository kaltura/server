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

	const CUSTOM_DATA_LAST_MODIFICATION_TIME = 'last_modification_time';
	
	public function setFileSize($size)
	{
		parent::setFileSize($size);
		if ($size !== $this->getFileSize()) {
		    self::setFileSizeLastSetAt(time());
		}
	}
	
	public function getFileSizeLastSetAt($format = null)
	{
		return parent::getFileSizeLastSetAt($format);
	}
	
	
	public function getLastModificationTime()
	{
	    return $this->getFromCustomData(self::CUSTOM_DATA_LAST_MODIFICATION_TIME);
	}
	
	public function setLastModificationTime($time)
	{
	    $this->putInCustomData(self::CUSTOM_DATA_LAST_MODIFICATION_TIME, $time);
	}
	
	
		
} // DropFolderFile
