<?php

/**
 * Subclass for performing query and update operations on the 'upload_token' table.
 *
 * 
 *
 * @package Core
 * @subpackage model
 */ 
class UploadTokenPeer extends BaseUploadTokenPeer
{
	public static function setDefaultCriteriaFilter ()
	{
		if ( self::$s_criteria_filter == null )
		{
			self::$s_criteria_filter = new criteriaFilter ();
		}

		$c = new Criteria();
		$c->addAnd(self::STATUS, UploadToken::UPLOAD_TOKEN_DELETED, Criteria::NOT_EQUAL);
		self::$s_criteria_filter->setFilter ( $c );
	}
	public static function getCacheInvalidationKeys()
	{
		return array(array("uploadToken:id=%s", self::ID));		
	}
	
	
	/**
	 * Retrieve a single object by pkey.
	 *
	 * @param      string $pk the primary key.
	 * @param      PropelPDO $con the connection to use
	 * @return     UploadToken
	 */
	public static function retrieveByPKNoFilter($pk, PropelPDO $con = null)
	{
		self::setUseCriteriaFilter ( false );
		$res = self::retrieveByPK($pk, $con);
		self::setUseCriteriaFilter ( true );
		return $res;
	}
}
