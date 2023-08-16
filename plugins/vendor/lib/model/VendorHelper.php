<?php
/**
 * @package plugins.vendor
 * @subpackage model
 */
class VendorHelper
{
	const ERROR_PARENT_CATEGORY_NOT_FOUND = 'Could not find parent category id';
	
	/**
	 * Redirects to new URL
	 * @param $url
	 */
	public static function redirect($url)
	{
		$redirect  = new kRendererRedirect($url);
		$redirect->output();
		KExternalErrors::dieGracefully();
	}
	
	public static function objectChanged()
	{
	
	}
	
	public static function getDropFolderRelatedInfo()
	{
	
	}

	public static function setDefaultValuesIntegration()
	{
	
	}
	
	public static function verifyAndSetDropFolderConfig()
	{
	
	}
	
	public static function setDeletePolicy()
	{
	
	}
	
	public static function getDropFolderStatus($status)
	{
		switch ($status)
		{
			case VendorIntegrationStatus::DISABLED:
			{
				return DropFolderStatus::DISABLED;
			}
			case VendorIntegrationStatus::ACTIVE:
			{
				return DropFolderStatus::ENABLED;
			}
			case VendorIntegrationStatus::DELETED:
			{
				return DropFolderStatus::DELETED;
			}
			default:
			{
				return DropFolderStatus::ERROR;
			}
		}
	}
	
	/**
	 * @param $partnerId
	 * @param $accountId
	 * @param $ks
	 * @throws Exception
	 */
	public static function loadSubmitPage($partnerId, $accountId, $ks, $filePath)
	{
		if (!file_exists($filePath))
		{
			KalturaLog::err("Submit page not found at $filePath");
			throw new KalturaAPIException(KalturaVendorIntegrationErrors::SUBMIT_PAGE_NOT_FOUND);
		}
		
		$page = file_get_contents($filePath);
		$page = str_replace('@ks@', $ks->getOriginalString(), $page);
		$page = str_replace('@BaseServiceUrl@', requestUtils::getHost(), $page);
		$page = str_replace('@partnerId@', $partnerId, $page);
		$page = str_replace('@accountId@', $accountId, $page);
		
		echo $page;
		die();
	}
	
	/**
	 * @param int $partnerId
	 * @param string $categoryFullName
	 * @param bool $createIfNotExist
	 * @return int id;
	 * @throws PropelException
	 * @throws Exception
	 */
	public static function createCategoryForVendorIntegration($partnerId, $categoryFullName, $vendorIntegration = null)
	{
		$category = categoryPeer::getByFullNameExactMatch($categoryFullName, null, $partnerId);
		if ($category)
		{
			KalturaLog::debug('Category: ' . $categoryFullName . ' already exist for partner: ' . $partnerId);
			return $category->getId();
		}
		
		$categoryDb = new category();
		
		//Check if this is a root category or child , if child get its parent ID
		$categoryNameArray = explode(categoryPeer::CATEGORY_SEPARATOR, $categoryFullName);
		$categoryName = end($categoryNameArray);
		if (count($categoryNameArray) > 1)
		{
			$parentCategoryFullNameArray = array_slice($categoryNameArray,0,-1);
			$parentCategoryFullName = implode(categoryPeer::CATEGORY_SEPARATOR, $parentCategoryFullNameArray);
			$parentCategory = categoryPeer::getByFullNameExactMatch($parentCategoryFullName, null, $partnerId);
			if (!$parentCategory)
			{
				self::exitWithError(self::ERROR_PARENT_CATEGORY_NOT_FOUND . $parentCategoryFullName, $vendorIntegration);
			}
			
			$parentCategoryId = $parentCategory->getId();
			$categoryDb->setParentId($parentCategoryId);
		}
		
		$categoryDb->setName($categoryName);
		$categoryDb->setFullName($categoryFullName);
		$categoryDb->setPartnerId($partnerId);
		$categoryDb->save();
		return $categoryDb->getId();
	}
	
	public static function exitWithError($errMsg, $vendorIntegration)
	{
		KalturaLog::err($errMsg);
		if ($vendorIntegration)
		{
			$vendorIntegration->saveLastError($errMsg);
		}
		
		KExternalErrors::dieGracefully();
	}
}
