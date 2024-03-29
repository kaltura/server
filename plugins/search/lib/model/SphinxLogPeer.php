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
		if($con && in_array($con->getConnectionName(), array(myDbHelper::DB_HELPER_CONN_SPHINX_LOG_READ, myDbHelper::DB_HELPER_CONN_SPHINX_LOG)))
		{
			return $con;
		}
		
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
	public static function retrieveByLastId(array $servers, $gap = 0, $limit = 1000, array $handledEntries = null, PropelPDO $con = null, $type = SphinxLogType::SPHINX)
	{
		$baseCriteria = new Criteria();

		$disabledPartnerIds = array();
		if ($type == SphinxLogType::SPHINX)
		{
			$disabledPartnerIds = kConf::get('disable_sphinx_indexing_partners', 'local', array());
		}
		else if ($type == SphinxLogType::ELASTIC)
		{
			$disabledPartnerIds = kConf::get('disable_elastic_populate_indexing_partners', 'local', array());
		}

		if ($disabledPartnerIds)
		{
			$baseCriteria->add(SphinxLogPeer::PARTNER_ID, $disabledPartnerIds, Criteria::NOT_IN);
		}

		$types = array($type);
		$baseCriteria->add(SphinxLogPeer::TYPE, $types, Criteria::IN);
		$baseCriteria->addAscendingOrderByColumn(SphinxLogPeer::ID);
		$baseCriteria->setLimit($limit);
		$baseCriteria->setForceIndex('dc_id');

		$result = array();

		foreach($servers as $server)
		{
			$criteria = clone $baseCriteria;

			$dc = $server->getDc();
			$criteria->add(SphinxLogPeer::DC, $dc);

			$crit = $criteria->getNewCriterion(SphinxLogPeer::ID, $server->getLastLogId() - $gap, Criteria::GREATER_THAN);
			if(!is_null($handledEntries)) {
				$crit->addAnd($criteria->getNewCriterion(SphinxLogPeer::ID, $handledEntries[$dc], Criteria::NOT_IN));
			}

			$criteria->add($crit);

			$rows = SphinxLogPeer::doSelect($criteria, $con);
			foreach ($rows as $row)
			{
				$result[$row->getId()] = $row;
			}
		}

		ksort($result, SORT_NUMERIC);

		return array_values($result);
	}
	
} // SphinxLogPeer
