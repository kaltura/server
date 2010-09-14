<?php

/**
 * Subclass for performing query and update operations on the 'file_sync' table.
 *
 * 
 *
 * @package lib.model
 */ 
class FileSyncPeer extends BaseFileSyncPeer
{
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
	public static function retreiveByFileSyncKey(FileSyncKey $key)
	{
		$c = self::getCriteriaForFileSyncKey($key);
		return self::doSelectOne($c);
	}
	
	/**
	 * @param FileSyncKey $key
	 * @return array
	 */
	public static function retreiveAllByFileSyncKey(FileSyncKey $key)
	{
		$c = self::getCriteriaForFileSyncKey($key);
		return self::doSelect($c);
	}
}
