<?php

/**
 * Manage application authentication tokens
 *
 * @service appToken
 */
class AppTokenService extends KalturaBaseService
{
	public function initService($serviceId, $serviceName, $actionName)
	{
		parent::initService($serviceId, $serviceName, $actionName);
		$this->applyPartnerFilterForClass('AppToken');
	}
	
	/**
	 * Add new application authentication token
	 * 
	 * @action add
	 * @param KalturaAppToken $appToken
	 * @return KalturaAppToken
	 */
	function addAction(KalturaAppToken $appToken)
	{
		$dbAppToken = $appToken->toInsertableObject();
		$dbAppToken->save();
		
		$appToken = new KalturaAppToken();
		$appToken->fromObject($dbAppToken, $this->getResponseProfile());
		return $appToken;
	}
	
	/**
	 * Get application authentication token by ID
	 * 
	 * @action get
	 * @param string $id
	 * @return KalturaAppToken
	 * 
	 * @throws KalturaErrors::APP_TOKEN_ID_NOT_FOUND
	 */
	function getAction($id)
	{
		$dbAppToken = AppTokenPeer::retrieveByPK($id);
		if(!$dbAppToken)
			throw new KalturaAPIException(KalturaErrors::APP_TOKEN_ID_NOT_FOUND, $id);
		
		$appToken = new KalturaAppToken();
		$appToken->fromObject($dbAppToken, $this->getResponseProfile());
		return $appToken;
	}
	
	/**
	 * Update application authentication token by ID
	 * 
	 * @action update
	 * @param string $id
	 * @param KalturaAppToken $appToken
	 * @return KalturaAppToken
	 * 
	 * @throws KalturaErrors::APP_TOKEN_ID_NOT_FOUND
	 */
	function updateAction($id, KalturaAppToken $appToken)
	{
		$dbAppToken = AppTokenPeer::retrieveByPK($id);
		if(!$dbAppToken)
			throw new KalturaAPIException(KalturaErrors::APP_TOKEN_ID_NOT_FOUND, $id);
		
		$appToken->toUpdatableObject($dbAppToken);
		$dbAppToken->save();
		
		$appToken = new KalturaAppToken();
		$appToken->fromObject($dbAppToken, $this->getResponseProfile());
		return $appToken;
	}
	
	/**
	 * Delete application authentication token by ID
	 * 
	 * @action delete
	 * @param string $id
	 * 
	 * @throws KalturaErrors::APP_TOKEN_ID_NOT_FOUND
	 */
	function deleteAction($id)
	{
		$dbAppToken = AppTokenPeer::retrieveByPK($id);
		if(!$dbAppToken)
			throw new KalturaAPIException(KalturaErrors::APP_TOKEN_ID_NOT_FOUND, $id);
		
		$invalidSessionKey = ks::buildSessionIdHash($this->getPartnerId(), $id); 
		invalidSessionPeer::invalidateByKey($invalidSessionKey, invalidSession::INVALID_SESSION_TYPE_SESSION_ID, $dbAppToken->getExpiry());
		$dbAppToken->setStatus(AppTokenStatus::DELETED);
		$dbAppToken->save();
	}
	
	/**
	 * List application authentication tokens by filter and pager
	 * 
	 * @action list
	 * @param KalturaFilterPager $filter
	 * @param KalturaAppTokenFilter $pager
	 * @return KalturaAppTokenListResponse
	 */
	function listAction(KalturaAppTokenFilter $filter = null, KalturaFilterPager $pager = null)
	{
		if(!$filter)
			$filter = new KalturaAppTokenFilter();
		
		if(!$pager)
			$pager = new KalturaFilterPager();


		if ($filter->sessionUserIdEqual)
		{
			$kuser = kuserPeer::getKuserByPartnerAndUid ($this->getPartnerId() , $filter->sessionUserIdEqual );
			if($kuser)
				$filter->sessionUserIdEqual = $kuser->getId();
			else
			{
				$response = new KalturaAppTokenListResponse();
				$response->totalCount = 0;
				return $response;
			}
		}

		$c = new Criteria();
		$appTokenFilter = $filter->toObject();
		$appTokenFilter->attachToCriteria($c);
		$pager->attachToCriteria($c);
		
		$list = AppTokenPeer::doSelect($c);
		
		$totalCount = null;
		$resultCount = count($list);
		if($resultCount && ($resultCount < $pager->pageSize))
		{
			$totalCount = ($pager->pageIndex - 1) * $pager->pageSize + $resultCount;
		}
		else
		{
			KalturaFilterPager::detachFromCriteria($c);
			$totalCount = AppTokenPeer::doCount($c);
		}
		
		$response = new KalturaAppTokenListResponse();
		$response->totalCount = $totalCount;
		$response->objects = KalturaAppTokenArray::fromDbArray($list, $this->getResponseProfile());
		return $response;
	}
	
	/**
	 * Starts a new KS (kaltura Session) based on an application authentication token ID
	 * 
	 * @action startSession
	 * @param string $id application token ID
	 * @param string $tokenHash a hash [MD5, SHA1, SHA256 and SHA512 are supported] of the current KS concatenated with the application token 
	 * @param string $userId session user ID, will be ignored if a different user ID already defined on the application token
	 * @param KalturaSessionType $type session type, will be ignored if a different session type is already defined on the application token
	 * @param int $expiry session expiry (in seconds), could be overridden by shorter expiry of the application token 
	 * @throws KalturaErrors::APP_TOKEN_ID_NOT_FOUND
	 * @return KalturaSessionInfo
	 */
	function startSessionAction($id, $tokenHash, $userId = null, $type = null, $expiry = null)
	{
		$dbAppToken = AppTokenPeer::retrieveByPK($id);
		if(!$dbAppToken)
			throw new KalturaAPIException(KalturaErrors::APP_TOKEN_ID_NOT_FOUND, $id);
		
		if($dbAppToken->getStatus() != AppTokenStatus::ACTIVE)
			throw new KalturaAPIException(KalturaErrors::APP_TOKEN_NOT_ACTIVE, $id);
		
		$appTokenHash = $dbAppToken->calcHash();
		if($appTokenHash !== $tokenHash)
			throw new KalturaAPIException(KalturaErrors::INVALID_APP_TOKEN_HASH);
		
		KalturaResponseCacher::disableCache();
		
		$tokenExpiry = $dbAppToken->getSessionDuration();
		if(!is_null($dbAppToken->getExpiry()))
		{
			$tokenExpiry = min($tokenExpiry, $dbAppToken->getExpiry() - time());
			if($tokenExpiry < 0)
				throw new KalturaAPIException(KalturaErrors::APP_TOKEN_EXPIRED, $id);
		}
		if(!$expiry)
		{
			$expiry = $tokenExpiry;
		}
		$expiry = min($expiry, $tokenExpiry);
		
		if(!is_null($dbAppToken->getSessionType()))
			$type = $dbAppToken->getSessionType();
		if(is_null($type))
			$type = SessionType::USER;
			
		if(!is_null($dbAppToken->getSessionUserId()))
			$userId = $dbAppToken->getSessionUserId();
			
		$partnerId = kCurrentContext::getCurrentPartnerId();
		$partner = PartnerPeer::retrieveByPK($partnerId);
		$secret = $type == SessionType::ADMIN ? $partner->getAdminSecret() : $partner->getSecret();
		
		$privilegesArray = array(
			ks::PRIVILEGE_SESSION_ID => array($id),
			ks::PRIVILEGE_APP_TOKEN => array($id)
		);
		if($dbAppToken->getSessionPrivileges())
		{
			$privilegesArray = array_merge_recursive($privilegesArray, ks::parsePrivileges($dbAppToken->getSessionPrivileges()));
		}
		$privileges = ks::buildPrivileges($privilegesArray);
		
		$ks = kSessionUtils::createKSession($partnerId, $secret, $userId, $expiry, $type, $privileges);
		if(!$ks)
			throw new KalturaAPIException(APIErrors::START_SESSION_ERROR, $partnerId);
			
		$sessionInfo = new KalturaSessionInfo();
		$sessionInfo->ks = $ks->toSecureString();
		$sessionInfo->partnerId = $partnerId;
		$sessionInfo->userId = $userId;
		$sessionInfo->expiry = $ks->valid_until;
		$sessionInfo->sessionType = $type;
		$sessionInfo->privileges = $privileges;
		
		return $sessionInfo;
	}
	
};
