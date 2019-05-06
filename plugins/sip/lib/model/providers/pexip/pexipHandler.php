<?php
/**
 * @package plugins.sip
 * @subpackage model.pexip
 */

class PexipHandler
{
	const ROOM_PREFIX = '/api/admin/configuration/v1/conference/';
	const ADP_PREFIX = '/api/admin/configuration/v1/automatic_participant/';
	const ROOM_NAME_KEY = 'roomName';
	const ROOM_ID_KEY = 'id';
	const ADP_ALIAS_KEY = 'alias';
	const ADP_ID_KEY = 'id';

	const ALREADY_EXISTS_PATTERN = '/already exists/';

	/**
	 * @param LiveStreamEntry $dbLiveEntry
	 * @param $pexipConfig
	 * @throws KalturaAPIException
	 */
	public static function createCallObjects(LiveStreamEntry $dbLiveEntry, $pexipConfig)
	{
		$roomId = PexipHandler::addVirtualRoom($dbLiveEntry, $pexipConfig);
		if (!$roomId)
		{
			Throw new KalturaAPIException(KalturaErrors::PEXIP_ROOM_CREATION_FAILED, $dbLiveEntry->getId());
		}
		$primaryRtmp = $dbLiveEntry->getPrimaryBroadcastingUrl() . "&i=0/" . $dbLiveEntry->getStreamName();
		$primaryAdpId = PexipHandler::addADP($dbLiveEntry, $roomId, $primaryRtmp, "Primary", $pexipConfig);
		if (!$primaryAdpId)
		{
			Throw new KalturaAPIException(KalturaErrors::PEXIP_ADP_CREATION_FAILED, $dbLiveEntry->getId());
		}

		$secondaryRtmp = $dbLiveEntry->getSecondaryBroadcastingUrl() . "&i=0/" . $dbLiveEntry->getStreamName();
		$secondaryAdpId = PexipHandler::addADP($dbLiveEntry, $roomId, $secondaryRtmp, "Secondary", $pexipConfig);

		if (!$secondaryAdpId)
		{
			Throw new KalturaAPIException(KalturaErrors::PEXIP_ADP_CREATION_FAILED, $dbLiveEntry->getId());
		}

		$sipEntryServerNode = PexipUtils::createSipEntryServerNode($dbLiveEntry, $roomId, $primaryAdpId, $secondaryAdpId);
		/** @var  SipEntryServerNode $sipEntryServerNode */
		if (!$sipEntryServerNode)
		{
			Throw new KalturaAPIException(KalturaErrors::SIP_ENTRY_SERVER_NODE_CREATION_FAILED, $dbLiveEntry->getId());
		}
	}

	/**
	 * @param LiveStreamEntry $entry
	 * @param $pexipConfig
	 * @return array|string
	 */
	protected static function addVirtualRoom(LiveStreamEntry $entry, $pexipConfig)
	{
		$roomName = pexipUtils::getRoomName($entry, $pexipConfig);
		$url = $pexipConfig['apiAddress'] . self::ROOM_PREFIX;
		KalturaLog::info("Creating Virtual Room with name $roomName for entry " . $entry->getId());
		$data = array(
			'name' => $roomName,
			'service_type' => 'conference',
			'aliases' => array(array('alias' => $entry->getIntId() . "@" . $pexipConfig['hostUrl']))
		);
		$curlWrapper = self::initPexipCurlWrapper(HttpMethods::POST, $pexipConfig, $data);

		$result = $curlWrapper->doExec($url);
		KalturaLog::info("Result is " . print_r($result, true));
		$virtualRoomId = null;
		if ($curlWrapper->getHttpCode() == KCurlHeaderResponse::HTTP_STATUS_CREATED)
		{
			$virtualRoomId = PexipUtils::extractIdFromCreatedResult($result, $url, $curlWrapper->getInfo(CURLINFO_HEADER_SIZE));
		}
		else
		{
			preg_match(self::ALREADY_EXISTS_PATTERN, $result, $alreadyExistsMatch);
			if ( !empty($alreadyExistsMatch))
			{
				KalturaLog::info("Virtual Room for entry " . $entry->getId() . " already exists.");
				$virtualRoom = self::getVirtualRoom($pexipConfig, self::ROOM_NAME_KEY , $roomName);
				if ($virtualRoom && isset($virtualRoom['id']))
				{
					return $virtualRoom['id'];
				}
			}
			else
			{
				PexipUtils::logError($curlWrapper, $url);
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
		switch($key)
		{
			case self::ROOM_NAME_KEY:
				$url = $pexipConfig['apiAddress']. self::ROOM_PREFIX .'?name='.$value;
				break;
			case self::ROOM_ID_KEY:
				$url = $pexipConfig['apiAddress']. self::ROOM_PREFIX . "$value/";
				break;
			default:
				return $virtualRoom;
		}

		KalturaLog::info("Getting Virtual Room info for $key $value");

		$curlWrapper = self::initPexipCurlWrapper(HttpMethods::GET, $pexipConfig);
		$result = $curlWrapper->exec($url);

		KalturaLog::info("Result From Pexip Server : " . print_r($result,true));
		if ($result && $curlWrapper->getHttpCode() == KCurlHeaderResponse::HTTP_STATUS_OK )
		{
			$virtualRoom = PexipUtils::extractObjectFromdResult($result);
			if (!$virtualRoom)
			{
				KalturaLog::info("Didn't find Virutal-Room matching to $key $value");
			}
		}
		else
		{
			PexipUtils::logError($curlWrapper, $url);
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
	protected static function getADP($pexipConfig , $key = null , $value = null)
	{
		$adp = null;
		switch($key)
		{
			case self::ADP_ALIAS_KEY:
				$url = $pexipConfig['apiAddress']. self::ADP_PREFIX . "?alias=".urlencode($value);
				break;
			case self::ADP_ID_KEY:
				$url = $pexipConfig['apiAddress']. self::ADP_PREFIX . "$value/";
				break;
			default:
				return $adp;
		}

		KalturaLog::info("Getting ADP info for $key $value");
		$curlWrapper = self::initPexipCurlWrapper(HttpMethods::GET, $pexipConfig);
		$result = $curlWrapper->exec($url);

		KalturaLog::info("Result From Pexip Server : " . print_r($result,true));
		if ($result && $curlWrapper->getHttpCode() == KCurlHeaderResponse::HTTP_STATUS_OK )
		{
			$adp = PexipUtils::extractObjectFromdResult($result);
			if (!$adp)
			{
				KalturaLog::info("Didn't find ADP matching to $key $value");
			}
		}
		else
		{
			PexipUtils::logError($curlWrapper, $url);
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
		if (isset($adp['conference']))
		{
			foreach ($adp['conference'] as $connectedRoom)
			{
				if ($connectedRoom == $roomAddress)
					return false;
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
	 * @return null
	 */
	protected static function addADP(LiveStreamEntry $entry, $roomId, $participantAddress, $name, $pexipConfig)
	{
		$adpId = null;
		KalturaLog::info("Creating RTMP-ADP $name to Virtual Room $roomId");

		$adpData = array(
			'alias' => $participantAddress,
			'remote_display_name' => $entry->getId() . "_$name"."_ADP",
			'description' => "ADP for $name ". $entry->getId(),
			'protocol' => 'rtmp',
			'role' => 'guest',
			'conference' => array(  $pexipConfig['apiAddress'] . self::ROOM_PREFIX . "$roomId/"),
			'streaming' => 1,
			'keep_conference_alivei_if_multiple' => 1
		);
		$url =  $pexipConfig['apiAddress'] . self::ADP_PREFIX;
		$curlWrapper = self::initPexipCurlWrapper(HttpMethods::POST, $pexipConfig, $adpData);
		$result = $curlWrapper->doExec($url);
		KalturaLog::info("Result FOR ADP CREATION IS ". print_r($result,true));

		if ($curlWrapper->getHttpCode() == KCurlHeaderResponse::HTTP_STATUS_CREATED )
		{
			$adpId = PexipUtils::extractIdFromCreatedResult($result,$url, $curlWrapper->getInfo(CURLINFO_HEADER_SIZE));
		}
		else
		{
			preg_match(self::ALREADY_EXISTS_PATTERN, $result, $alreadyExistsMatch);
			if ( !empty($alreadyExistsMatch))
			{
				KalturaLog::info("ADP for alias " . $participantAddress . " already exists.");
				$adp = self::getADP($pexipConfig, self::ADP_ALIAS_KEY, $participantAddress);
				if ($adp)
				{
					if (self::shouldUpdateAdp($adp,$roomId))
					{
						self::updateADP($adp['id'], $roomId, $pexipConfig);
					}
					return $adp['id'];
				}
			}
			else
			{
				PexipUtils::logError($curlWrapper, $url);
			}
		}
		$curlWrapper->close();
		return $adpId;
	}

	/**
	 * @param $adpIp
	 * @param $roomId
	 * @param $pexipConfig
	 * @return mixed
	 */
	protected static function updateADP($adpIp, $roomId, $pexipConfig)
	{
		$adpId =null;
		KalturaLog::info("Updating ADP $adpIp adding Virtual Room $roomId");

		$adpData = array(
			'conference' => array(  $pexipConfig['apiAddress'] . self::ROOM_PREFIX . "$roomId/"),
		);
		$url =  $pexipConfig['apiAddress'] . self::ADP_PREFIX . "$adpIp/";

		$curlWrapper = self::initPexipCurlWrapper(HttpMethods::UPDATE, $pexipConfig, $adpData);
		$result = $curlWrapper->doExec($url);
		KalturaLog::info("Result FOR ADP UPDATE IS ". print_r($result,true));

		if ($curlWrapper->getHttpCode() != KCurlHeaderResponse::HTTP_STATUS_ACCEPTED )
		{
			PexipUtils::logError($curlWrapper, $url);
		}
		$curlWrapper->close();
		return $adpId;
	}

	/**
	 * @param $roomId
	 * @param $primaryAdpId
	 * @param $secondaryAdpId
	 * @param $pexipConfig
	 */
	public static function deleteCallobjects($roomId, $primaryAdpId, $secondaryAdpId, $pexipConfig)
	{
		if ($roomId)
		{
			KalturaLog::info("Deleting Virtual room with id $roomId");
			self::deleteItem($roomId, self::ROOM_PREFIX, $pexipConfig);
		}
		if ($primaryAdpId)
		{
			KalturaLog::info("Deleting Primary ADP with id $primaryAdpId");
			self::deleteItem($primaryAdpId, self::ADP_PREFIX, $pexipConfig);
		}
		if($secondaryAdpId)
		{
			KalturaLog::info("Deleting Secondary ADP with id $secondaryAdpId");
			self::deleteItem($secondaryAdpId, self::ADP_PREFIX, $pexipConfig);
		}
	}

	/**
	 * @param $itemId
	 * @param $path
	 * @param $pexipConfig
	 * @return bool
	 */
	protected static function deleteItem($itemId, $path , $pexipConfig)
	{
		$url = $pexipConfig['apiAddress'] . $path . "$itemId/";
		$curlWrapper = self::initPexipCurlWrapper(HttpMethods::DELETE, $pexipConfig);
		$results = $curlWrapper->exec($url);
		$httpCode = $curlWrapper->getHttpCode();
		KalturaLog::info("Status code is : $httpCode");

		KalturaLog::info("HTTP Request httpCode [" . $httpCode . "] Results [$results]");
		if ($results && $httpCode == KCurlHeaderResponse::HTTP_STATUS_NO_CONTENT)
		{
			KalturaLog::info("Pexip Item With id $itemId was deleted succesfully");
		}
		else
		{
			PexipUtils::logError($curlWrapper, $url);
		}
		$curlWrapper->close();
		return true;
	}

	/**
	 * @param $offset
	 * @param $pageSize
	 * @param $pexipConfig
	 * @return array|mixed
	 */
	public static function listRooms($offset, $pageSize, $pexipConfig)
	{
		$url = $pexipConfig['apiAddress'] . self::ROOM_PREFIX . '?service_type=conference&offset=' . $offset . '&limit=' . $pageSize;
		KalturaLog::info("Fetching Virtual Rooms");

		$curlWrapper = self::initPexipCurlWrapper(HttpMethods::GET, $pexipConfig);
		$result = $curlWrapper->exec($url);
		$httpCode = $curlWrapper->getHttpCode();
		KalturaLog::info("Status code is : $httpCode");
		$curlWrapper->close();
		KalturaLog::info("HTTP Request httpCode [" . $httpCode . "]");
		if (!$result || $httpCode != KCurlHeaderResponse::HTTP_STATUS_OK)
		{
			PexipUtils::logError($curlWrapper, $url);
			return array();
		}
		else
		{
			$result = json_decode($result, true);
			KalturaLog::info("Retrieved virtual Rooms: " . $result['meta']['total_count']);
			return $result;
		}
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
		$curlWrapper->setOpts(array( CURLOPT_SSL_VERIFYHOST => 0,
			CURLOPT_USERPWD => $pexipConfig['userName'].":".$pexipConfig['password'],
			CURLOPT_HTTPHEADER => array('Content-Type: application/json'),
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_VERBOSE => 0,
			CURLOPT_HEADER => 1));

		switch($method)
		{
			case HttpMethods::POST:
				$curlWrapper->setOpt(CURLOPT_POST ,1);
				break;
			case HttpMethods::DELETE:
				$curlWrapper->setOpt(CURLOPT_CUSTOMREQUEST ,"DELETE");
				break;
			case HttpMethods::UPDATE:
				$curlWrapper->setOpt(CURLOPT_CUSTOMREQUEST ,"PATCH");
				break;
			default:
				break;
		}

		if ($data)
		{
			$curlWrapper->setOpt(CURLOPT_POSTFIELDS ,json_encode($data));
		}

		return $curlWrapper;
	}
}