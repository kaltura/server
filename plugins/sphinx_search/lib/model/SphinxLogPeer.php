<?php


/**
 * Skeleton subclass for performing query and update operations on the 'sphinx_log' table.
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
class SphinxLogPeer extends BaseSphinxLogPeer {

	public static function alternativeCon($con)
	{
		return myDbHelper::getConnection(myDbHelper::DB_HELPER_CONN_SPHINX_LOG);
	}


	/**
	 * Retrieve all records larger than the id
	 *
	 * @param      array $servers<SphinxLogServer>
	 * @param      int $limit
	 * @param      PropelPDO $con the connection to use
	 * @return     SphinxLog
	 */
	public static function retrieveByLastId(array $servers, $limit = 1000, PropelPDO $con = null)
	{
		$criteria = new Criteria();
		$criterions = null;
		if(count($servers))
			$criterions = $criteria->getNewCriterion(SphinxLogPeer::ID, null, Criteria::ISNULL);
		
		foreach($servers as $server)
		{
			$crit = $criteria->getNewCriterion(SphinxLogPeer::ID, $server->getLastLogId(), Criteria::GREATER_THAN);
			$crit->addAnd($criteria->getNewCriterion(SphinxLogPeer::DC, $server->getDc()));
			$criterions->addOr($crit);
		}
		
		if($criterions)
			$criteria->addAnd($criterions);
			
		$criteria->addAscendingOrderByColumn(SphinxLogPeer::ID);
		$criteria->setLimit($limit);

		return SphinxLogPeer::doSelect($criteria, $con);
	}
	
} // SphinxLogPeer
