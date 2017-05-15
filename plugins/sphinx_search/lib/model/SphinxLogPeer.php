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

	public static function alternativeCon($con, $queryDB = kQueryCache::QUERY_DB_UNDEFINED)
	{
		return myDbHelper::getConnection(myDbHelper::DB_HELPER_CONN_SPHINX_LOG);
	}


	/**
	 * Retrieve all records larger than the id
	 *
	 * @param      array $servers<SphinxLogServer>
	 * @param	   int $type
	 * @param	   int $gap
	 * @param      int $limit
	 * @param	   array $handledEntries	
	 * @param      PropelPDO $con the connection to use
	 * @return     SphinxLog
	 */
	public static function retrieveByLastId(array $servers, $type, $gap = 0, $limit = 1000, array $handledEntries = null, PropelPDO $con = null)
	{
		$criteria = new Criteria();
		$criterions = null;
		if(count($servers))
			$criterions = $criteria->getNewCriterion(SphinxLogPeer::ID, null, Criteria::ISNULL);
		
		foreach($servers as $server)
		{
			$dc = $server->getDc();
			$crit = $criteria->getNewCriterion(SphinxLogPeer::ID, $server->getLastLogId() - $gap, Criteria::GREATER_THAN);
			$crit->addAnd($criteria->getNewCriterion(SphinxLogPeer::DC, $dc));
			if(!is_null($handledEntries)) {
				$crit->addAnd($criteria->getNewCriterion(SphinxLogPeer::ID, $handledEntries[$dc], Criteria::NOT_IN));
			}
			$criterions->addOr($crit);
		}
		
		if($criterions)
			$criteria->addAnd($criterions);
			
		$disabledPartnerIds = kConf::get('disable_sphinx_indexing_partners', 'local', array());
		if ($disabledPartnerIds)
		{
			$criteria->add(SphinxLogPeer::PARTNER_ID, $disabledPartnerIds, Criteria::NOT_IN);
		}

		$criteria->add(SphinxLogPeer::TYPE, $type, Criteria::IN);
		
		$criteria->addAscendingOrderByColumn(SphinxLogPeer::ID);
		$criteria->setLimit($limit);

		return SphinxLogPeer::doSelect($criteria, $con);
	}
	
} // SphinxLogPeer
