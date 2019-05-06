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
	 * @return string
	 * @throws Exception
	 */
	public function generateSipUrlAction($entryId)
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

		$dbLiveEntry = PexipUtils::validateAndRetrieveEntry($entryId);

		$dbLiveEntry->setIsSipEnabled(true);
		$dbLiveEntry->save();

		PexipHandler::createCallObjects($dbLiveEntry, $pexipConfig);

		$sipUrl = $dbLiveEntry->getIntId() . "@" . $pexipConfig['hostUrl'];

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

		$liveEntry = PexipUtils::retrieveAndValidateEntryForSipCall($queryParams, $pexipConfig);
		if (!$liveEntry)
		{
			KalturaLog::err("Live entry for call not Validated!");
			return $response;
		}

		//	TODO - PexipHandler::validateEnoughLicenses();
		$sipEntryServerNode = EntryServerNodePeer::retrieveByEntryIdAndServerType($liveEntry->getId(), SipPlugin::getCoreValue('EntryServerNodeType', SipEntryServerNodeType::SIP_ENTRY_SERVER ));
		if (!$sipEntryServerNode)
		{
			KalturaLog::info("SipEntryServerNode not found. Please generate sip url.");
			return $response;
		}

		$response->action = "continue";
		return $response;
	}

	/**
	 * @action listRooms
	 * @param int $offset
	 * @param int $pageSize
	 * @return KalturaStringValueArray
	 * @throws KalturaAPIException
	 */
	public function listRoomsAction($offset = 0, $pageSize = 500 )
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

		return KalturaStringValueArray::fromDbArray(PexipHandler::listRooms($offset, $pageSize, $pexipConfig));
	}

}