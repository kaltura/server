<?php


/**
 * Skeleton subclass for performing query and update operations on the 'entry_distribution' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package plugins.contentDistribution
 * @subpackage model
 */
class EntryDistributionPeer extends BaseEntryDistributionPeer implements IRelatedObjectPeer
{
	/** the search index column name for the NEXT_REPORT field */
	const NEXT_REPORT = 'entry_distribution.NEXT_REPORT';
	const SUN_STATUS = 'entry_distribution.SUN_STATUS';
	
	/**
	 * Creates default criteria filter
	 */
	public static function setDefaultCriteriaFilter()
	{
		if ( self::$s_criteria_filter == null )
			self::$s_criteria_filter = new criteriaFilter ();
		
		$c = new myCriteria(); 
		$c->addAnd ( EntryDistributionPeer::STATUS, EntryDistributionStatus::DELETED, Criteria::NOT_EQUAL);
		self::$s_criteria_filter->setFilter ( $c );
	}
	
	/**
	 * Retrieve objects by entry id.
	 *
	 * @param      string $entryId
	 * @param      PropelPDO $con the connection to use
	 * @return     array<EntryDistribution>
	 */
	public static function retrieveByEntryId($entryId, PropelPDO $con = null)
	{
		$criteria = new Criteria();
		$criteria->add(EntryDistributionPeer::ENTRY_ID, $entryId);

		return EntryDistributionPeer::doSelect($criteria, $con);
	}
	
	/**
	 * Retrieve single EntryDistribution object by entry id and profile id.
	 *
	 * @param      string $entryId
	 * @param      int $distributionProfileId
	 * @param      PropelPDO $con the connection to use
	 * @return     EntryDistribution
	 */
	public static function retrieveByEntryAndProfileId($entryId, $distributionProfileId, PropelPDO $con = null)
	{
		$criteria = new Criteria();
		$criteria->add(EntryDistributionPeer::ENTRY_ID, $entryId);
		$criteria->add(EntryDistributionPeer::DISTRIBUTION_PROFILE_ID, $distributionProfileId);

		return EntryDistributionPeer::doSelectOne($criteria, $con);
	}
	
	/**
	 * Retrieve EntryDistribution objects by entry id according to specified statuses list
	 *
	 * @param      string $entryId
	 * @param      array $statuses
	 * @param      PropelPDO $con the connection to use
	 * @return     EntryDistribution
	 */
	public static function retrieveByEntryAndStatuses($entryId, array $statuses, PropelPDO $con = null)
	{
		$criteria = new Criteria();
		$criteria->add(EntryDistributionPeer::ENTRY_ID, $entryId);
		$criteria->add(EntryDistributionPeer::STATUS, $statuses, Criteria::IN);
	
		return EntryDistributionPeer::doSelect($criteria, $con);
	}
	
	public static function retrieveByEntryIdsAndProfileId(array $entryIds, $distributionProfileId, PropelPDO $con = null)
	{
		$criteria = new Criteria();
		$criteria->add(EntryDistributionPeer::ENTRY_ID, $entryIds, Criteria::IN);
		$criteria->add(EntryDistributionPeer::DISTRIBUTION_PROFILE_ID, $distributionProfileId);

		return EntryDistributionPeer::doSelect($criteria, $con);
	}
	
	/**
	 * @param Criteria $criteria
	 * @param PropelPDO $con
	 */
	public static function doSelect(Criteria $criteria, PropelPDO $con = null)
	{
		$c = clone $criteria;
		
		if($c instanceof KalturaCriteria)
		{
			$c->applyFilters();
			$criteria->setRecordsCount($c->getRecordsCount());
		}
			
		return parent::doSelect($c, $con);
	}

	
	/* (non-PHPdoc)
	 * @see BaseEntryDistributionPeer::getAtomicColumns()
	 */
	public static function getAtomicColumns()
	{
		return array(BaseEntryDistributionPeer::STATUS);
	}
	
	public static function getCacheInvalidationKeys()
	{
		return array(array("entryDistribution:entryId=%s", self::ENTRY_ID));		
	}
	
	/* (non-PHPdoc)
	 * @see IRelatedObjectPeer::getRootObjects()
	 */
	public function getRootObjects(IRelatedObject $object)
	{
		/* @var $object EntryDistribution */
		$roots = array();
		
		$distributionProfile = DistributionProfilePeer::retrieveByPK($object->getDistributionProfileId());
		if($distributionProfile)
			$roots[] = $distributionProfile;
		
		$entry = entryPeer::retrieveByPK($object->getEntryId());
		if($entry)
			$roots[] = $entry;
			
		return $roots;
	}

	/* (non-PHPdoc)
	 * @see IRelatedObjectPeer::isReferenced()
	 */
	public function isReferenced(IRelatedObject $object)
	{
		return false;
	}
} // EntryDistributionPeer
