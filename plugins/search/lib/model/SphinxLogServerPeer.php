<?php


/**
 * Skeleton subclass for performing query and update operations on the 'sphinx_log_server' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package plugins.sphinxSearch
 * @subpackage model
 */
class SphinxLogServerPeer extends BaseSphinxLogServerPeer {

	public static function alternativeCon($con, $queryDB = kQueryCache::QUERY_DB_UNDEFINED)
	{
		return myDbHelper::getConnection(myDbHelper::DB_HELPER_CONN_SPHINX_LOG);
	}


	/**
	 * Retrieve all records of the server
	 *
	 * @param      string $server server name
	 * @param      PropelPDO $con the connection to use
	 * @return     array<SphinxLogServer>
	 */
	public static function retrieveByServer($server, PropelPDO $con = null)
	{
		$criteria = new Criteria();
		$criteria->add(SphinxLogServerPeer::SERVER, $server);

		return SphinxLogServerPeer::doSelect($criteria, $con);
	}

	/**
	 * Retrieve the server object of the current data center
	 *
	 * @param      string $server server name
	 * @param      PropelPDO $con the connection to use
	 * @return     SphinxLogServer
	 */
	public static function retrieveByLocalServer($server, PropelPDO $con = null)
	{
		$criteria = new Criteria();
		$criteria->add(SphinxLogServerPeer::SERVER, $server);
		$criteria->add(SphinxLogServerPeer::DC, kDataCenterMgr::getCurrentDcId());

		return SphinxLogServerPeer::doSelectOne($criteria, $con);
	}
	
} // SphinxLogServerPeer
