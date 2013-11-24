<?php


/**
 * Skeleton subclass for performing query and update operations on the 'drm_profile' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package plugins.drmBase
 * @subpackage model
 */
class DrmProfilePeer extends BaseDrmProfilePeer 
{
	public static function setDefaultCriteriaFilter ()
	{
		parent::setDefaultCriteriaFilter();
		if ( self::$s_criteria_filter == null )
		{
			self::$s_criteria_filter = new criteriaFilter ();
		}
		
		$c = new myCriteria(); 
		$c->addAnd ( self::STATUS, DrmProfileStatus::DELETED, Criteria::NOT_EQUAL);
		self::$s_criteria_filter->setFilter ( $c );
	}
	
	/**
	 * The returned Class will contain objects of the default type or
	 * objects that inherit from the default.
	 *
	 * @param      array $row PropelPDO result row.
	 * @param      int $colnum Column to examine for OM class information (first is 0).
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function getOMClass($row, $colnum)
	{
		if($row)
		{
			$typeField = self::translateFieldName(DrmProfilePeer::PROVIDER, BasePeer::TYPE_COLNAME, BasePeer::TYPE_NUM);
			$drmProfileType = $row[$typeField];				
			$extendedCls = KalturaPluginManager::getObjectClass(parent::OM_CLASS, $drmProfileType);
			if($extendedCls)
			{
				return $extendedCls;
			}
		}
			
		return parent::OM_CLASS;
	}
	
	public static function retrieveByProvider($provider)
	{
		$c = new Criteria();
		$c->addAnd(DrmProfilePeer::PROVIDER, $provider, Criteria::EQUAL);
		return DrmProfilePeer::doSelectOne($c);		
	}
} // DrmProfilePeer
