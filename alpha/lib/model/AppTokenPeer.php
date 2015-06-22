<?php


/**
 * Skeleton subclass for performing query and update operations on the 'app_token' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package Core
 * @subpackage model
 */
class AppTokenPeer extends BaseAppTokenPeer {

	
	/**
	 * Retrieve a single object by pkey with no filter
	 * @param string $id
	 * @param $con
	 * @return AppToken
	 */
	public static function retrieveByPkNoFilter($id, $con = null)
	{
		self::setUseCriteriaFilter(false);
		$appToken = self::retrieveByPK($id, $con);
		self::setUseCriteriaFilter(true);
		
		return $appToken;
	}
	
} // AppTokenPeer
