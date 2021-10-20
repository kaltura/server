<?php


/**
 * Skeleton subclass for performing query and update operations on the 'virtual_event' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package plugins.virtualEvent
 * @subpackage model
 */
class VirtualEventPeer extends BaseVirtualEventPeer {
	
	/*
	 * (non-PHPdoc)
	 * @see BaseVirtualEventPeer::setDefaultCriteriaFilter()
	 */
	public static function setDefaultCriteriaFilter()
	{
		if(self::$s_criteria_filter == null )
			self::$s_criteria_filter = new criteriaFilter();
		
		$c = new Criteria();
		$c->addAnd(VirtualEventPeer::STATUS, VirtualEventStatus::DELETED, Criteria::NOT_EQUAL);
		self::$s_criteria_filter->setFilter($c);
	}
	
	/* (non-PHPdoc)
	 * @see BaseVirtualEventPeer::doSelect()
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
	
	
	/**
	 * @param int $pk
	 * @return VirtualEvent
	 */
	public static function retrieveByPKNoFilter($pk)
	{
		self::setUseCriteriaFilter(false);
		$virtualEvent = self::retrieveByPK($pk);
		self::setUseCriteriaFilter(true);
		
		return $virtualEvent;
	}
	
	/**
	 * Retrieve a single object by pkey.
	 *
	 * @param      int $pk the primary key.
	 * @param      PropelPDO $con the connection to use
	 * @return     category
	 */
	public static function retrieveByPK($pk, PropelPDO $con = null)
	{
		if (!strlen(trim($pk))) {
			return null;
		}
		
		if (null !== ($obj = virtualEventPeer::getInstanceFromPool((string) $pk))) {
			return $obj;
		}
		
		$criteria = KalturaCriteria::create(virtualEventPeer::OM_CLASS);
		$criteria->add(virtualEventPeer::ID, $pk);
		
		$v = virtualEventPeer::doSelect($criteria, $con);
		
		return !empty($v) > 0 ? $v[0] : null;
	}

	
} // VirtualEventPeer
