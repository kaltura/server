<?php

/**
 * @package plugins.game
 */
class GamePlugin extends KalturaPlugin implements IKalturaServices
{
	const PLUGIN_NAME = 'game';
	
	public function initService($serviceId, $serviceName, $actionName)
	{
		parent::initService($serviceId, $serviceName, $actionName);
		
		$partnerId = $this->getPartnerId();
		if (!GamePlugin::isAllowedPartner($partnerId))
		{
			throw new KalturaAPIException(KalturaErrors::SERVICE_FORBIDDEN, "{$this->serviceName}->{$this->actionName}");
		}
		
		$this->applyPartnerFilterForClass(self::PLUGIN_NAME);
	}
	
	public static function isAllowedPartner($partnerId)
	{
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
		$redisConfig = kConf::get('game', kConfMapNames::REDIS);
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
}