<?php


/**
 * Skeleton subclass for performing query and update operations on the 'api_server' table.
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
class ApiServerPeer extends BaseApiServerPeer {

	/**
	 * @param string $hostname
	 * @param PropelPDO $con
	 * @return ApiServer
	 */
	public static function retrieveByHostname($hostname, PropelPDO $con = null)
	{
		$criteria = new Criteria(ApiServerPeer::DATABASE_NAME);
		$criteria->add(ApiServerPeer::HOSTNAME, $hostname);

		return ApiServerPeer::doSelectOne($criteria, $con);
	}
	
} // ApiServerPeer
