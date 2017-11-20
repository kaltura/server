<?php


/**
 * Skeleton subclass for performing query and update operations on the 'category_kuser' table.
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
class categoryKuserPeer extends BasecategoryKuserPeer {
	
	/**
	 * 
	 * @param int $categoryId
	 * @param int $kuserId
	 * @param $con
	 * 
	 * @return categoryKuser
	 */
	public static function retrieveByCategoryIdAndKuserId($categoryId, $kuserId, $con = null)
	{
		$criteria = new Criteria();

		$criteria->add(categoryKuserPeer::CATEGORY_ID, $categoryId);
		$criteria->add(categoryKuserPeer::KUSER_ID, $kuserId);

		return categoryKuserPeer::doSelectOne($criteria, $con);
	}

	/**
	 *
	 * @param int $kuserId
	 * @param $con
	 *
	 * @return array Array of categoryKuser
	 */
	public static function retrieveByKuserId($kuserId, $con = null)
	{
		$criteria = new Criteria();
		$criteria->add(categoryKuserPeer::KUSER_ID, $kuserId);

		return categoryKuserPeer::doSelect($criteria, $con);
	}
	
	/**
	 * 
	 * @param int $kuserId
	 * @return bool - no need to fetch the objects
	 */
	public static function isCategroyKuserExistsForKuser($kuserId)
	{
		$criteria = new Criteria();

		$criteria->add(categoryKuserPeer::KUSER_ID, $kuserId);
		
		$categoryKuser = categoryKuserPeer::doSelectOne($criteria);
		
		if($categoryKuser)
			return true;
			
		return false;
	}


	/**
	 *  this function return categoryUser if the user has explicit or implicit (by group) required permissions on the category
	 *
	 * @param int $categoryId
	 * @param int $kuserId
	 * @param array $requiredPermissions
	 * @param bool $supportGroups
	 * @param null $con
	 * @return categoryKuser|null
	 */
	public static function retrievePermittedKuserInCategory($categoryId, $kuserId = null, $requiredPermissions = null, $supportGroups = true, $con = null){
		$category = categoryPeer::retrieveByPK($categoryId);
		if(!$category)
			return null;

		if($category->getInheritedParentId())
			$categoryId = $category->getInheritedParentId();

		if(is_null($kuserId))
			$kuserId = kCurrentContext::getCurrentKsKuserId();

		if(is_null($requiredPermissions))
			$requiredPermissions = array(PermissionName::CATEGORY_VIEW);

		$categoryKuser = self::retrieveByCategoryIdAndActiveKuserId($categoryId, $kuserId, $requiredPermissions, $con);
		if (!is_null($categoryKuser)){
			return $categoryKuser;
		}

		//check if kuserId has permission in category by a junction group
		if($supportGroups)
		{
			$kgroupIds = KuserKgroupPeer::retrieveKgroupIdsByKuserId($kuserId);
			if (count($kgroupIds) == 0)
				return null;

			$criteria = new Criteria();
			$criteria->add(categoryKuserPeer::CATEGORY_ID, $categoryId);
			$criteria->add(categoryKuserPeer::KUSER_ID, $kgroupIds, Criteria::IN);
			$criteria->add(categoryKuserPeer::STATUS, CategoryKuserStatus::ACTIVE);
			$categoryKusers = categoryKuserPeer::doSelect($criteria, $con);
			if(!$categoryKusers)
				return null;
			foreach( $categoryKusers as $categoryKuser){
				foreach($requiredPermissions as $requiredPermission){
					if($categoryKuser->hasPermission($requiredPermission)){
						return $categoryKuser;
					}
				}
			}
		}
		return null;
	}
	
	/**
	 * 
	 * @param int $categoryId
	 * @param int $kuserId
	 * @param array $requiredPermissions
	 * @param $con
	 * 
	 * @return categoryKuser
	 */
	public static function retrieveByCategoryIdAndActiveKuserId($categoryId, $kuserId, $requiredPermissions, $con = null)
	{
		$criteria = new Criteria();
		$criteria->add(categoryKuserPeer::CATEGORY_ID, $categoryId);
		$criteria->add(categoryKuserPeer::KUSER_ID, $kuserId);
		$criteria->add(categoryKuserPeer::STATUS, CategoryKuserStatus::ACTIVE);

		$categoryKuser = categoryKuserPeer::doSelectOne($criteria, $con);
		if(!$categoryKuser)
			return null;
			
		foreach($requiredPermissions as $requiredPermission)
			if(!$categoryKuser->hasPermission($requiredPermission))
				return null;
				
		return $categoryKuser;
	}
	
	/**
	 * 
	 * @param array $categoriesIds
	 * @param int $kuserId
	 * @param array $requiredPermissions
	 * @param $con
	 * 
	 * @return categoryKuser
	 */
	public static function areCategoriesAllowed(array $categoriesIds, $kuserId = null, $requiredPermissions = null, $con = null)
	{
		if(is_null($kuserId))
			$kuserId = kCurrentContext::getCurrentKsKuserId();
			
		if(is_null($requiredPermissions))
			$requiredPermissions = array(PermissionName::CATEGORY_VIEW);
			
		$categories = categoryPeer::retrieveByPKs($categoriesIds);
		if(count($categories) < count($categoriesIds))
			return false;
		
		$categoriesIds = array();
		foreach($categories as $category)
		{
			/* @var $category category */
			$categoriesIds[] = $category->getInheritedParentId() ? $category->getInheritedParentId() : $category->getId();
		}
		$categoriesIds = array_unique($categoriesIds);
		
		$criteria = new Criteria();
		$criteria->add(categoryKuserPeer::CATEGORY_ID, $categoriesIds, Criteria::IN);
		$criteria->add(categoryKuserPeer::KUSER_ID, $kuserId);
		$criteria->add(categoryKuserPeer::STATUS, CategoryKuserStatus::ACTIVE);

		$categoryKusers = categoryKuserPeer::doSelectOne($criteria, $con);
		if(count($categoryKusers) < count($categoriesIds))
			return false;
			
		foreach($categoryKusers as $categoryKuser)
		{
			$permissions = explode(',', $categoryKuser->getPermissionNames());
			foreach($requiredPermissions as $requiredPermission)
				if(!in_array($requiredPermission, $permissions))
					return false;
		}
		return true;
	}
	
	/**
	 * 
	 * @param int $categoryId
	 * @param int $kuserId
	 * @param $con
	 * 
	 * @return array
	 */
	public static function retrieveActiveKusersByCategoryId($categoryId, $con = null)
	{
		$criteria = new Criteria();

		$criteria->add(categoryKuserPeer::CATEGORY_ID, $categoryId);
		$criteria->add(categoryKuserPeer::STATUS, CategoryKuserStatus::ACTIVE);

		return categoryKuserPeer::doSelect($criteria, $con);
	}
	
	
	public static function setDefaultCriteriaFilter ()
	{
		if ( self::$s_criteria_filter == null )
		{
			self::$s_criteria_filter = new criteriaFilter ();
		}
		
		$c =  KalturaCriteria::create(categoryKuserPeer::OM_CLASS); 
		$c->addAnd ( categoryKuserPeer::STATUS, array(CategoryKuserStatus::DELETED), Criteria::NOT_IN);
		$partnerId = kCurrentContext::$ks_partner_id;
		$c->addAnd ( categoryKuserPeer::PARTNER_ID,$partnerId);

		self::$s_criteria_filter->setFilter($c);
	}
	
	public static function getCacheInvalidationKeys()
	{
		return array(array("categoryKuser:categoryId=%s", self::CATEGORY_ID));		
	}
} // categoryKuserPeer
