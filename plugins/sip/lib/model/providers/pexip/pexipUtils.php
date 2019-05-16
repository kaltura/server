<?php
/**
 * @package plugins.sip
 * @subpackage model.pexip
 */

class PexipUtils
{
	const CONFIG_LICENSE_THRESHOLD = 'licenseThreshold';
	const CONFIG_HOST_URL = 'hostUrl';
	const CONFIG_API_ADDRESS = 'apiAddress';
	const CONFIG_USER_NAME = 'userName';
	const CONFIG_PASSWORD = 'password';
	const SIP_URL_DELIMITER = '@';
	const PARAM_META = 'meta';
	const PARAM_TOTAL_COUNT = 'total_count';
	const PARAM_LOCAL_ALIAS = 'local_alias';
	const LICENSES_PER_CALL = 3;
	/**
	 * @return bool|null
	 * @throws Exception
	 * @throws KalturaAPIException
	 */
	public static function initAndValidateConfig()
	{
		if (kConf::hasMap('sip') && $pexipConfig = kConf::get('pexip', 'sip'))
		{
			return $pexipConfig;
		}

		throw new KalturaAPIException(KalturaErrors::PEXIP_MAP_NOT_CONFIGURED);
	}

	/**
	 * @param LiveStreamEntry $dbLiveEntry
	 * @param bool $regenerate
	 * @return string
	 */
	public static function generateSipToken(LiveStreamEntry $dbLiveEntry, $regenerate = false)
	{
		if (!$dbLiveEntry->getSipToken() || $regenerate)
		{
			$addition = str_pad(substr((string)microtime(true) * 10000, -5), 5, '0', STR_PAD_LEFT);
			return $dbLiveEntry->getPartnerId() . $addition;
		}
		return $dbLiveEntry->getSipToken();
	}

	/**
	 * @param $entry
	 * @param $pexipConfig
	 * @return string
	 */
	public static function getRoomName(LiveEntry $entry, $pexipConfig)
	{
		return $entry->getId() . '@' . $pexipConfig[self::CONFIG_HOST_URL];
	}

	/**
	 * @param $entryId
	 * @return entry
	 * @throws KalturaAPIException
	 */
	public static function validateAndRetrieveEntry($entryId)
	{
		$dbEntry = entryPeer::retrieveByPK($entryId);
		if (!$dbEntry)
		{
			throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND, $entryId);
		}

		if ($dbEntry->getType() != entryType::LIVE_STREAM)
		{
			throw new KalturaAPIException(KalturaErrors::INVALID_ENTRY_TYPE, $dbEntry->getName(), $dbEntry->getType(), entryType::LIVE_STREAM);
		}

		return $dbEntry;
	}

	/**
	 * @param $queryParams
	 * @param $pexipConfig
	 * @return bool|entry
	 */
	public static function retrieveAndValidateEntryForSipCall($queryParams, $pexipConfig)
	{
		list($partnerId, $sipToken ) = self::extractPartnerIdAndSipTokenFromAddress($queryParams, $pexipConfig);
		if (!$partnerId || !$sipToken )
		{
			return false;
		}

		myPartnerUtils::resetAllFilters();
		$c = KalturaCriteria::create(entryPeer::OM_CLASS);
		$sipFilter = new kSipAdvancedFilter();
		$sipFilter->setSipToken($sipToken);
		$entryFilter = new entryFilter();
		$entryFilter->setAdvancedSearch($sipFilter);
		$entryFilter->setPartnerSearchScope($partnerId);
		$c->attachFilter($entryFilter);
		$dbLiveEntry = entryPeer::doSelectOne($c);

		if (!$dbLiveEntry)
		{
			KalturaLog::err("Entry was not found for int_id $sipToken");
			return false;
		}

		if (!PermissionPeer::isValidForPartner(PermissionName::FEATURE_SIP, $dbLiveEntry->getPartnerId()))
		{
			KalturaLog::err('Sip Feature is not enabled for partner ' . $dbLiveEntry->getPartnerId());
			return false;
		}

		if (!$dbLiveEntry instanceof LiveStreamEntry)
		{
			KalturaLog::err('Entry ' . $dbLiveEntry->getId() . ' is not of type LiveStreamEntry.');
			return false;
		}

		if (!$dbLiveEntry->getIsSipEnabled())
		{
			KalturaLog::err('Sip flag is not enabled for entry ' . $dbLiveEntry->getId() . ' - generateSipUrl action should be called before connecting to entry');
			return false;
		}

		if ($dbLiveEntry->isCurrentlyLive(false))
		{
			KalturaLog::err('Entry Is currently Live. will not allow call.');
			return false;
		}

		if (!$dbLiveEntry->getSipRoomId())
		{
			KalturaLog::err('Missing Sip Room Id - generateSipUrl action should be called before connecting to entry');
			return false;
		}

		return $dbLiveEntry;
	}

	/**
	 * @param $queryParams
	 * @return array
	 */
	protected static function extractPartnerIdAndSipTokenFromAddress($queryParams)
	{
		KalturaLog::debug('Extracting entry sip token from local_alias: ' . $queryParams[self::PARAM_LOCAL_ALIAS]);
		$intIdPattern = '/(?<=sip:)(.*)/';
		preg_match($intIdPattern, $queryParams[self::PARAM_LOCAL_ALIAS], $matches);
		if (!empty($matches))
		{
			$parts = explode(self::SIP_URL_DELIMITER, $matches[0]);
			if (!empty($parts))
			{
				$partnerId = substr($parts[0], 0, -5);
				KalturaLog::debug("Extracted partnerId and sipToken : [$partnerId ,$matches[0]]");
				return array($partnerId, $matches[0]);
			}
		}
		KalturaLog::debug('Could not extract PartnerId and SipToken from local_alias');
		return array();

	}

	/**
	 * @param LiveEntry $entry
	 * @param $roomId
	 * @param $primaryAdpId
	 * @param $secondaryAdpId
	 * @return bool|EntryServerNode|mixed
	 */
	public static function createSipEntryServerNode(LiveEntry $entry, $roomId, $primaryAdpId, $secondaryAdpId)
	{
		$connectedEntryServerNodes = EntryServerNodePeer::retrieveByEntryIdAndStatuses($entry->getId(), EntryServerNodePeer::$connectedServerNodeStatuses);
		if (count($connectedEntryServerNodes))
		{
			KalturaLog::info('Entry [' . $entry->getId() . '] is Live and Active. can\'t create SipEntryServerNode.');
			return false;
		}

		$sipEntryServerNode = EntryServerNodePeer::retrieveByEntryIdAndServerType($entry->getId(), SipPlugin::getCoreValue('EntryServerNodeType', SipEntryServerNodeType::SIP_ENTRY_SERVER));
		if ($sipEntryServerNode)
		{
			KalturaLog::debug('SipEntryServerNode already created for entry '. $entry->getId() );
			return $sipEntryServerNode;
		}

		$lockKey = 'allocate_sip_room_' . $entry->getId();
		$sipEntryServerNode = kLock::runLocked($lockKey, array('PexipUtils', 'createSipEntryServerNodeImpl'), array($entry, $roomId, $primaryAdpId, $secondaryAdpId));
		return $sipEntryServerNode;

	}

	/**
	 * @param $entry
	 * @param $roomId
	 * @param $primaryAdpId
	 * @param $secondaryAdpId
	 * @return EntryServerNode|SipEntryServerNode
	 * @throws PropelException
	 */
	public static function createSipEntryServerNodeImpl($entry, $roomId, $primaryAdpId, $secondaryAdpId)
	{
		//In case until this method is run under lock another process already created the sipEntryServerNode.
		$sipEntryServerNode = EntryServerNodePeer::retrieveByEntryIdAndServerType($entry->getId(), SipPlugin::getCoreValue('EntryServerNodeType', SipEntryServerNodeType::SIP_ENTRY_SERVER));
		if ($sipEntryServerNode)
		{
			KalturaLog::debug('SipEntryServerNode ' . $sipEntryServerNode->getId() . " already created for entry $entry->getId() ");
			return $sipEntryServerNode;
		}

		$serverNode = ServerNodePeer::retrieveActiveServerNode(null, null, SipPlugin::getCoreValue('serverNodeType', SipServerNodeType::SIP_SERVER));
		$sipEntryServerNode = new SipEntryServerNode();
		$sipEntryServerNode->setEntryId($entry->getId());
		$sipEntryServerNode->setServerNodeId($serverNode->getId());
		$sipEntryServerNode->setServerType(SipPlugin::getCoreValue('EntryServerNodeType', SipEntryServerNodeType::SIP_ENTRY_SERVER));
		$sipEntryServerNode->setSipRoomStatus(SipEntryServerNodeStatus::CREATED);
		$sipEntryServerNode->setPartnerId($entry->getPartnerId());
		$sipEntryServerNode->setSipRoomId($roomId);
		$sipEntryServerNode->setSipRoomPrimaryADP($primaryAdpId);
		$sipEntryServerNode->setSipRoomSecondaryADP($secondaryAdpId);
		$sipEntryServerNode->save();

		return $sipEntryServerNode;
	}

	/**
	 * @return array|bool
	 */
	public static function validateAndGetQueryParams()
	{
		$queryParams = array();
		parse_str($_SERVER['QUERY_STRING'], $queryParams);

		KalturaLog::debug('Retrieved qurey params :' . print_r($queryParams, true));
		if (!isset($queryParams[self::PARAM_LOCAL_ALIAS]))
		{
			KalturaLog::debug('Missing ' . self::PARAM_LOCAL_ALIAS . ' param');
			return false;
		}
		// TODO - validate origin call came from pexip server
		return $queryParams;
	}

	/**
	 * @param $pexipConfig
	 * @return bool
	 */
	public static function validateLicensesAvailable($pexipConfig)
	{
		$result = PexipHandler::listRooms(0, 1, $pexipConfig, true);
		if (empty($result))
		{
			KalturaLog::debug('Could Not retrieve active rooms - available licenes not validated!');
			return false;
		}
		if ( ( $result[self::PARAM_META][self::PARAM_TOTAL_COUNT] * self::LICENSES_PER_CALL ) >= $pexipConfig[self::CONFIG_LICENSE_THRESHOLD])
		{
			KalturaLog::debug('Max number of active rooms reached - active rooms count is ' . $result[self::PARAM_META][self::PARAM_TOTAL_COUNT] . '- available licenes not validated!');
			return false;
		}

		return true;
	}

	/**
	 * @param KCurlWrapper $curlWrapper
	 * @param $url
	 */
	public static function logError(KCurlWrapper $curlWrapper, $url)
	{
		KalturaLog::info('Sending HTTP request failed ['. $curlWrapper->getErrorNumber() . '] httpCode ['.$curlWrapper->getHttpCode()."] url [$url]: ".$curlWrapper->getError());
	}

	/**
	 * @param $result
	 * @return mixed
	 */
	public static function extractObjectFromdResult($result)
	{
		$resObj = json_decode($result, true);
		if (!empty($resObj['objects']) && isset($resObj['objects'][0]))
		{
			KalturaLog::info('Retrieved Object ' . print_r($resObj['objects'][0],true));
			return $resObj['objects'][0];
		}
		return null;
	}

	/**
	 * @param $result
	 * @param $url
	 * @param $headerSize
	 * @return null
	 */
	public static function extractIdFromCreatedResult($result,$url ,$headerSize)
	{
		$header = substr($result, 0, $headerSize);
		$headerData = explode('\n', $header);
		KalturaLog::info('Checking Headers ' . print_r($headerData, true));
		$locationPattern = "(?<=Location: $url)(.*)(?=/)";
		$locationPattern = str_replace('/', '\/', $locationPattern);
		foreach ($headerData as $part)
		{
			preg_match("/$locationPattern/", $part, $matches);
			if (!empty($matches))
			{
				$virtualRoomId = $matches[0];
				KalturaLog::info("Pexip created ID: $virtualRoomId");
				return $virtualRoomId;
			}
		}
		KalturaLog::info('Could not extract ID from headers');
		return null;
	}
}