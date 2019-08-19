<?php
/**
 * @service sso
 * @package plugins.sso
 * @subpackage api.services
 */
class SsoService extends KalturaBaseService
{
	/**
	 * Adds a new sso configuration(vendorIntegration of type sso).
	 *
	 * @action add
	 * @param KalturaSso $sso a new sso configuration
	 * @return KalturaSso The new sso configuration
	 * @throws KalturaSsoErrors::DUPLICATE_SSO
	 * @throws KalturaSsoErrors::MISSING_MANDATORY_PARAMETER
	 */
	public function addAction(KalturaSso $sso)
	{
		$sso->validateParameters();
		$sso->validateDuplication();

		$newSso = new SsoVendorIntegration();
		$sso->toInsertableObject($newSso);
		$newSso->setStatus(VendorStatus::ACTIVE);
		$newSso->setVendorType(VendorTypeEnum::SSO);
		$newSso->save();
		$sso->fromObject($newSso);
		return $sso;
	}

	/**
	 * Retrieves sso object
	 * @action get
	 * @param int $ssoId The unique identifier in the sso's object
	 * @return KalturaSso The specified sso object
	 *
	 * @throws KalturaSsoErrors::INVALID_SSO_ID
	 */
	public function getAction($ssoId)
	{
		$dbSso = self::getSso($ssoId);
		if (!$dbSso)
		{
			throw new KalturaAPIException(KalturaSsoErrors::INVALID_SSO_ID, $ssoId);
		}
		$sso = new KalturaSso();
		$sso->fromObject($dbSso, $this->getResponseProfile());
		return $sso;
	}

	protected static function getSso($ssoId)
	{
		$dbSso = VendorIntegrationPeer::retrieveByPK($ssoId);
		if(!$dbSso || $dbSso->getVendorType() != VendorTypeEnum::SSO)
		{
			throw new KalturaAPIException(KalturaSsoErrors::INVALID_SSO_ID, $ssoId);
		}
		return $dbSso;
	}

	/**
	 * Delete sso by ID
	 *
	 * @action delete
	 * @param int $ssoId The unique identifier in the sso's object
	 * @return KalturaSso The deleted  object
	 * @throws KalturaSsoErrors::INVALID_SSO_ID
	 */
	public function deleteAction($ssoId)
	{
		$dbSso = self::getSso($ssoId);
		$dbSso->setStatus(VendorStatus::DELETED);
		$dbSso->save();
		$sso = new KalturaSso();
		$sso->fromObject($dbSso, $this->getResponseProfile());
		return $sso;
	}

	/**
	 * Update sso by ID
	 *
	 * @action update
	 * @param int $ssoId The unique identifier in the sso's objec
	 * @param KalturaSso $sso The updated object
	 * @return KalturaSso The updated  object
	 * @throws KalturaSsoErrors::INVALID_SSO_ID
	 * @throws KalturaSsoErrors::CANNOT_UPDATE_PARAMETER
	 */
	public function updateAction($ssoId, KalturaSso $sso)
	{
		$dbSso = self::getSso($ssoId);
		$dbSso = $sso->toUpdatableObject($dbSso);
		$dbSso->save();
		$sso = new KalturaSso();
		$sso->fromObject($dbSso, $this->getResponseProfile());
		return $sso;
	}

	/**
	 * Lists sso objects that are associated with an account.
	 *
	 * @action list
	 * @param KalturaSsoFilter $filter
	 * @param KalturaFilterPager $pager A limit for the number of records to display on a page
	 * @return KalturaSsoListResponse The list of sso objects
	 */
	public function listAction(KalturaSsoFilter $filter = null, KalturaFilterPager $pager = null)
	{
		if (!$filter)
		{
			$filter = new KalturaSsoFilter();
		}
		if(!$pager)
		{
			$pager = new KalturaFilterPager();
		}
		return $filter->getListResponse($pager, $this->getResponseProfile());
	}

	/**
	 * Login with SSO, getting redirect url according to application type and partner Id
	 *
	 * @action login
	 * @param string $userId
	 * @param KalturaApplicationType $applicationType
	 * @return string $redirectUrl
	 * @throws KalturaErrors::LOGIN_DATA_NOT_FOUND
	 * @throws KalturaErrors::INVALID_USER_ID
	 */
	public function loginAction($userId, $applicationType)
	{
		$loginData = UserLoginDataPeer::getByEmail($userId);
		if (!$loginData)
		{
			throw new KalturaAPIException(KalturaErrors::LOGIN_DATA_NOT_FOUND);
		}
		$partnerId = $loginData->getLastLoginPartnerId() ? $loginData->getLastLoginPartnerId() : $loginData->getConfigPartnerId();
		$applicationId = SsoPlugin::getCoreValue(KalturaSso::APPLICATION_TYPE, $applicationType);
		$dbSso = VendorIntegrationPeer::getVendorByPartnerAccountIdVendorType($applicationId, $partnerId, VendorTypeEnum::SSO);
		if(!$dbSso)
		{
			throw new KalturaAPIException(KalturaErrors::LOGIN_DATA_NOT_FOUND);
		}
		$sso = new KalturaSso();
		$sso->fromObject($dbSso, $this->getResponseProfile());
		return $sso->redirectUrl;
	}
}