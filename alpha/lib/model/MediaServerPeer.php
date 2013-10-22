<?php


/**
 * Skeleton subclass for performing query and update operations on the 'media_server' table.
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
class MediaServerPeer extends BaseMediaServerPeer {

	/**
	 * Retrieve a single server by its hostname
	 *
	 * @param      string $hostname
	 * @param      PropelPDO $con the connection to use
	 * @return     MediaServer
	 */
	public static function retrieveByHostname($hostname, PropelPDO $con = null)
	{
		$criteria = new Criteria();
		$criteria->add(MediaServerPeer::HOSTNAME, $hostname);

		return MediaServerPeer::doSelectOne($criteria, $con);
	}
	
} // MediaServerPeer
