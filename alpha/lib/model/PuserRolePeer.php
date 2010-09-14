<?php

/**
 * Subclass for performing query and update operations on the 'puser_role' table.
 *
 * 
 *
 * @package lib.model
 */ 
class PuserRolePeer extends BasePuserRolePeer
{
	const LIST_SEPARATOR = ";";
	 
	public static function retrieveByKshowPartnerAndUid ( $kshow_id , $partner_id , $subp_id, $puser_id, $role = null )
	{
		$c = new Criteria();
		$c->add ( self::KSHOW_ID , $kshow_id );
		$c->add ( self::PARTNER_ID , $partner_id );
		$c->add ( self::SUBP_ID , $subp_id );
		$c->add ( self::PUSER_ID , $puser_id );
		
		if ($role)
			$c->add ( self::ROLE , $role );
			
		$puser_role = self::doSelectOne (  $c );
		return $puser_role;
	}
	
	public static function addPuserRole ( $kshow_id , $partner_id , $subp_id, $puser_id , $role )
	{
		$puser_id = trim ( $puser_id );
		if ( empty ( $puser_id ) )			return null;
			
		// TODO - first check if already exist $kshow_id , $partner_id , $puser_id
		$puser_role = new  PuserRole();
		$puser_role->setKshowId( $kshow_id );
		$puser_role->setPartnerId( $partner_id );
		$puser_role->setSubpId( $subp_id );
		$puser_role->setPuserId( $puser_id );
		$puser_role->setRole( $role );
		$puser_role->save();
		
		return $puser_role->getId();
	}
	
	public static function addPusersRole ( $kshow_id , $partner_id , $subp_id, $puser_id_list , $role )
	{
		$id_list = array();
		
		foreach ( $puser_id_list as $puser_id => $puser)
		{
			$id_list[] = self::addPuserRole ( $kshow_id , $partner_id , $subp_id, $puser_id , $role );
		}
		
		return $id_list;
	}
}
