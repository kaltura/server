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
	
	public static function setDefaultCriteriaFilter ()
	{
		parent::setDefaultCriteriaFilter();
		if ( self::$s_criteria_filter == null )
		{
			self::$s_criteria_filter = new criteriaFilter ();
		}
		
		$c = new myCriteria(); 
		$c->addAnd ( self::STATUS, DropFolderStatus::DELETED, Criteria::NOT_EQUAL);
		self::$s_criteria_filter->setFilter ( $c );
	}
	
	
	public static function retrieveByPath($path)
	{
		$c = new Criteria();
		$c->addAnd(DropFolderPeer::PATH, $path, Criteria::EQUAL);
		return DropFolderPeer::doSelectOne($c);
	}
	
	public static function retrieveByPathNoFilters($path)
	{
		DropFolderPeer::setUseCriteriaFilter(false);
		$dropFolder = self::retrieveByPath($path);
		DropFolderPeer::setUseCriteriaFilter(true);
		return $dropFolder;
	}
	

} // DropFolderPeer
