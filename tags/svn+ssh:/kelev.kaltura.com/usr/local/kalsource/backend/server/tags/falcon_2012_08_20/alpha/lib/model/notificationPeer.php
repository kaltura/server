<?php

/**
 * Subclass for performing query and update operations on the 'notification' table.
 *
 * 
 *
 * @package Core
 * @subpackage model
 */ 
class notificationPeer extends BasenotificationPeer
{
	function getPendingStatus()
	{
		return notification::NOTIFICATION_STATUS_PENDING;
	}
	
	function getInProcStatus()
	{
		return notification::NOTIFICATION_STATUS_QUEUED;
	}
	
	function getInProcStatusList()
	{
		return notification::NOTIFICATION_STATUS_QUEUED;
	}
	
	public static function retrieveByEntryId ( $obj_id )
	{
		$c = new Criteria();
		$c->add ( self::OBJECT_ID , $obj_id );
		$c->add ( self::OBJECT_TYPE , notification::NOTIFICATION_OBJECT_TYPE_ENTRY );
		return self::doSelect( $c );
	}
	
	public static function retrieveByEntryIdAndType($obj_id, $type)
	{
	    $c = new Criteria();
		$c->add ( self::OBJECT_ID , $obj_id );
		$c->add ( self::OBJECT_TYPE , notification::NOTIFICATION_OBJECT_TYPE_ENTRY );
		$c->add ( self::TYPE, $type);
		return self::doSelect( $c );
	}

	public static function retrieveByKshowId ( $obj_id )
	{
		$c = new Criteria();
		$c->add ( self::OBJECT_ID , $obj_id );
		$c->add ( self::OBJECT_TYPE , notification::NOTIFICATION_OBJECT_TYPE_KSHOW );
		return self::doSelect( $c );
	}
	
}
