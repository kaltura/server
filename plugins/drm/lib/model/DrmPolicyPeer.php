<?php


/**
 * Skeleton subclass for performing query and update operations on the 'drm_policy' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package plugins.drm
 * @subpackage model
 */
class DrmPolicyPeer extends BaseDrmPolicyPeer 
{
	public static function setDefaultCriteriaFilter ()
	{
		parent::setDefaultCriteriaFilter();
		if ( self::$s_criteria_filter == null )
		{
			self::$s_criteria_filter = new criteriaFilter ();
		}
		
		$c = new myCriteria(); 
		$c->addAnd ( self::STATUS, DrmPolicyStatus::DELETED, Criteria::NOT_EQUAL);
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
			$typeField = self::translateFieldName(DrmPolicyPeer::PROVIDER, BasePeer::TYPE_COLNAME, BasePeer::TYPE_NUM);
			$drmPolicyType = $row[$typeField];				
			$extendedCls = KalturaPluginManager::getObjectClass(parent::OM_CLASS, $drmPolicyType);
			if($extendedCls)
			{
				return $extendedCls;
			}
		}
			
		return parent::OM_CLASS;
	}
	
	/**
	 * Retrieve drm policy according to systemName
	 * @param string $systemName
	 * @param int $excludeId
	 * @param PropelPDO $con
	 * @return DrmPolicy
	 */
	public static function retrieveBySystemName ($systemName, $excludeId = null, PropelPDO $con = null)
	{
		$c = new Criteria();
		$c->add ( DrmPolicyPeer::SYSTEM_NAME, $systemName );		
		return DrmPolicyPeer::doSelectOne($c);
	}
	
} // DrmPolicyPeer
