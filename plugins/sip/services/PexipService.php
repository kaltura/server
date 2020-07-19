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
	 * @param int $sourceType
	 * @param bool $regenerate
	 * @return string
	 * @throws Exception
	 */
	public function generateSipUrlAction($entryId, $regenerate = false, $sourceType = KalturaSipSourceType::PICTURE_IN_PICTURE)
	{
		kApiCache::disableCache();
		if (!PermissionPeer::isValidForPartner(PermissionName::FEATURE_SIP, $this->getPartnerId()))
		{
			throw new KalturaAPIException (APIErrors::FEATURE_FORBIDDEN, $this->serviceId . '->' . $this->actionName);
		}

		$pexipConfig = kPexipUtils::initAndValidateConfig();

		/** @var LiveStreamEntry $dbLiveEntry */
		$dbLiveEntry = kPexipUtils::validateAndRetrieveEntry($entryId);
		if ($regenerate)
		{
			kPexipHandler::deleteCallObjects($dbLiveEntry, $pexipConfig);
		}

		$dualStreamLiveEntry = kPexipUtils::validateAndRetrieveDualStreamEntry($dbLiveEntry, $sourceType);

		$sipToken = kPexipUtils::generateSipToken($dbLiveEntry, $pexipConfig, $regenerate);
		list ($roomId, $primaryAdpId, $secondaryAdpId) = kPexipHandler::createCallObjects($dbLiveEntry, $pexipConfig, $sipToken, $dualStreamLiveEntry, $sourceType);

		$dbLiveEntry->setSipToken($sipToken);
		$dbLiveEntry->setSipRoomId($roomId);
		$dbLiveEntry->setPrimaryAdpId($primaryAdpId);
		$dbLiveEntry->setSecondaryAdpId($secondaryAdpId);
		$dbLiveEntry->setIsSipEnabled(true);
		$dbLiveEntry->setSipSourceType($sourceType);
		if($dualStreamLiveEntry)
		{
			$dbLiveEntry->setSipDualStreamEntryId($dualStreamLiveEntry->getId());
		}
		$dbLiveEntry->save();

		return $sipToken;
	}

	/**
	 * @action handleIncomingCall
	 */
	public function handleIncomingCallAction()
	{
		kApiCache::disableCache();
		$response = new KalturaSipResponse();
		$response->action = 'reject';
		$response->sessionId = UniqueId::get();
		$response->hostName = infraRequestUtils::getHostname();

		try
		{
			$pexipConfig = kPexipUtils::initAndValidateConfig();
		}
		catch(Exception $e)
		{
			KalturaLog::err($e->getMessage());
			$response->msg = $e->getMessage();
			return $response;
		}

		$queryParams = kPexipUtils::validateAndGetQueryParams();
		if(!$queryParams)
		{
			$response->msg = 'could not validate query params';
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
			$dbLiveEntry = kPexipUtils::retrieveAndValidateEntryForSipCall($queryParams, $pexipConfig);
		}
		catch(Exception $e)
		{
			$msg = 'Error validating and retrieving Entry for sip Call';
			KalturaLog::err($msg);
			$response->msg = $msg;
			return $response;
		}

		/** @var LiveStreamEntry $dbLiveEntry */
		if (!$dbLiveEntry)
		{
			$msg = 'Live entry for call not Validated!';
			KalturaLog::err($msg);
			$response->msg = $msg;
			return $response;
		}

		if(!kPexipUtils::validateLicensesAvailable($pexipConfig))
		{
			$msg = 'Max number of active rooms reached. Please try again shortly.';
			kPexipUtils::sendSipEmailNotification($dbLiveEntry->getPartnerId(), $dbLiveEntry->getPuserId(), $msg, $dbLiveEntry->getId());
			$response->msg = $msg;
			return $response;
		}

		$sipEntryServerNode = kPexipUtils::createSipEntryServerNode($dbLiveEntry, $dbLiveEntry->getSipRoomId(), $dbLiveEntry->getPrimaryAdpId(), $dbLiveEntry->getSecondaryAdpId());
		/** @var  SipEntryServerNode $sipEntryServerNode */
		if (!$sipEntryServerNode)
		{
			$msg = 'Entry ' . $dbLiveEntry->getId() . ' is Live and Active. can\'t connect call.';
			kPexipUtils::sendSipEmailNotification($dbLiveEntry->getPartnerId(), $dbLiveEntry->getPuserId(), $msg, $dbLiveEntry->getId());
			$response->msg = $msg;
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
		$pexipConfig = kPexipUtils::initAndValidateConfig();
		$res = kPexipHandler::listRooms($offset, $pageSize, $pexipConfig, $activeOnly);
		return KalturaStringValueArray::fromDbArray(array(json_encode($res)));
	}
}
