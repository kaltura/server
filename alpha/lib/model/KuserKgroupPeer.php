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
class KuserKgroupPeer extends BaseKuserKgroupPeer {

	/**
	 * Creates default criteria filter
	 */
	public static function setDefaultCriteriaFilter()
	{
		if(self::$s_criteria_filter == null)
			self::$s_criteria_filter = new criteriaFilter();

		$c =  KalturaCriteria::create(KuserKgroupPeer::OM_CLASS);
		$c->addAnd ( KuserKgroupPeer::STATUS, array(KuserKgroupStatus::DELETED), Criteria::NOT_IN);
		$c->addAnd ( KuserKgroupPeer::PARTNER_ID, kCurrentContext::getCurrentPartnerId(), Criteria::EQUAL );
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
		$kuserKgroups = self::retrieveByKuserId($kuserId);
		foreach($kuserKgroups as $kuserKgroup) {
			/* @var $kuserKgroup KuserKgroup */
			$kuserKgroup->setStatus(KuserKgroupStatus::DELETED);
			$kuserKgroup->save();
		}
	}

	/**
	 * delete all kuserKgroups that belong to kgroupId
	 *
	 * @param int $kgroupId
	 */
	public static function deleteByKgroupId($kgroupId){
		$kuserKgroups = self::retrieveByKgroupId($kgroupId);
		foreach($kuserKgroups as $kuserKgroup) {
			/* @var $kuserKgroup KuserKgroup */
			$kuserKgroup->setStatus(KuserKgroupStatus::DELETED);
			$kuserKgroup->save();
		}
	}

	/**
	 * get kusers by kgroupId
	 *
	 * @param int $kgroupId
	 * @return array
	 */
	public static function retrieveByKgroupId($kgroupId){
		$c = new Criteria();
		$c->add(KuserKgroupPeer::KGROUP_ID, $kgroupId);
		return KuserKgroupPeer::doSelect($c);
	}

	/**
	 * get kgroups by kuser
	 *
	 * @param int $kuserId
	 * @return array
	 */
	public static function retrieveByKuserId($kuserId){
		$c = new Criteria();
		$c->add(KuserKgroupPeer::KUSER_ID, $kuserId);
		return KuserKgroupPeer::doSelect($c);
	}

} // KuserKgroupPeer
