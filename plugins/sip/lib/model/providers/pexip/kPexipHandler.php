<?php
/**
 * @package plugins.sip
 * @subpackage model.pexip
 */

class kPexipHandler
{
	const ROOM_PREFIX = '/api/admin/configuration/v1/conference/';
	const LOCATION_PREFIX = '/api/admin/configuration/v1/system_location/';
	const ADP_PREFIX = '/api/admin/configuration/v1/automatic_participant/';
	const ACTIVE_ROOM_PREFIX = '/api/admin/status/v1/conference/';
	const ROOM_NAME_KEY = 'roomName';
	const ROOM_ID_KEY = 'id';
	const ADP_ALIAS_KEY = 'alias';
	const ADP_ID_KEY = 'id';

	const ALREADY_EXISTS_PATTERN = '/already exists/';

	/**
	 * @param LiveStreamEntry $dbLiveEntry
	 * @param $pexipConfig
	 * @param $alias
	 * @return array
	 * @throws KalturaAPIException
	 */
	public static function createCallObjects(LiveStreamEntry $dbLiveEntry, $pexipConfig, $alias)
	{
		$roomId = self::addVirtualRoom($dbLiveEntry, $pexipConfig, $alias);
		if (!$roomId)
		{
			throw new KalturaAPIException(KalturaErrors::PEXIP_ROOM_CREATION_FAILED, $dbLiveEntry->getId());
		}
		$primaryAdpId = null;
		$primaryUrl = self::getStreamUrl($dbLiveEntry, $pexipConfig);
		if ($primaryUrl)
		{
			$locationId = $pexipConfig[kPexipUtils::CONFIG_PRIMARY_LOCATION_ID];
			$locationId = is_numeric($locationId) ? $locationId : null;
			$primaryAdpId = self::addADP($dbLiveEntry, $roomId, $primaryUrl, 'Primary', $pexipConfig, $locationId);
			if (!$primaryAdpId)
			{
				throw new KalturaAPIException(KalturaErrors::PEXIP_ADP_CREATION_FAILED, $dbLiveEntry->getId());
			}
		}
		$secondaryAdpId = null;
		$secondaryUrl = self::getStreamUrl($dbLiveEntry, $pexipConfig, false);
		if ($secondaryUrl)
		{
			$locationId = $pexipConfig[kPexipUtils::CONFIG_SECONDARY_LOCATION_ID];
			$locationId = is_numeric($locationId) ? $locationId : null;
			$secondaryAdpId = self::addADP($dbLiveEntry, $roomId, $secondaryUrl, 'Secondary', $pexipConfig, $locationId);

			if (!$secondaryAdpId)
			{
				throw new KalturaAPIException(KalturaErrors::PEXIP_ADP_CREATION_FAILED, $dbLiveEntry->getId());
			}
		}

		return array($roomId, $primaryAdpId, $secondaryAdpId);
	}

	/**
	 * @param $dbLiveEntry
	 * @param $pexipConfig
	 * @param bool $isPrimaryStream
	 * @return string|string[]|null
	 * @throws Exception
	 */
	protected static function getStreamUrl($dbLiveEntry, $pexipConfig, $isPrimaryStream = true)
	{
		/** @var LiveStreamEntry $dbLiveEntry **/
		$streamUrl = null;
		if (isset($pexipConfig[kPexipUtils::FORCE_NON_SECURE_STREAMING]) && $pexipConfig[kPexipUtils::FORCE_NON_SECURE_STREAMING])
		{
			KalturaLog::info('Retrieving RTMP Stream Url For Entry ' . $dbLiveEntry->getId());
			$streamUrl = $isPrimaryStream ? $dbLiveEntry->getPrimaryBroadcastingUrl() : $dbLiveEntry->getSecondaryBroadcastingUrl();
		}
		else
		{
			KalturaLog::info('Retrieving RTMPS Stream Url For Entry ' . $dbLiveEntry->getId());
			$streamUrl = $isPrimaryStream ? $dbLiveEntry->getPrimarySecuredBroadcastingUrl() : $dbLiveEntry->getSecondarySecuredBroadcastingUrl();
		}

		if (!$streamUrl)
		{
			KalturaLog::info('RTMP/S stream could not be created for entry ' . $dbLiveEntry->getId());
			$msg = 'There was an issue generating a link for the broadcast for entry ' . $dbLiveEntry->getId() . ', please contact you system Admin';
			kPexipUtils::sendSipEmailNotification($dbLiveEntry->getPartnerId(), $dbLiveEntry->getPuserId(), $msg, $dbLiveEntry->getId());
			return null;
		}
		$streamUrl = $streamUrl . '/' . $dbLiveEntry->getStreamName();
		$streamUrl = str_replace("%i", "1", $streamUrl);
		return $streamUrl;
	}

	/**
	 * @param LiveStreamEntry $entry
	 * @param $pexipConfig
	 * @param $alias
	 * @return mixed|null
	 */
	protected static function addVirtualRoom(LiveStreamEntry $entry, $pexipConfig, $alias)
	{
		$roomName = kPexipUtils::getRoomName($entry, $pexipConfig);
		$url = $pexipConfig[kPexipUtils::CONFIG_API_ADDRESS] . self::ROOM_PREFIX;
		KalturaLog::info("Creating Virtual Room with name $roomName for entry " . $entry->getId());
		$data = array(
			'name' => $roomName,
			'service_type' => 'conference',
			'aliases' => array(array('alias' => $alias)),
			'participant_limit' => 3
		);
		$curlWrapper = self::initPexipCurlWrapper(HttpMethods::POST, $pexipConfig, $data);

		$result = $curlWrapper->doExec($url);
		KalturaLog::info('Result is ' . print_r($result, true));
		$virtualRoomId = null;
		if($curlWrapper->getHttpCode() == KCurlHeaderResponse::HTTP_STATUS_CREATED)
		{
			$virtualRoomId = kPexipUtils::extractIdFromCreatedResult($result, $url, $curlWrapper->getInfo(CURLINFO_HEADER_SIZE));
		}
		else
		{
			preg_match(self::ALREADY_EXISTS_PATTERN, $result, $alreadyExistsMatch);
			if(!empty($alreadyExistsMatch))
			{
				KalturaLog::info('Virtual Room for entry ' . $entry->getId() . ' already exists.');
				$virtualRoom = self::getVirtualRoom($pexipConfig, self::ROOM_NAME_KEY, $roomName);
				if($virtualRoom && isset($virtualRoom[self::ROOM_ID_KEY]))
				{
					if(!self::shouldUpdateRoomAlias($virtualRoom, $alias) || self::updateRoomAlias($virtualRoom[self::ROOM_ID_KEY], $pexipConfig, $alias))
					{
						$virtualRoomId = $virtualRoom[self::ROOM_ID_KEY];
					}
				}
			}
			else
			{
				kPexipUtils::logError($curlWrapper, $url);
			}
		}
		$curlWrapper->close();
		return $virtualRoomId;
	}


	/**
	 * @param $pexipConfig
	 * @param null $key
	 * @param null $value
	 * @return mixed|null
	 */
	public static function getVirtualRoom($pexipConfig, $key = null, $value = null)
	{
		$virtualRoom = null;
		switch ($key)
		{
			case self::ROOM_NAME_KEY:
				$url = $pexipConfig[kPexipUtils::CONFIG_API_ADDRESS] . self::ROOM_PREFIX . '?name=' . $value;
				break;
			case self::ROOM_ID_KEY:
				$url = $pexipConfig[kPexipUtils::CONFIG_API_ADDRESS] . self::ROOM_PREFIX . "$value/";
				break;
			default:
				return $virtualRoom;
		}

		KalturaLog::info("Getting Virtual Room info for $key $value");

		$curlWrapper = self::initPexipCurlWrapper(HttpMethods::GET, $pexipConfig);
		$result = $curlWrapper->exec($url);

		KalturaLog::info('Result From Pexip Server : ' . print_r($result, true));
		if($result && $curlWrapper->getHttpCode() == KCurlHeaderResponse::HTTP_STATUS_OK)
		{
			$virtualRoom = kPexipUtils::extractObjectFromdResult($result);
			if(!$virtualRoom)
			{
				KalturaLog::info("Didn't find Virutal-Room matching to $key $value");
			}
		}
		else
		{
			kPexipUtils::logError($curlWrapper, $url);
		}
		$curlWrapper->close();
		return $virtualRoom;
	}

	/**
	 * @param $pexipConfig
	 * @param null $key
	 * @param null $value
	 * @return mixed|null
	 */
	protected static function getADP($pexipConfig, $key = null, $value = null)
	{
		$adp = null;
		switch ($key)
		{
			case self::ADP_ALIAS_KEY:
				$url = $pexipConfig[kPexipUtils::CONFIG_API_ADDRESS] . self::ADP_PREFIX . '?alias=' . urlencode($value);
				break;
			case self::ADP_ID_KEY:
				$url = $pexipConfig[kPexipUtils::CONFIG_API_ADDRESS] . self::ADP_PREFIX . "$value/";
				break;
			default:
				return $adp;
		}

		KalturaLog::info("Getting ADP info for $key $value");
		$curlWrapper = self::initPexipCurlWrapper(HttpMethods::GET, $pexipConfig);
		$result = $curlWrapper->exec($url);

		KalturaLog::info('Result From Pexip Server : ' . print_r($result, true));
		if($result && $curlWrapper->getHttpCode() == KCurlHeaderResponse::HTTP_STATUS_OK)
		{
			$adp = kPexipUtils::extractObjectFromdResult($result);
			if(!$adp)
			{
				KalturaLog::info("Didn't find ADP matching to $key $value");
			}
		}
		else
		{
			kPexipUtils::logError($curlWrapper, $url);
		}
		$curlWrapper->close();
		return $adp;
	}

	/**
	 * @param $adp
	 * @param $roomId
	 * @return bool
	 */
	protected static function shouldUpdateAdp($adp, $roomId)
	{
		$roomAddress = self::ROOM_PREFIX . "$roomId/";
		if(isset($adp['conference']))
		{
			foreach ($adp['conference'] as $connectedRoom)
			{
				if($connectedRoom == $roomAddress)
					return false;
			}
		}
		return true;
	}

	/**
	 * @param $room
	 * @param $alias
	 * @return bool
	 */
	protected static function shouldUpdateRoomAlias($room, $alias)
	{
		if(isset($room['aliases']))
		{
			foreach ($room['aliases'] as $aliasItem)
			{
				if($aliasItem['alias'] === $alias)
				{
					return false;
				}
			}
		}
		return true;
	}


	/**
	 * @param LiveStreamEntry $entry
	 * @param $roomId
	 * @param $participantAddress
	 * @param $name
	 * @param $pexipConfig
	 * @param $locationId
	 * @return null
	 */
	protected static function addADP(LiveStreamEntry $entry, $roomId, $participantAddress, $name, $pexipConfig, $locationId = null)
	{
		$adpId = null;
		KalturaLog::info("Creating RTMP/S-ADP $name to Virtual Room $roomId");

		$adpData = array(
			'alias' => $participantAddress,
			'remote_display_name' => $entry->getId() . "_{$name}_ADP",
			'description' => "ADP for $name " . $entry->getId(),
			'protocol' => 'rtmp',
			'role' => 'guest',
			'conference' => array($pexipConfig[kPexipUtils::CONFIG_API_ADDRESS] . self::ROOM_PREFIX . "$roomId/"),
			'streaming' => 1,
			'keep_conference_alive' => 'keep_conference_alive_never'
		);
		if ($locationId)
		{
			$adpData["system_location"] = $pexipConfig[kPexipUtils::CONFIG_API_ADDRESS] . self::LOCATION_PREFIX . "$locationId/";
		}
		$url = $pexipConfig[kPexipUtils::CONFIG_API_ADDRESS] . self::ADP_PREFIX;
		$curlWrapper = self::initPexipCurlWrapper(HttpMethods::POST, $pexipConfig, $adpData);
		$result = $curlWrapper->doExec($url);
		KalturaLog::info('Result for ADP creation is ' . print_r($result, true));

		if($curlWrapper->getHttpCode() == KCurlHeaderResponse::HTTP_STATUS_CREATED)
		{
			$adpId = kPexipUtils::extractIdFromCreatedResult($result, $url, $curlWrapper->getInfo(CURLINFO_HEADER_SIZE));
		}
		else
		{
			preg_match(self::ALREADY_EXISTS_PATTERN, $result, $alreadyExistsMatch);
			if(!empty($alreadyExistsMatch))
			{
				KalturaLog::info('ADP for alias ' . $participantAddress . ' already exists.');
				$adp = self::getADP($pexipConfig, self::ADP_ALIAS_KEY, $participantAddress);
				if($adp && isset($adp[self::ADP_ID_KEY]))
				{
					if(!self::shouldUpdateAdp($adp, $roomId) || self::updateADP($adp[self::ADP_ID_KEY], $roomId, $pexipConfig))
					{
						$adpId = $adp[self::ADP_ID_KEY];
					}
				}
			}
			else
			{
				kPexipUtils::logError($curlWrapper, $url);
			}
		}
		$curlWrapper->close();
		return $adpId;
	}

	/**
	 * @param $adpIp
	 * @param $roomId
	 * @param $pexipConfig
	 * @return bool
	 */
	protected static function updateADP($adpIp, $roomId, $pexipConfig)
	{
		KalturaLog::info("Updating ADP $adpIp adding Virtual Room $roomId");
		$result = true;
		$adpData = array(
			'conference' => array($pexipConfig[kPexipUtils::CONFIG_API_ADDRESS] . self::ROOM_PREFIX . "$roomId/"),
		);
		$url = $pexipConfig[kPexipUtils::CONFIG_API_ADDRESS] . self::ADP_PREFIX . "$adpIp/";

		$curlWrapper = self::initPexipCurlWrapper(HttpMethods::UPDATE, $pexipConfig, $adpData);
		$execResult = $curlWrapper->doExec($url);
		KalturaLog::info('Result FOR ADP update is ' . print_r($execResult, true));

		if($curlWrapper->getHttpCode() != KCurlHeaderResponse::HTTP_STATUS_ACCEPTED)
		{
			kPexipUtils::logError($curlWrapper, $url);
			$result = false;
		}
		$curlWrapper->close();
		return $result;
	}

	/**
	 * @param $roomId
	 * @param $pexipConfig
	 * @param $alias
	 * @return bool
	 */
	protected static function updateRoomAlias($roomId, $pexipConfig, $alias)
	{
		KalturaLog::info("Updating Room $roomId");
		$result = true;
		$roomData = array(
			'aliases' => array(array('alias' => $alias))
		);
		$url = $pexipConfig[kPexipUtils::CONFIG_API_ADDRESS] . self::ROOM_PREFIX . "$roomId/";

		$curlWrapper = self::initPexipCurlWrapper(HttpMethods::UPDATE, $pexipConfig, $roomData);
		$execResult = $curlWrapper->doExec($url);
		KalturaLog::info('Result for ROOM update is' . print_r($execResult, true));

		if($curlWrapper->getHttpCode() != KCurlHeaderResponse::HTTP_STATUS_ACCEPTED)
		{
			kPexipUtils::logError($curlWrapper, $url);
			$result = false;
		}
		$curlWrapper->close();
		return $result;
	}

	/**
	 * @param LiveStreamEntry $liveEntry
	 * @param $pexipConfig
	 * @throws PropelException
	 */
	public static function deleteCallObjects(LiveStreamEntry $liveEntry, $pexipConfig)
	{
		if($liveEntry->getSipRoomId())
		{
			KalturaLog::info('Deleting Virtual room with id ' . $liveEntry->getSipRoomId());
			self::deleteItem($liveEntry->getSipRoomId(), self::ROOM_PREFIX, $pexipConfig);
		}
		if($liveEntry->getPrimaryAdpId())
		{
			KalturaLog::info('Deleting Primary ADP with id ' . $liveEntry->getPrimaryAdpId());
			self::deleteItem($liveEntry->getPrimaryAdpId(), self::ADP_PREFIX, $pexipConfig);
		}
		if($liveEntry->getSecondaryAdpId())
		{
			KalturaLog::info('Deleting Secondary ADP with id ' . $liveEntry->getSecondaryAdpId());
			self::deleteItem($liveEntry->getSecondaryAdpId(), self::ADP_PREFIX, $pexipConfig);
		}

		$sipEntryServerNode = EntryServerNodePeer::retrieveByEntryIdAndServerType($liveEntry->getEntryId(), SipPlugin::getCoreValue('EntryServerNodeType', SipEntryServerNodeType::SIP_ENTRY_SERVER));
		if($sipEntryServerNode)
		{
			$sipEntryServerNode->delete();
		}
	}

	/**
	 * @param $itemId
	 * @param $path
	 * @param $pexipConfig
	 * @return bool
	 */
	protected static function deleteItem($itemId, $path, $pexipConfig)
	{
		$result = true;
		$url = $pexipConfig[kPexipUtils::CONFIG_API_ADDRESS] . $path . "$itemId/";
		$curlWrapper = self::initPexipCurlWrapper(HttpMethods::DELETE, $pexipConfig);
		$results = $curlWrapper->exec($url);
		$httpCode = $curlWrapper->getHttpCode();
		KalturaLog::info('HTTP Request httpCode [' . $httpCode . "] Results [$results]");
		if($httpCode == KCurlHeaderResponse::HTTP_STATUS_NO_CONTENT)
		{
			KalturaLog::info("Pexip Item With id $itemId was deleted succesfully");
		}
		else
		{
			kPexipUtils::logError($curlWrapper, $url);
			$result = false;
		}
		$curlWrapper->close();
		return $result;
	}

	/**
	 * @param $offset
	 * @param $pageSize
	 * @param $pexipConfig
	 * @param $activeOnly
	 * @return array|mixed
	 */
	public static function listRooms($offset, $pageSize, $pexipConfig, $activeOnly = false)
	{
		$listResult = array();
		KalturaLog::info('Fetching Virtual Rooms');
		$url = $pexipConfig[kPexipUtils::CONFIG_API_ADDRESS] . self::ROOM_PREFIX . '?service_type=conference&offset=' . $offset . '&limit=' . $pageSize;
		if($activeOnly)
		{
			$url = $pexipConfig[kPexipUtils::CONFIG_API_ADDRESS] . self::ACTIVE_ROOM_PREFIX . '?service_type=conference&offset=' . $offset . '&limit=' . $pageSize;
		}

		$curlWrapper = self::initPexipCurlWrapper(HttpMethods::GET, $pexipConfig);
		$result = $curlWrapper->exec($url);
		$httpCode = $curlWrapper->getHttpCode();
		KalturaLog::info('HTTP Request httpCode [' . $httpCode . ']');
		if(!$result || $httpCode != KCurlHeaderResponse::HTTP_STATUS_OK)
		{
			kPexipUtils::logError($curlWrapper, $url);
		}
		else
		{
			$listResult = json_decode($result, true);
			KalturaLog::info('Retrieved virtual Rooms: ' . $listResult[kPexipUtils::PARAM_META][kPexipUtils::PARAM_TOTAL_COUNT]);
		}
		$curlWrapper->close();
		return $listResult;
	}

	/**
	 * @param $method
	 * @param $pexipConfig
	 * @param null $data
	 * @return KCurlWrapper
	 */
	protected static function initPexipCurlWrapper($method, $pexipConfig, $data = null)
	{
		$curlWrapper = new KCurlWrapper();
		$curlWrapper->setOpts(array(CURLOPT_SSL_VERIFYHOST => 0,
			CURLOPT_USERPWD => $pexipConfig[kPexipUtils::CONFIG_USER_NAME] . ':' . $pexipConfig[kPexipUtils::CONFIG_PASSWORD],
			CURLOPT_HTTPHEADER => array('Content-Type: application/json'),
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_VERBOSE => 0,
			CURLOPT_HEADER => 1));

		switch($method)
		{
			case HttpMethods::POST:
				$curlWrapper->setOpt(CURLOPT_POST, 1);
				break;
			case HttpMethods::DELETE:
				$curlWrapper->setOpt(CURLOPT_CUSTOMREQUEST, 'DELETE');
				break;
			case HttpMethods::UPDATE:
				$curlWrapper->setOpt(CURLOPT_CUSTOMREQUEST, 'PATCH');
				break;
			default:
				break;
		}

		if($data)
		{
			$curlWrapper->setOpt(CURLOPT_POSTFIELDS, json_encode($data));
		}

		return $curlWrapper;
	}
}