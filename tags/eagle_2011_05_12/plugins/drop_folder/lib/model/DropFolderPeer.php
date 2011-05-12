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
		self::addDefaultCriteria($c);
		self::$s_criteria_filter->setFilter ( $c );
	}
	
	private static function addDefaultCriteria(Criteria &$c)
	{
		$c->addAnd( self::STATUS, DropFolderStatus::DELETED, Criteria::NOT_EQUAL);
	}
	
		
	public static function retrieveByPathDefaultFilter($path)
	{
		DropFolderPeer::setUseCriteriaFilter(false);
		$c = new Criteria();
		DropFolderPeer::addDefaultCriteria($c);			
		$c->addAnd(DropFolderPeer::PATH, $path, Criteria::EQUAL);
		$dropFolder = DropFolderPeer::doSelectOne($c);		
		DropFolderPeer::setUseCriteriaFilter(true);
		return $dropFolder;
	}
	

} // DropFolderPeer
