<?php
/**
 * @service sso
 * @package plugins.sso
 * @subpackage api.services
 */
class SsoService extends KalturaBaseService
{
	/**
	 * Adds a new sso configuration.
	 *
	 * @action add
	 * @param KalturaSso $sso a new sso configuration
	 * @return KalturaSso The new sso configuration
	 * @throws KalturaSsoErrors::DUPLICATE_SSO
	 */
	public function addAction(KalturaSso $sso)
	{
		$dbSso = $sso->toInsertableObject();
		$dbSso->setStatus(SsoStatus::ACTIVE);
		$dbSso->save();
		$sso->fromObject($dbSso, $this->getResponseProfile());
		return $sso;
	}

	/**
	 * Retrieves sso object
	 * @action get
	 * @param int $ssoId The unique identifier in the sso's object
	 * @return KalturaSso The specified sso object
	 * @throws KalturaSsoErrors::INVALID_SSO_ID
	 */
	public function getAction($ssoId)
	{
		$dbSso = SsoPeer::retrieveByPK($ssoId);
		if (!$dbSso)
		{
			throw new KalturaAPIException(KalturaSsoErrors::INVALID_SSO_ID, $ssoId);
		}
		$sso = new KalturaSso();
		$sso->fromObject($dbSso, $this->getResponseProfile());
		return $sso;
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
		$dbSso = SsoPeer::retrieveByPK($ssoId);
		if (!$dbSso)
		{
			throw new KalturaAPIException(KalturaSsoErrors::INVALID_SSO_ID, $ssoId);
		}
		$dbSso->setStatus(SsoStatus::DELETED);
		$dbSso->save();
		$sso = new KalturaSso();
		$sso->fromObject($dbSso, $this->getResponseProfile());
		return $sso;
	}

	/**
	 * Update sso by ID
	 *
	 * @action update
	 * @param int $ssoId The unique identifier in the sso's object
	 * @param KalturaSso $sso The updated object
	 * @return KalturaSso The updated  object
	 * @throws KalturaSsoErrors::INVALID_SSO_ID
	 */
	public function updateAction($ssoId, KalturaSso $sso)
	{
		$dbSso = SsoPeer::retrieveByPK($ssoId);
		if (!$dbSso)
		{
			throw new KalturaAPIException(KalturaSsoErrors::INVALID_SSO_ID, $ssoId);
		}
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
	 * or according to application type and domain
	 * @action login
	 * @param string $userId
	 * @param string $applicationType
	 * @param int $partnerId
	 * @return string $redirectUrl
	 * @throws KalturaSsoErrors::SSO_NOT_FOUND
	 */
	public function loginAction($userId = null, $applicationType , $partnerId = null)
	{
		if (!$userId)
		{
			$this->validatePartnerUsingSso($partnerId);
			$dbSso = KalturaSso::getSso($partnerId, $applicationType, null);
		}
		else
		{
			$domain = KalturaSso::getDomainFromUser($userId);
			if ($partnerId)
			{
				$this->validatePartnerUsingSso($partnerId);
				$dbSso = KalturaSso::getSso($partnerId, $applicationType, $domain);
			}
			else
			{
				$dbSso = $this->getSsoWithoutPID($userId, $applicationType, $domain);
			}
		}
		$sso = new KalturaSso();
		$sso->fromObject($dbSso, $this->getResponseProfile());
		return $sso->redirectUrl;
	}

	protected function getSsoWithoutPID($userId, $applicationType, $domain)
	{
		try
		{
			$partnerId = UserLoginDataPeer::getPartnerIdFromLoginData($userId);
			$this->validatePartnerUsingSso($partnerId);
			//try login by PID
			$dbSso = KalturaSso::getSso($partnerId, $applicationType, $domain);
		}
		catch (Exception $e)
		{
			//try login by DOMAIN
			$dbSso = KalturaSso::getSso(null, $applicationType, $domain);
		}
		return $dbSso;
	}

	protected function validatePartnerUsingSso($partnerId)
	{
		$partner = PartnerPeer::retrieveByPK($partnerId);
		if (!$partner || !$partner->getUseSso())
		{
			KalturaLog::debug("FEATURE_FORBIDDEN, $this->serviceId . '->' . $this->actionName");
			throw new KalturaAPIException(KalturaSsoErrors::SSO_NOT_FOUND);
		}
	}

}