<?php


/**
 * Skeleton subclass for performing query and update operations on the 'drop_folder' table.
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
class DropFolderPeer extends BaseDropFolderPeer
{
	
	public static function getByDcAndPath($dcId, $path)
	{
		$c = new Criteria();
		$c->addAnd(DropFolderPeer::DC, $dcId, Criteria::EQUAL);
		$c->addAnd(DropFolderPeer::PATH, $path, Criteria::EQUAL);
		return DropFolderPeer::doSelectOne($c);
	}	

} // DropFolderPeer
