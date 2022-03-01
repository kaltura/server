<?php

/**
 * @package plugins.game
 */
class GamePlugin extends KalturaPlugin implements IKalturaServices
{
	const PLUGIN_NAME = 'game';
	
	public static function isAllowedPartner($partnerId)
	{
		if (PermissionPeer::isValidForPartner(PermissionName::GAME_PLUGIN_PERMISSION, $partnerId))
			return true;
		
		$partner = PartnerPeer::retrieveByPK($partnerId);
		if ($partner)
		{
			return $partner->getPluginEnabled(self::PLUGIN_NAME);
		}
		
		return false;
	}
	
	/* (non-PHPdoc)
	* @see IKalturaServices::getServicesMap()
	*/
	public static function getServicesMap()
	{
		$map = array(
			'userScore' => 'UserScoreService',
		);
		return $map;
	}
	
	/* (non-PHPdoc)
     * @see IKalturaPlugin::getPluginName()
     */
	public static function getPluginName()
	{
		return self::PLUGIN_NAME;
	}
	
	/**
	 * @param $valueName
	 * @return string external API value of dynamic enum.
	 */
	public static function getApiValue($valueName)
	{
		return self::getPluginName() . IKalturaEnumerator::PLUGIN_VALUE_DELIMITER . $valueName;
	}
	
	/**
	 * @return kInfraRedisCacheWrapper
	 * @throws Exception
	 */
	public static function initGameServicesRedisInstance()
	{
		$redisWrapper = new kInfraRedisCacheWrapper();
		$redisConfig = kConf::get(self::PLUGIN_NAME, kConfMapNames::REDIS, null);
		if (!$redisConfig || !isset($redisConfig['host']) || !isset($redisConfig['port']) || !isset($redisConfig['timeout']) ||
			!isset($redisConfig['cluster'])  || !isset($redisConfig['persistent']))
		{
			return null;
		}
		
		$config = array('host' => $redisConfig['host'], 'port' => $redisConfig['port'], 'timeout' => floatval($redisConfig['timeout']),
			'cluster' => $redisConfig['cluster'], 'persistent' => $redisConfig['persistent']);
		$redisWrapper->init($config);
		return $redisWrapper;
	}
	
	/**
	 * Prepare the redis key to be called with
	 * @return string
	 * @throws KalturaAPIException
	 */
	public function prepareGameObjectKey($gameObjectId, $gameObjectType)
	{
		if (is_null($gameObjectId))
		{
			throw new KalturaAPIException(KalturaErrors::GAME_OBJECT_ID_REQUIRED);
		}
		if (!$gameObjectType)
		{
			throw new KalturaAPIException(KalturaErrors::GAME_OBJECT_TYPE_REQUIRED);
		}
		
		$redisKey = kCurrentContext::getCurrentPartnerId();
		$redisKey.= '_' . $gameObjectType . '_' . $gameObjectId;
		KalturaLog::info("Accessing Redis game object: $redisKey");
		return $redisKey;
	}
	
	public function getKuserIdFromPuserId($puser)
	{
		$partner = kCurrentContext::getCurrentPartnerId();
		$kuser = kuserPeer::getKuserByPartnerAndUid($partner, $puser);
		if (!$kuser)
		{
			throw new KalturaAPIException(KalturaErrors::USER_ID_NOT_FOUND, $puser);
		}
		
		return $kuser->getId();
	}
	
	/**
	 * Retrieves pusers for all kusers in the results array, and returns a map for these pusers
	 * @param $results
	 * @return array
	 * @throws PropelException
	 */
	public function createMapKuserToPuser($results)
	{
		$kusers = array_keys($results);
		
		$users = kuserPeer::retrieveByPKs($kusers);
		if (!$users)
		{
			KalturaLog::info('Failed to retrieve users from DB');
			return array();
		}
		
		$mapKuserPuser = array();
		foreach ($users as $user)
		{
			if ($user->getPuserId())
			{
				$mapKuserPuser[$user->getId()] = $user->getPuserId();
			}
			else
			{
				$kuserId = $user->getId();
				$mapKuserPuser[$kuserId] = 'Unknown';
				KalturaLog::info("No user found for kuser $kuserId");
			}
		}
		
		return $mapKuserPuser;
	}
}
