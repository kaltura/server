<?php

/**
 * Subclass for performing query and update operations on the 'moderation' table.
 *
 * 
 *
 * @package lib.model
 */ 
class moderationPeer extends BasemoderationPeer
{
	private static $s_default_count_limit = 301;
	
	
	public static function getByStatusAndObject ( $status , $object_id , $object_type )
	{
		$c = new Criteria();
		$c->add ( self::STATUS , $status );
		$c->add ( self::OBJECT_ID , $object_id );
		$c->add ( self::OBJECT_TYPE , $object_type ) ;
		return self::doSelectOne( $c );
	}
	
	
	public static function doUpdateAllModerations($selectCriteria , $values, $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection(self::DATABASE_NAME);
		}
		return BasePeer::doUpdate($selectCriteria, $values, $con);		
	}
}
