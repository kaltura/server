<?php


/**
 * Skeleton subclass for performing query and update operations on the 'drop_folder_file' table.
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
class DropFolderFilePeer extends BaseDropFolderFilePeer
{

	
	public static function getByDropFolderIdAndFileName($dropFolderId, $fileName)
	{
		$c = new Criteria();
		$c->addAnd(DropFolderFilePeer::DROP_FOLDER_ID, $dropFolderId, Criteria::EQUAL);
		$c->addAnd(DropFolderFilePeer::FILE_NAME, $fileName, Criteria::EQUAL);
		$dropFolderFile = DropFolderFilePeer::doSelectOne($c);
		return $dropFolderFile;		
	}
	
	
} // DropFolderFilePeer
