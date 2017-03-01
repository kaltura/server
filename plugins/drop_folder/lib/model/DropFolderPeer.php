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
    
    // cache classes by their type
	protected static $class_types_cache = array(
	    DropFolderType::LOCAL => parent::OM_CLASS,
	);
	
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

	public static function retrieveByTag($tag, $currentDC = false)
	{
		$c = new Criteria();
		$c->addAnd( self::STATUS, array(DropFolderStatus::ENABLED, DropFolderStatus::ERROR), Criteria::IN);
		if ($tag != '*')
			$c->addAnd(DropFolderPeer::TAGS, $tag, Criteria::EQUAL);
		if ($currentDC)
			$c->addAnd(DropFolderPeer::DC, kDataCenterMgr::getCurrentDcId(), Criteria::EQUAL);
		$dropFolders = DropFolderPeer::doSelect($c);
		return $dropFolders;
	}
	
	/* (non-PHPdoc)
	 * @see BaseCuePointPeer::getOMClass()
	 */
	public static function getOMClass($row, $colnum)
	{
		if($row)
		{
			$colnum += self::translateFieldName(self::TYPE, BasePeer::TYPE_COLNAME, BasePeer::TYPE_NUM);
			$assetType = $row[$colnum];
			if(isset(self::$class_types_cache[$assetType]))
				return self::$class_types_cache[$assetType];
				
			$extendedCls = KalturaPluginManager::getObjectClass(self::OM_CLASS, $assetType);
			if($extendedCls)
			{
				self::$class_types_cache[$assetType] = $extendedCls;
				return $extendedCls;
			}
			self::$class_types_cache[$assetType] = self::OM_CLASS;
		}
			
		return self::OM_CLASS;
	}
	

	public static function getCacheInvalidationKeys()
	{
		return array(array("dropFolder:id=%s", self::ID), array("dropFolder:dc=%s", self::DC));		
	}
} // DropFolderPeer
