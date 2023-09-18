<?php


/**
 * Skeleton subclass for performing query and update operations on the 'kuser_to_user_role' table.
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
class KuserToUserRolePeer extends BaseKuserToUserRolePeer implements IRelatedObjectPeer
{
	/**
	 * Creates default criteria filter
	 * To keep backward compatibility, do not fetch roles where app_guid is null
	 */
	public static function setDefaultCriteriaFilter()
	{
		if(self::$s_criteria_filter == null)
			self::$s_criteria_filter = new criteriaFilter();
		
		// to keep backward compatibility, do not retrieve results where app_guid !== null
		$c =  KalturaCriteria::create(KuserToUserRolePeer::OM_CLASS);
		$c->addAnd(KuserToUserRolePeer::APP_GUID, null, Criteria::ISNULL);
		
		self::$s_criteria_filter->setFilter($c);
	}
	
	public static function getCacheInvalidationKeys()
	{
		return array(array("kuserToUserRole:kuserId=%s", self::KUSER_ID), array("kuserToUserRole:id=%s", self::ID));
	}
	
	/**
	 * Get objects by kuser and user role IDs
	 * @param int $kuserId
	 * @param int $userRoleId
	 * @return array Array of selected KuserToUserRole Objects
	 */
	public static function getByKuserAndUserRoleIds($kuserId, $userRoleId)
	{
		$c = new Criteria();
		$c->addAnd(self::KUSER_ID, $kuserId, Criteria::EQUAL);
		$c->addAnd(self::USER_ROLE_ID, $userRoleId, Criteria::EQUAL);
		return self::doSelect($c);
	}
	
	/* (non-PHPdoc)
	 * @see IRelatedObjectPeer::getRootObjects()
	 */
	public function getRootObjects(IRelatedObject $object)
	{
		/* @var $object KuserToUserRole */
		return array(
			$object->getkuser(),
			$object->getUserRole(),
		);
	}
	
	/* (non-PHPdoc)
	 * @see IRelatedObjectPeer::isReferenced()
	 */
	public function isReferenced(IRelatedObject $object)
	{
		return false;
	}
	
	/**
	 * Get object by kuser and appGuid
	 *
	 * @param $kuserId
	 * @param $appGuid
	 *
	 * @return KuserToUserRole|null
	 * @throws PropelException
	 */
	public static function getByKuserIdAndAppGuid($kuserId, $appGuid)
	{
		$c = new Criteria();
		$c->addAnd(self::KUSER_ID, $kuserId, Criteria::EQUAL);
		$c->addAnd(self::APP_GUID, $appGuid, Criteria::EQUAL);
		
		self::setUseCriteriaFilter(false);
		$res = self::doSelectOne($c);
		self::setUseCriteriaFilter(true);
		
		return $res;
	}
} // KuserToUserRolePeer
