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
	 * Get application authentication token by id
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
	 * Update application authentication token by id
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
	 * Delete application authentication token by id
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
	 * Starts a new KS (kaltura Session) based on application authentication token id
	 * 
	 * @action startSession
	 * @param string $id application token id
	 * @param string $tokenHash hashed token, built of sha1 on current KS concatenated with the application token
	 * @param string $userId session user id, will be ignored if a different user id already defined on the application token
	 * @param KalturaSessionType $type session type, will be ignored if a different session type already defined on the application token
	 * @param int $expiry session expiry (in seconds), could be overwritten by shorter expiry of the application token and the session-expiry that defined on the application token 
	 * @param string $privileges session privileges, will be appended to privileges that defined on the application token
	 * @throws KalturaErrors::APP_TOKEN_ID_NOT_FOUND
	 * @return string
	 */
	function startSessionAction($id, $tokenHash, $userId = null, $type = null, $expiry = null, $privileges = null)
	{
		$dbAppToken = AppTokenPeer::retrieveByPK($id);
		if(!$dbAppToken)
			throw new KalturaAPIException(KalturaErrors::APP_TOKEN_ID_NOT_FOUND, $id);
		
		if($dbAppToken->getStatus() != AppTokenStatus::ACTIVE)
			throw new KalturaAPIException(KalturaErrors::APP_TOKEN_NOT_ACTIVE, $id);
		
		$appTokenHash = sha1(kCurrentContext::$ks . $dbAppToken->getToken());
		if($appTokenHash !== $tokenHash)
			throw new KalturaAPIException(KalturaErrors::INVALID_APP_TOKEN_HASH);
		
		KalturaResponseCacher::disableCache();
	
		$partnerId = kCurrentContext::getCurrentPartnerId();
		$partner = PartnerPeer::retrieveByPK($partnerId);
		$partnerMaxExpiry = $partner->getKsMaxExpiryInSeconds();
		
		$tokenExpiry = $partnerMaxExpiry;
		if(!is_null($dbAppToken->getExpiry()))
		{
			$tokenExpiry = $dbAppToken->getExpiry() - time();
			if($tokenExpiry < 0)
				throw new KalturaAPIException(KalturaErrors::APP_TOKEN_EXPIRED, $id);
		}
		
		if(!is_null($dbAppToken->getSessionType()))
			$type = $dbAppToken->getSessionType();
		if(is_null($type))
			$type = SessionType::USER;
			
		if(!is_null($dbAppToken->getSessionUserId()))
			$userId = $dbAppToken->getSessionUserId();
			
		if(is_null($expiry))
			$expiry = $partnerMaxExpiry;
		$expiry = max($partnerMaxExpiry, min($expiry, $tokenExpiry, $dbAppToken->getSessionDuration()));
			
		$secret = $type == SessionType::ADMIN ? $partner->getAdminSecret() : $partner->getSecret();
		
		$privilegesArray = array();
		if($privileges)
		{
			$privilegesArray = ks::parsePrivileges($privileges);
		}
		if($dbAppToken->getSessionPrivileges())
		{
			$privilegesArray = array_merge_recursive($privilegesArray, ks::parsePrivileges($dbAppToken->getSessionPrivileges()));
		}
		$privileges = ks::buildPrivileges($privilegesArray);
		
		$ks = "";
		$result = kSessionUtils::startKSession($partnerId, $secret, $userId, $ks, $expiry, $type, "", $privileges);
		
		if($result < 0)
			throw new KalturaAPIException(APIErrors::START_SESSION_ERROR, $partnerId);
			
		return $ks;
	}
}