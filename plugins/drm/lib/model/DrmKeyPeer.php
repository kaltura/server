<?php


/**
 * Skeleton subclass for performing query and update operations on the 'drm_key' table.
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
class DrmKeyPeer extends BaseDrmKeyPeer 
{
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
			$typeField = self::translateFieldName(DrmKeyPeer::PROVIDER, BasePeer::TYPE_COLNAME, BasePeer::TYPE_NUM);
			$drmKeyType = $row[$typeField];				
			$extendedCls = KalturaPluginManager::getObjectClass(parent::OM_CLASS, $drmKeyType);
			if($extendedCls)
			{
				return $extendedCls;
			}
		}
			
		return parent::OM_CLASS;
	}
	
	/**
	 * Retrieve drm keys according to uique key
	 * @param string $systemName
	 * @param int $excludeId
	 * @param PropelPDO $con
	 * @return DrmPolicy
	 */
	public static function retrieveByUniqueKey ($objectId, $objectType, $provider)
	{
		$c = new Criteria();
		$c->add ( DrmKeyPeer::OBJECT_ID, $objectId );	
		$c->add ( DrmKeyPeer::OBJECT_TYPE, $objectType );	
		$c->add ( DrmKeyPeer::PROVIDER, $provider );		
		return DrmKeyPeer::doSelectOne($c);
	}
} // DrmKeyPeer
