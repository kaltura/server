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
	 * @param int $categoryId
	 * @param int $kuserId
	 * @param $con
	 * 
	 * @return array
	 */
	public static function doSelectByActiveKusersByCategoryId($categoryId, $con = null)
	{
		$criteria = new Criteria();

		$criteria->add(categoryKuserPeer::CATEGORY_ID, $categoryId);
		$criteria->add(categoryKuserPeer::STATUS, CategoryKuserStatus::ACTIVE);

		return categoryKuserPeer::doSelect($criteria, $con);
	}
	
} // categoryKuserPeer
