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
	
	public static function getCacheInvalidationKeys()
	{
		return array(array("kuserToUserRole:kuserId=%s", self::KUSER_ID));		
	}
	
	public function getKuserToUserRoleParentObjects(KuserToUserRole $object)
	{
		return array(
			$object->getkuser(),
			$object->getUserRole(),
		);
	}
	
	/* (non-PHPdoc)
	 * @see IRelatedObjectPeer::getParentObjects()
	 */
	public function getParentObjects(IBaseObject $object)
	{
		return $this->getKuserToUserRoleParentObjects($object);
	}

	/* (non-PHPdoc)
	 * @see IRelatedObjectPeer::getRootObjects()
	 */
	public function getRootObjects(IBaseObject $object)
	{
		return $this->getParentObjects($object);
	}

	/* (non-PHPdoc)
	 * @see IRelatedObjectPeer::isReferenced()
	 */
	public function isReferenced(IBaseObject $object)
	{
		return false;
	}
} // KuserToUserRolePeer
