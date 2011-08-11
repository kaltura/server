<?php

/**
 * Subclass for performing query and update operations on the 'widget' table.
 *
 * 
 *
 * @package Core
 * @subpackage model
 */ 
class widgetPeer extends BasewidgetPeer
{
	const WIDGET_PEER_JOIN_KSHOW = 1;
	const WIDGET_PEER_JOIN_ENTRY = 2;
	const WIDGET_PEER_JOIN_UI_CONF = 4;
	const WIDGET_PEER_JOIN_ALL = 8;
/*	
	public static function retrieveByHashedId($pk, $con = null)
	{
		// try fetching by alias
		$c = new Criteria();
		$c->add ( self:: , $pk );
		$partner = self::doSelectOne( $c );
		return $partner;
	}
*/

	public static function retrieveByPK($pk, PropelPDO $con = null, $join = null)
	{
		if ( $join == null )
			return parent::retrieveByPK( $pk , $con );
		$c = new Criteria();
		$c->add ( self::ID , $pk );
		$c->setLimit( 1 );
		// TODO - support all joins -
		// for now supporting only kshow,entry,kshow+entry and all 
		if ( $join == self::WIDGET_PEER_JOIN_KSHOW )
			$res = self::doSelectJoinkshow( $c , $con );
		elseif ( $join == self::WIDGET_PEER_JOIN_ENTRY )
			$res = self::doSelectJoinentry( $c , $con );
		elseif ( $join == self::WIDGET_PEER_JOIN_ENTRY + self::WIDGET_PEER_JOIN_KSHOW )
			$res = self::doSelectJoinAllExceptuiConf( $c , $con );
		elseif ( $join == self::WIDGET_PEER_JOIN_ALL )		
			$res = self::doSelectJoinAllExceptuiConf( $c , $con );
		else
			throw new Exception ( "still NEED to implement join type [$join]") ; 	
					
		return $res;
	}

	
}
