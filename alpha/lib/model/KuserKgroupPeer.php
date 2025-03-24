<?php


/**
 * Skeleton subclass for performing query and update operations on the 'kuser_kgroup' table.
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
class KuserKgroupPeer extends BaseKuserKgroupPeer implements IRelatedObjectPeer
{
	private static $kgroupIdsByKuserId = array();

	/**
	 * Creates default criteria filter
	 */
	public static function setDefaultCriteriaFilter()
	{
		if(self::$s_criteria_filter == null)
			self::$s_criteria_filter = new criteriaFilter();

		$c =  KalturaCriteria::create(KuserKgroupPeer::OM_CLASS);
		$c->addAnd ( KuserKgroupPeer::STATUS, array(KuserKgroupStatus::DELETED), Criteria::NOT_IN);
		$partnerId = kCurrentContext::getCurrentPartnerId();
		if($partnerId)
			$c->addAnd ( KuserKgroupPeer::PARTNER_ID, $partnerId, Criteria::EQUAL );
		self::$s_criteria_filter->setFilter($c);
	}


	/**
	 * @param int $kuserId
	 * @param int $kgroupId
	 */
	static public function retrieveByKuserIdAndKgroupId ($kuserId, $kgroupId){

		$criteria = new Criteria();
		$criteria->add(KuserKgroupPeer::KUSER_ID, $kuserId);
		$criteria->add(KuserKgroupPeer::KGROUP_ID, $kgroupId);
		$criteria->add(KuserKgroupPeer::STATUS, KuserKgroupStatus::ACTIVE);

		return KuserKgroupPeer::doSelectOne($criteria);
	}

	/**
	 * delete all kuserKgroups that belong to kuserId
	 *
	 * @param int $kuserId
	 */
	public static function deleteByKuserId($kuserId){
		$kuserKgroups = self::retrieveByKuserIds(array($kuserId));
		foreach($kuserKgroups as $kuserKgroup) {
			/* @var $kuserKgroup KuserKgroup */
			$kuserKgroup->setStatus(KuserKgroupStatus::DELETED);
			$kuserKgroup->save();
		}
	}

	/**
	 * get kgroups by kusers
	 *
	 * @param array $kuserIds
	 * @return array
	 */
	public static function retrieveByKuserIds($kuserIds){
		$c = new Criteria();
		$c->add(KuserKgroupPeer::KUSER_ID, $kuserIds, Criteria::IN);
		return KuserKgroupPeer::doSelect($c);
	}

	/**
	 * @param array $kuserIds
	 * @return array
	 */
	public static function retrieveKgroupIdsByKuserIds($kuserIds){
		$kuserKgroups = self::retrieveByKuserIds($kuserIds);
		$kgroupIds = array();
		foreach ($kuserKgroups as $kuserKgroup){
			/* @var $kuserKgroup KuserKgroup */
			$kgroupIds[] = $kuserKgroup->getKgroupId();
		}
		return $kgroupIds;
	}

	/**
	 * @param int $kuserId
	 * @return array
	 */
	public static function retrieveKgroupIdsByKuserId($kuserId){
		if (isset(self::$kgroupIdsByKuserId[$kuserId])){
			return self::$kgroupIdsByKuserId[$kuserId];
		}

		self::$kgroupIdsByKuserId[$kuserId] = self::retrieveKgroupIdsByKuserIds(array($kuserId));

		return self::$kgroupIdsByKuserId[$kuserId];
	}

	/**
	 * @param $kuserId
	 * @param $partnerId
	 * @return array|mixed
	 */
	public static function retrieveKgroupByKuserIdAndPartnerId($kuserId, $partnerId)
	{
		//remove default criteria
		self::setUseCriteriaFilter(false);
		$c = new Criteria();
		$c->add(KuserKgroupPeer::KUSER_ID, array($kuserId), Criteria::IN);
		$c->addAnd ( KuserKgroupPeer::PARTNER_ID, $partnerId, Criteria::EQUAL );
		$c->addAnd ( KuserKgroupPeer::STATUS, array(KuserKgroupStatus::DELETED), Criteria::NOT_IN);

		$kuserKgroups = KuserKgroupPeer::doSelect($c);
		self::setUseCriteriaFilter(true);

		return $kuserKgroups;

	}

	/* (non-PHPdoc)
	 * @see IRelatedObjectPeer::getRootObjects()
	 */
	public function getRootObjects(IRelatedObject $object)
	{
		return array(
			kuserPeer::retrieveByPK($object->getKuserId()),
			kuserPeer::retrieveByPK($object->getKgroupId()),
		);
	}

	/* (non-PHPdoc)
	 * @see IRelatedObjectPeer::isReferenced()
	 */
	public function isReferenced(IRelatedObject $object)
	{
		return false;
	}

	public static function getCacheInvalidationKeys()
	{
		return array(array("kuserKgroup:kuserId=%s", self::KUSER_ID), array("kuserKgroup:kgroupId=%s", self::KGROUP_ID));		
	}

	/**
	 * @param int $kgroupId
	 */
	static public function retrieveKuserKgroupByKgroupId ($kgroupId)
	{

		$criteria = new Criteria();
		$criteria->add(KuserKgroupPeer::KGROUP_ID, $kgroupId);
		$criteria->add(KuserKgroupPeer::STATUS, KuserKgroupStatus::ACTIVE);

		return KuserKgroupPeer::doSelect($criteria);
	}
	
	/**
	 * @param array $kgroupIds
	 * @param in $kuserId
	 */
	static public function retrieveByKgroupIdsAndKuserId ($kgroupIds, $kuserId)
	{
		
		$criteria = new Criteria();
		$criteria->add(KuserKgroupPeer::KUSER_ID, $kuserId);
		$criteria->add(KuserKgroupPeer::KGROUP_ID, $kgroupIds, Criteria::IN);
		$criteria->add(KuserKgroupPeer::STATUS, KuserKgroupStatus::ACTIVE);
		
		return KuserKgroupPeer::doSelectOne($criteria);
	}
	
	/**
	 * get  pgroup ids by kusers
	 *
	 * @param array $kuserIds
	 * @return array
	 */
	public static function retrievePgroupIdsByKuserIds($kuserIds)
	{
		$kuserKgroups = self::retrieveByKuserIds($kuserIds);
		$pgroupIds = array();
		foreach ($kuserKgroups as $kuserKgroup)
		{
			/* @var $kuserKgroup KuserKgroup */
			$pgroupIds[] = $kuserKgroup->getPgroupId();
		}
		return $pgroupIds;
	}
} // KuserKgroupPeer
