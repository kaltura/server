<?php
/**
 * @service pexip
 * @package plugins.sip
 * @subpackage api.services
 */
class PexipService extends KalturaBaseService
{
	const CALL_DIRECTION_PARAM_NAME = 'call_direction';
	const CALL_DIRECTION_DIAL_IN = 'dial_in';

	/**
	 * no partner will be provided by vendors as this called externally and not from kaltura
	 * @param string $actionName
	 * @return bool
	 */
	protected function partnerRequired($actionName)
	{
		if ($actionName === 'handleIncomingCall')
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
	public function generateSipUrlAction($entryId, $regenerate = false)
	{
		if (!PermissionPeer::isValidForPartner(PermissionName::FEATURE_SIP, $this->getPartnerId()))
		{
			throw new KalturaAPIException (APIErrors::FEATURE_FORBIDDEN, $this->serviceId . '->' . $this->actionName);
		}

		$pexipConfig = PexipUtils::initAndValidateConfig();

		/** @var LiveStreamEntry $dbLiveEntry */
		$dbLiveEntry = PexipUtils::validateAndRetrieveEntry($entryId);

		if ($regenerate)
		{
			PexipHandler::deleteCallObjects($dbLiveEntry, $pexipConfig);
		}

		$sipToken = PexipUtils::generateSipToken($dbLiveEntry, $pexipConfig, $regenerate);
		list ($roomId, $primaryAdpId, $secondaryAdpId) = PexipHandler::createCallObjects($dbLiveEntry, $pexipConfig, $sipToken);

		$dbLiveEntry->setSipToken($sipToken);
		$dbLiveEntry->setSipRoomId($roomId);
		$dbLiveEntry->setPrimaryAdpId($primaryAdpId);
		$dbLiveEntry->setSecondaryAdpId($secondaryAdpId);
		$dbLiveEntry->setIsSipEnabled(true);
		$dbLiveEntry->save();

		return $sipToken;
	}

	/**
	 * @action handleIncomingCall
	 * @return bool
	 */
	public function handleIncomingCallAction()
	{
		$response = new KalturaSipResponse();
		$response->action = 'reject';

		try
		{
			$pexipConfig = PexipUtils::initAndValidateConfig();
		}
		catch(Exception $e)
		{
			KalturaLog::err($e->getMessage());
			return $response;
		}

		$queryParams = PexipUtils::validateAndGetQueryParams();
		if(!$queryParams)
		{
			return $response;
		}

		if ($queryParams[self::CALL_DIRECTION_PARAM_NAME] != self::CALL_DIRECTION_DIAL_IN)
		{
			KalturaLog::debug(self::CALL_DIRECTION_PARAM_NAME . ' ' . $queryParams[self::CALL_DIRECTION_PARAM_NAME] .' not validated!');
			$response->action = 'done';
			return $response;
		}

		KalturaLog::debug(self::CALL_DIRECTION_PARAM_NAME . ' validated!');
		try
		{
			$dbLiveEntry = PexipUtils::retrieveAndValidateEntryForSipCall($queryParams, $pexipConfig);
		}
		catch(Exception $e)
		{
			KalturaLog::err('Error validating and retrieving Entry for sip Call');
			return $response;
		}

		/** @var LiveStreamEntry $dbLiveEntry */
		if (!$dbLiveEntry)
		{
			KalturaLog::err('Live entry for call not Validated!');
			return $response;
		}

		if(!PexipUtils::validateLicensesAvailable($pexipConfig))
		{
			return $response;
		}

		$sipEntryServerNode = PexipUtils::createSipEntryServerNode($dbLiveEntry, $dbLiveEntry->getSipRoomId(), $dbLiveEntry->getPrimaryAdpId(), $dbLiveEntry->getSecondaryAdpId());
		/** @var  SipEntryServerNode $sipEntryServerNode */
		if (!$sipEntryServerNode)
		{
			KalturaLog::debug("Could not create or retrieve SipEntryServerNode.");
			return $response;
		}

		$response->action = 'done';
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
		$res = PexipHandler::listRooms($offset, $pageSize, $pexipConfig, $activeOnly);
		return KalturaStringValueArray::fromDbArray(array(json_encode($res)));
	}

}