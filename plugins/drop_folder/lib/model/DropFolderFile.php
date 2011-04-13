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

	public function setFileSize($size)
	{
		parent::setFileSize($size);
		self::setFileSizeLastSetAt(time());	
	}
	
} // DropFolderFile
