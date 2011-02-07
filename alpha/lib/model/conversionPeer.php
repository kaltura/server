<?php

/**
 * Subclass for performing query and update operations on the 'conversion' table.
 *
 * 
 *
 * @package Core
 * @subpackage model
 */ 
class conversionPeer extends BaseconversionPeer
{
	public static function retrieveByEntryId ( $obj_id )
	{
		$c = new Criteria();
		$c->add ( self::ENTRY_ID , $obj_id );
		return self::doSelect( $c );
	}
	
}
