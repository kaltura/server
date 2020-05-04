<?php

/**
 * Subclass for performing query and update operations on the 'file_sync' table.
 *
 * 
 *
 * @package Core
 * @subpackage model
 */ 
class FileSyncPeer extends BaseFileSyncPeer
{
	/* (non-PHPdoc)
	 * @see BaseFileSyncPeer::setDefaultCriteriaFilter()
	 */
	public static function setDefaultCriteriaFilter()
	{
		if(self::$s_criteria_filter == null)
		{
			self::$s_criteria_filter = new criteriaFilter();
		}
		
		$c = new Criteria();
		$c->add(self::STATUS, array(FileSync::FILE_SYNC_STATUS_DELETED, FileSync::FILE_SYNC_STATUS_PURGED), Criteria::NOT_IN);
		self::$s_criteria_filter->setFilter($c);
	}
	
	/**
	 * 
	 * @param FileSyncKey $key
	 * @param Criteria $c
	 * @return Criteria
	 */
	public static function getCriteriaForFileSyncKey ( FileSyncKey $key , Criteria $c = null )
	{
		if ( $c == null ) $c = new Criteria();
		$c->addAnd ( self::OBJECT_ID , $key->object_id );
		$c->addAnd ( self::OBJECT_TYPE , $key->object_type );
		$c->addAnd ( self::OBJECT_SUB_TYPE , $key->object_sub_type );
		$c->addAnd ( self::VERSION , $key->version );
		return $c;
	}
	
	
	/**
	 * 
	 * @param FileSyncKey $key
	 * @return FileSync
	 */
	public static function retrieveByFileSyncKey(FileSyncKey $key, $current_dc_only = false)
	{
		$c = self::getCriteriaForFileSyncKey($key);
		if($current_dc_only)
			$c->add(self::DC, kDataCenterMgr::getCurrentDcId());
		return self::doSelectOne($c);
	}
	
	/**
	 * @param FileSyncKey $key
	 * @return array
	 */
	public static function retrieveAllByFileSyncKey(FileSyncKey $key)
	{
		$c = self::getCriteriaForFileSyncKey($key);
		return self::doSelect($c);
	}

	public static function getCacheInvalidationKeys()
	{
		return array(array("fileSync:id=%s", self::ID), array("fileSync:objectId=%s", self::OBJECT_ID));		
	}

	public static function retrieveFileSyncsByFlavorAndDc($dc, $partnerId, $status, $objectIds, $types, $subTypes)
	{
		$c = new Criteria();
		$c->add(self::DC, $dc);
		$c->add(self::PARTNER_ID, $partnerId);
		$c->add(self::STATUS, $status,Criteria::IN);
		$c->add(self::OBJECT_ID, $objectIds,Criteria::IN);
		$c->add(self::OBJECT_TYPE, $types,Criteria::IN);
		$c->add(self::OBJECT_SUB_TYPE, $subTypes,Criteria::IN);
		return self::doSelectOne($c);
	}
}
