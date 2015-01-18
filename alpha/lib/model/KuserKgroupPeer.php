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
	 * @param int $kuserId
	 * @param int $kgroupId
	 */
	static public function getByKuserIdAndKgroupId ($kuserId, $kgroupId){

		$criteria = new Criteria();
		$criteria->add(KuserKgroupPeer::KUSER_ID, $kuserId);
		$criteria->add(KuserKgroupPeer::KGROUP_ID, $kgroupId);
		$criteria->add(KuserKgroupPeer::STATUS, KuserKgroupStatus::ACTIVE);

		return categoryKuserPeer::doSelectOne($criteria);
	}

	/**
	 * delete all kuserKgroups that belong to kuserId
	 *
	 * @param int $kuserId
	 */
	public static function deleteByKuserId(Kuser $kuserId){
		$kuserKgroups = self::getKgroupsByKuserId($kuserId);
		if (!is_null($kuserKgroups) && count($kuserKgroups)) {
			foreach($kuserKgroups as $kuserKgroup) {
				/* @var $kuserKgroup KuserKgroup */
				$kuserKgroup->setStatus(KuserKgroupStatus::DELETED);
				$kuserKgroup->save();
			}
		}
	}

	/**
	 * delete all kuserKgroups that belong to kgroupId
	 *
	 * @param int $kgroupId
	 */
	public static function deleteByKgroupId($kgroupId){
		$kuserKgroups = self::getKusersByKgroupId($kgroupId);
		if (!is_null($kuserKgroups) && count($kuserKgroups)) {
			foreach($kuserKgroups as $kuserKgroup) {
				/* @var $kuserKgroup KuserKgroup */
				$kuserKgroup->setStatus(KuserKgroupStatus::DELETED);
				$kuserKgroup->save();
			}
		}
	}

	/**
	 * get kusers by kgroupId
	 *
	 * @param int $kgroupId
	 * @return array
	 */
	public static function getKusersByKgroupId($kgroupId){
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
	public static function getKgroupsByKuserId($kuserId){
		$c = new Criteria();
		$c->add(KuserKgroupPeer::KUSER_ID, $kuserId);
		return KuserKgroupPeer::doSelect($c);
	}

} // KuserKgroupPeer
