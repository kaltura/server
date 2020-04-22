<?php
/**
 * @package plugins.vendor
 * @subpackage zoom.model
 */
class ZoomHelper
{
	/** payload data */
	const ACCOUNT_ID = "account_id";
	const PAYLOAD = 'payload';

	/** php body */
	const PHP_INPUT = 'php://input';

	/* @var zoomVendorIntegration $zoomIntegration */
	protected static $zoomIntegration;

    /**
     * @param $accountId
     * @param bool $includeDeleted
     * @return null|zoomVendorIntegration
     * @throws PropelException
     */
	public static function getZoomIntegrationByAccountId($accountId, $includeDeleted = false)
	{
		if($includeDeleted)
		{
			self::$zoomIntegration = VendorIntegrationPeer::retrieveSingleVendorPerPartnerNoFilter($accountId, VendorTypeEnum::ZOOM_ACCOUNT);
		}
		else
		{
			self::$zoomIntegration = VendorIntegrationPeer::retrieveSingleVendorPerPartner($accountId, VendorTypeEnum::ZOOM_ACCOUNT);
		}

		return self::$zoomIntegration;
	}

	/**
	 * @return zoomVendorIntegration
	 */
	public static function getZoomIntegration()
	{
		self::verifyZoomIntegration(self::$zoomIntegration);
		return self::$zoomIntegration;
	}

	public static function setZoomIntegration($zoomIntegration)
	{
		self::verifyZoomIntegration($zoomIntegration);
		self::$zoomIntegration = $zoomIntegration;
	}

	public static function exitWithError($errMsg)
	{
		KalturaLog::err($errMsg);
		if(self::$zoomIntegration)
		{
			self::$zoomIntegration->saveLastError($errMsg);
		}

		KExternalErrors::dieGracefully();
	}

	/**
	 * @param array $zoomUserPermissions
	 * @return bool
	 */
	public static function canConfigureEventSubscription($zoomUserPermissions)
	{
		if(in_array('Recording:Read', $zoomUserPermissions) && in_array('Recording:Edit', $zoomUserPermissions))
		{
			return true;
		}

		return false;
	}

	/**
	 * redirects to new URL
	 * @param $url
	 */
	public static function redirect($url)
	{
		$redirect  = new kRendererRedirect($url);
		$redirect->output();
		KExternalErrors::dieGracefully();
	}

    /**
     * @param array $tokens
     * @param array $zoomConfiguration
     * @throws Exception
     */
	public static function loadLoginPage($tokens, $zoomConfiguration)
	{
		$file_path = dirname(__FILE__) . '/../api/webPage/kalturaZoomLoginPage.html';
		if (file_exists($file_path))
		{
			$page = file_get_contents($file_path);
			$tokensString = json_encode($tokens);
			$verificationToken = $zoomConfiguration[kZoomOauth::VERIFICATION_TOKEN];
			list($enc, $iv) = AESEncrypt::encrypt($verificationToken, $tokensString);
			$page = str_replace('@BaseServiceUrl@', requestUtils::getHost(), $page);
			$page = str_replace('@encryptData@', base64_encode($enc), $page);
			$page = str_replace('@iv@', base64_encode($iv), $page);
			echo $page;
			die();
		}
	}

	/**
	 * @param ZoomVendorIntegration $zoomIntegration
	 * @param $accountId
	 * @param $ks
	 * @throws Exception
	 */
	public static function loadSubmitPage($zoomIntegration, $accountId, $ks)
	{
		$file_path = dirname(__FILE__) . '/../api/webPage/KalturaZoomRegistrationPage.html';
		if (file_exists($file_path))
		{
			$page = file_get_contents($file_path);
			/** @noinspection PhpUndefinedMethodInspection */
			$page = str_replace('@ks@', $ks->getOriginalString(), $page);
			$page = str_replace('@BaseServiceUrl@', requestUtils::getHost(), $page);
			if (!is_null($zoomIntegration))
			{
				$page = str_replace('@defaultUserID@', $zoomIntegration->getDefaultUserEMail() , $page);
				$page = str_replace('@zoomCategory@', $zoomIntegration->getZoomCategory() ? $zoomIntegration->getZoomCategory()  : 'Zoom Recordings'  , $page);
				$page = str_replace('@enableRecordingUpload@', $zoomIntegration->getStatus()== VendorStatus::ACTIVE ? 'checked'  : ''  , $page);
				$page = str_replace('@createUserIfNotExist@', $zoomIntegration->getCreateUserIfNotExist() ? 'checked'  : ''  , $page);
			}
			else
			{
				$page = str_replace('@defaultUserID@', '' , $page);
				$page = str_replace('@zoomCategory@', 'Zoom Recordings', $page);
				$page = str_replace('@enableRecordingUpload@', 'checked', $page);
				$page = str_replace('@createUserIfNotExist@', 'checked', $page);
			}
			$page = str_replace('@accountId@', $accountId , $page);
			echo $page;
			die();
		}

		throw new KalturaAPIException('unable to find submit page, please contact support');
	}

	/**
	 * @param $data
	 * @return string
	 */
	public static function extractAccountIdFromDeAuthPayload($data)
	{
		$payload = $data[self::PAYLOAD];
		$accountId = $payload[self::ACCOUNT_ID];
		return $accountId;
	}

	/**
	 * @return mixed
	 * @throws Exception
	 */
	public static function getPayloadData()
	{
		$request_body = file_get_contents(self::PHP_INPUT);
		$data = json_decode($request_body, true);
		return $data;
	}

	/**
	 * @param int $partnerId
	 * @param string $categoryFullName
	 * @param bool $createIfNotExist
	 * @return int id;
	 * @throws PropelException
	 * @throws Exception
	 */
	public static function createCategoryForZoom($partnerId, $categoryFullName, $createIfNotExist = true)
	{
		$category = categoryPeer::getByFullNameExactMatch($categoryFullName, null, $partnerId);
		if($category)
		{
			KalturaLog::debug('Category: ' . $categoryFullName . ' already exist for partner: ' . $partnerId);
			return $category->getId();
		}

		if(!$createIfNotExist)
		{
			return null;
		}

		$categoryDb = new category();

		//Check if this is a root category or child , if child get its parent ID
		$categoryNameArray = explode(categoryPeer::CATEGORY_SEPARATOR, $categoryFullName);
		$categoryName = end($categoryNameArray);
		if(count($categoryNameArray) > 1)
		{
			$parentCategoryFullNameArray = array_slice ($categoryNameArray,0,-1);
			$parentCategoryFullName = implode(categoryPeer::CATEGORY_SEPARATOR, $parentCategoryFullNameArray );
			$parentCategory = categoryPeer::getByFullNameExactMatch($parentCategoryFullName, null, $partnerId);
			if(!$parentCategory)
			{
				self::exitWithError(kZoomErrorMessages::PARENT_CATEGORY_NOT_FOUND . $parentCategoryFullName);
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

	/**
	 * @param ZoomVendorIntegration $zoomIntegration
     */
	public static function verifyZoomIntegration($zoomIntegration)
	{
		if (!$zoomIntegration)
		{
			self::exitWithError(kZoomErrorMessages::NO_INTEGRATION_DATA);
		}

		if($zoomIntegration->getStatus() == VendorStatus::DISABLED)
		{
			self::exitWithError(kZoomErrorMessages::UPLOAD_DISABLED);
		}
	}
}