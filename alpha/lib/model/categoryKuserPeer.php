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
	 * 
	 * @param int $categoryId
	 * @param int $kuserId
	 * @param array $requiredPermissions
	 * @param $con
	 * 
	 * @return categoryKuser
	 */
	public static function retrieveByCategoryIdAndActiveKuserId($categoryId, $kuserId = null, $requiredPermissions = null, $con = null)
	{
		if(is_null($kuserId))
			$kuserId = kCurrentContext::getCurrentKsKuserId();
			
		if(is_null($requiredPermissions))
			$requiredPermissions = array(PermissionName::CATEGORY_VIEW);
			
		$category = categoryPeer::retrieveByPK($categoryId);
		if(!$category)
			return null;
		
		if($category->getInheritedParentId())
			$categoryId = $category->getInheritedParentId();
			
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

		self::$s_criteria_filter->setFilter($c);
	}
	
} // categoryKuserPeer
