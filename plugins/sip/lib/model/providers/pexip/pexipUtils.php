<?php
/**
 * @package plugins.sip
 * @subpackage model.pexip
 */

class PexipUtils
{
	/**
	 * @return bool
	 * @throws Exception
	 * @throws kCoreException
	 */
	public static function initAndValidateConfig()
	{
		if (kConf::hasMap('sip') && $pexipConfig = kConf::get('pexip', 'sip'))
		{
			return $pexipConfig;
		}

		KalturaLog::debug("Pexip misconfigured. please validate configuration.");
		return false;
	}

	/**
	 * @param $dbLiveEntry
	 * @param $pexipConfig
	 * @param $regenerate
	 * @return string
	 */
	public static function createSipUrl(LiveStreamEntry $dbLiveEntry, $pexipConfig, $regenerate = false)
	{
		if (!$dbLiveEntry->getSipToken() || $regenerate)
		{
			$addition = str_pad(substr((string)microtime(true)*10000, -5),5,'0',STR_PAD_LEFT);
			$sipToken = $dbLiveEntry->getPartnerId() . $addition;
			$dbLiveEntry->setSipToken($sipToken);
			$dbLiveEntry->save();
		}
		$sipUrl = $dbLiveEntry->getSipToken() . "@" . $pexipConfig['hostUrl'];
		return $sipUrl;
	}
	/**
	 * @param $entry
	 * @param $pexipConfig
	 * @return string
	 */
	public static function getRoomName(LiveEntry $entry, $pexipConfig)
	{
		return $entry->getId() . "@" . $pexipConfig['hostUrl'];
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
		$sipToken = self::extractSipTokenFromAddress($queryParams, $pexipConfig);
		if (!$sipToken)
		{
			return false;
		}

		$partnerId = substr($sipToken, 0, -5);
		kCurrentContext::$partner_id = $partnerId;
		entryPeer::setUseCriteriaFilter ( false );
		$c = KalturaCriteria::create(entryPeer::OM_CLASS);
		$criterion = $c->getNewCriterion(entryPeer::PARTNER_ID, $partnerId);
		$c->addAnd($criterion);
		$c->addAnd($c->getNewCriterion(entryPeer::SIP_TOKEN, $sipToken,KalturaCriteria::EQUAL));
		$dbLiveEntry = entryPeer::doSelectOne($c);
		entryPeer::setUseCriteriaFilter ( true );
		if (!$dbLiveEntry)
		{
			KalturaLog::err("Entry was not found for int_id $sipToken");
			return false;
		}

		if (!PermissionPeer::isValidForPartner(PermissionName::FEATURE_SIP, $dbLiveEntry->getPartnerId()))
		{
			KalturaLog::err("Sip Feature is not enabled for partner " . $dbLiveEntry->getPartnerId());
			return false;
		}

		if (!$dbLiveEntry instanceof LiveStreamEntry)
		{
			KalturaLog::err("Entry " . $dbLiveEntry->getId() . " is not of type LiveStreamEntry.");
			return false;
		}

		if (!$dbLiveEntry->getIsSipEnabled())
		{
			KalturaLog::err("Sip flag is not enabled for entry " . $dbLiveEntry->getId() . " - generateSipUrl action should be called before connecting to entry");
			return false;
		}

		if ($dbLiveEntry->isCurrentlyLive(false))
		{
			KalturaLog::err("Entry Is currently Live. will not allow call.");
			return false;
		}

		if (!$dbLiveEntry->getSipRoomId())
		{
			KalturaLog::err("Missing Sip Room Id - generateSipUrl action should be called before connecting to entry");
			return false;
		}

		if (!$dbLiveEntry->getPrimaryAdpId())
		{
			KalturaLog::err("Missing Primary Adp Id - generateSipUrl action should be called before connecting to entry");
			return false;
		}

		if (!$dbLiveEntry->getSecondaryAdpId())
		{
			KalturaLog::err("Missing Secondary Adp Id - generateSipUrl action should be called before connecting to entry");
			return false;
		}

		return $dbLiveEntry;
	}

	/**
	 * @param null $pexipConfig
	 * @return bool
	 */
	protected static function extractSipTokenFromAddress($queryParams, $pexipConfig)
	{
		KalturaLog::debug("Extracting entry sip token from local_alias: " . $queryParams['local_alias']);
		$intIdPattern = '/(?<=sip:)(.*)(?=@' . $pexipConfig['hostUrl'] . ')/';
		preg_match($intIdPattern, $queryParams['local_alias'], $matches);
		if (empty($matches))
		{
			KalturaLog::debug("Could Not extract entry int_id from local_alias");
			return false;
		}
		KalturaLog::debug("Entry sip token extracted : $matches[0]");
		return $matches[0];
	}

	/**
	 * @param $entryId
	 * @return EntryServerNode
	 */
	public static function createSipEntryServerNode(LiveEntry $entry, $roomId, $primaryAdpId, $secondaryAdpId)
	{
		$connectedEntryServerNodes = EntryServerNodePeer::retrieveByEntryIdAndStatuses($entry->getId(), EntryServerNodePeer::$connectedServerNodeStatuses);
		if (count($connectedEntryServerNodes))
		{
			KalturaLog::info("Entry [" . $entry->getId() . "] is Live and Active. can't create SipEntryServerNode.");
			return false;
		}

		$sipEntryServerNode = EntryServerNodePeer::retrieveByEntryIdAndServerType($entry->getId(), SipPlugin::getCoreValue('EntryServerNodeType', SipEntryServerNodeType::SIP_ENTRY_SERVER));
		if ($sipEntryServerNode)
		{
			KalturaLog::debug("SipEntryServerNode already created for entry ". $entry->getId() );
			return $sipEntryServerNode;
		}

		$lockKey = "allocate_sip_room_" . $entry->getId();
		$sipEntryServerNode = kLock::runLocked($lockKey, array('PexipUtils', 'createSipEntryServerNodeImpl'), array($entry, $roomId, $primaryAdpId, $secondaryAdpId));
		return $sipEntryServerNode;

	}

	/**
	 * @param $entry
	 * @return bool|SipEntryServerNode
	 * @throws PropelException
	 */
	public static function createSipEntryServerNodeImpl($entry, $roomId, $primaryAdpId, $secondaryAdpId)
	{
		//In case until this method is run under lock another process already created the sipEntryServerNode.
		$sipEntryServerNode = EntryServerNodePeer::retrieveByEntryIdAndServerType($entry->getId(), SipPlugin::getCoreValue('EntryServerNodeType', SipEntryServerNodeType::SIP_ENTRY_SERVER));
		if ($sipEntryServerNode)
		{
			KalturaLog::debug("SipEntryServerNode " . $sipEntryServerNode->getId() . " already created for entry $entry->getId() ");
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

		KalturaLog::debug("Retrieved qurey params :" . print_r($queryParams, true));
		if (!isset($queryParams['local_alias']))
		{
			KalturaLog::debug("Missing local_alias param");
			return false;
		}
		// TODO - validate origin call came from pexip server
		return $queryParams;
	}

	/**
	 * @param KCurlWrapper $curlWrapper
	 * @param $url
	 */
	public static function logError(KCurlWrapper $curlWrapper, $url)
	{
		KalturaLog::info("Sending HTTP request failed [". $curlWrapper->getErrorNumber() . "] httpCode [".$curlWrapper->getHttpCode()."] url [$url]: ".$curlWrapper->getError());
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
			KalturaLog::info("Retrieved Object " . print_r($resObj['objects'][0],true));
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
		$headerData = explode("\n", $header);
		KalturaLog::info("Checking Headers " . print_r($headerData, true));
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
		KalturaLog::info("Could not extract ID from headers");
		return null;
	}
}