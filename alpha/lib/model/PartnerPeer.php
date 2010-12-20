<?php

/**
 * Subclass for performing query and update operations on the 'partner' table.
 * Because Partner is very often used in the SDK and the object itself hardley changes,
 * we'll use the obejct cache mechanism to hit the DB as little as possible
 *
 *
 * @package lib.model
 */
class PartnerPeer extends BasePartnerPeer
{
	const NULL_PARTNER = "_NULL_" ;
	const CLZZ = "Partner" ;

	const EXPIRY_FOR_NULL = 120;
	const EXPIRY_FOR_NON_NULL = 300;
	
	const GLOBAL_PARTNER = 0;
	/*
		Will retrieve the partner object in one of 2 ways:
		1. if pk in a number - will use the original  retrieveByPK
		2. if pk is not the umber - will try to retrieve
	*/
	/**
	 * Enter description here...
	 *
	 * @param unknown_type $pk
	 * @param unknown_type $con
	 * @return partner
	 */
	public static function retrieveByPK($pk, PropelPDO $con = null)
	{
//		echo __METHOD__ . " {$pk}\n";

		if ( is_numeric( $pk ))
		{
			$partner = self::getPartnerFromCache( $pk );
			if ( $partner == self::NULL_PARTNER ) return null;
			if ( $partner ) return $partner;

			$partner = parent::retrieveByPK( $pk, $con );
			self::putPartnerInCache ( $partner , true , $pk );
			return $partner;
		}
		else
		{
			if ( ! $pk ) return null; 
			$partner = self::getPartnerFromCache( $pk , false );
			if ( $partner ==  self::NULL_PARTNER ) return null;
			if ( $partner ) return $partner;

			// try fetching by alias
			$c = new Criteria();
			$c->add ( self::PARTNER_ALIAS , $pk );
			$partner = self::doSelectOne( $c );
			self::putPartnerInCache ( $partner , false , $pk);
			return $partner;

		}
	}


	public static function resetPartnerInCache ( $key , $by_id = true )
	{
		$cache = new myObjectCache ( self::EXPIRY_FOR_NULL );
		if ( $by_id )
		{
			$cache->remove ( self::CLZZ , $key );
		}
		else
		{
			$cache->remove ( self::CLZZ , $key , "partnerAlias" );
		}
	}

	private static function getPartnerFromCache ( $key , $by_id = true )
	{
//		echo __METHOD__ . " [{$key}] [{$by_id}] \n";
		$cache = new myObjectCache ( self::EXPIRY_FOR_NON_NULL );
		if ( $by_id )
		{
			$partner = $cache->get ( self::CLZZ , $key );
		}
		else
		{
			$partner = $cache->get ( self::CLZZ , $key , "partnerAlias");
		}

		return $partner;
	}

	private static function putPartnerInCache ( $obj , $by_id = true , $pk = null)
	{

		// if there is no partner - cache for a short while assuming we are in the middle of creating one
		// if there is a partner - cache for a little longer assuming it doesn't change very much once it's created
		//$expiry = ( $obj != null ? 600 : 60 );
		if ( $obj == null )
		{
			$cache = new myObjectCache ( self::EXPIRY_FOR_NULL );
			if ( $by_id )
			{
				$cache->putValue ( self::CLZZ , $pk , null , self::NULL_PARTNER );
			}
			else
			{
				$cache->putValue ( self::CLZZ , $pk , "partnerAlias" , self::NULL_PARTNER );
			}
		}
		else
		{
			$cache = new myObjectCache ( self::EXPIRY_FOR_NON_NULL  );
			if ( $by_id )
			{
				$cache->put ( $obj );
			}
			else
			{
				$cache->put ( $obj , "partnerAlias");
			}
		}
	}

	public static function removePartnerFromCache ( $id )
	{
		$cache = new myObjectCache ( );
		$cache->remove ( self::CLZZ , $id );
	}

}
