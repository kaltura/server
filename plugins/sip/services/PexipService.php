<?php
/**
 * @service pexip
 * @package plugins.sip
 * @subpackage api.services
 */
class PexipService extends KalturaBaseService
{
	/**
	 * no partner will be provided by vendors as this called externally and not from kaltura
	 * @param string $actionName
	 * @return bool
	 */
	protected function partnerRequired($actionName)
	{
		if ($actionName == 'handleIncomingCall')
		{
			return false;
		}
		return true;
	}

	/**
	 * @action generateSipUrl
	 * @param string $entryId
	 * @param bool $regenerate
	 * @return string
	 * @throws Exception
	 */
	public function generateSipUrlAction($entryId = '0_g6nbeccz', $regenerate = false)
	{
		if (!PermissionPeer::isValidForPartner(PermissionName::FEATURE_SIP, $this->getPartnerId()))
		{
			throw new KalturaAPIException (APIErrors::FEATURE_FORBIDDEN, $this->serviceId . '->' . $this->actionName);
		}

		$pexipConfig = PexipUtils::initAndValidateConfig();
		if (!$pexipConfig)
		{
			throw new KalturaAPIException(KalturaErrors::PEXIP_MAP_NOT_CONFIGURED);
		}

		/** @var LiveStreamEntry $dbLiveEntry */
		$dbLiveEntry = PexipUtils::validateAndRetrieveEntry($entryId);

		if ($regenerate)
		{
			PexipHandler::deleteCallObjects($dbLiveEntry, $pexipConfig);
		}

		$sipUrl = PexipUtils::createSipUrl($dbLiveEntry, $pexipConfig, $regenerate);
		PexipHandler::createCallObjects($dbLiveEntry, $pexipConfig, $sipUrl);
		return $sipUrl;
	}

	/**
	 * @action handleIncomingCall
	 * @return bool
	 */
	public function handleIncomingCall()
	{
		$response = new KalturaSipResponse();
		$response->action = "reject";

		$pexipConfig = PexipUtils::initAndValidateConfig();
		if (!$pexipConfig)
		{
			return $response;
		}

		$queryParams = PexipUtils::validateAndGetQueryParams();
		if(!$queryParams)
		{
			return $response;
		}

		if ($queryParams['call_direction'] != 'dial_in')
		{
			KalturaLog::debug("call_direction " . $queryParams['call_direction'] ." not validated!");
			$response->action = "done";
			return $response;
		}

		KalturaLog::debug("call_direction validated!");
		try
		{
			$dbLiveEntry = PexipUtils::retrieveAndValidateEntryForSipCall($queryParams, $pexipConfig);
		}
		catch(Exception $e)
		{
			KalturaLog::err("Error validating and retrieving Entry for sip Call");
			return $response;
		}
		/** @var LiveEntry $dbLiveEntry */
		if (!$dbLiveEntry)
		{
			KalturaLog::err("Live entry for call not Validated!");
			return $response;
		}
		//	TODO - PexipHandler::validateEnoughLicenses();

		$sipEntryServerNode = PexipUtils::createSipEntryServerNode($dbLiveEntry, $dbLiveEntry->getSipRoomId(), $dbLiveEntry->getPrimaryAdpId(), $dbLiveEntry->getSecondaryAdpId());
		/** @var  SipEntryServerNode $sipEntryServerNode */
		if (!$sipEntryServerNode)
		{
			KalturaLog::info("Could not create or retrieve SipEntryServerNode.");
			return $response;
		}

		$response->action = "done";
		return $response;
	}

	/**
	 * @action listRooms
	 * @param int $offset
	 * @param int $pageSize
	 * @param bool $activeOnly
	 * @return KalturaStringValueArray
	 * @throws KalturaAPIException
	 */
	public function listRoomsAction($offset = 0, $pageSize = 500, $activeOnly = false )
	{
		if (!PermissionPeer::isValidForPartner(PermissionName::FEATURE_SIP, $this->getPartnerId()))
		{
			throw new KalturaAPIException (APIErrors::FEATURE_FORBIDDEN, $this->serviceId . '->' . $this->actionName);
		}
		$pexipConfig = PexipUtils::initAndValidateConfig();
		if (!$pexipConfig)
		{
			throw new KalturaAPIException(KalturaErrors::PEXIP_MAP_NOT_CONFIGURED);
		}

		return KalturaStringValueArray::fromDbArray(PexipHandler::listRooms($offset, $pageSize, $pexipConfig, $activeOnly));
	}

}