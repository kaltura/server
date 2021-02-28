<?php
/**
 * @service confMaps
 * @package plugins.confMaps
 * @subpackage api.services
 */
class ConfMapsService extends KalturaBaseService
{
	public function initService($serviceId, $serviceName, $actionName)
	{
		parent::initService($serviceId, $serviceName, $actionName);

		if(kCurrentContext::$ks_partner_id == Partner::BATCH_PARTNER_ID)
		{
			return;
		}

		$kuser = kCurrentContext::getCurrentKsKuser();
		if(!$kuser)
		{
			throw new KalturaAPIException(KalturaErrors::USER_ID_NOT_PROVIDED_OR_EMPTY);
		}
	}

	/**
	 * Add configuration map
	 *
	 * @action add
	 * @param KalturaConfMaps $map
	 * @return KalturaConfMaps
	 * @throws KalturaErrors::MAP_ALREADY_EXIST
	 */
	function addAction(KalturaConfMaps $map)
	{
		$map->relatedHost = strtolower($map->relatedHost);

		$dbMap = ConfMapsPeer::getMapByVersion($map->name, $map->relatedHost);
		if($dbMap)
		{
			throw new KalturaAPIException(KalturaErrors::MAP_ALREADY_EXIST, $map->name, $map->relatedHost);
		}
		$map->validateContent();
		$newMapVersion = new ConfMaps();
		$map->toInsertableObject($newMapVersion);
		$newMapVersion->setStatus(ConfMapsStatus::STATUS_ENABLED);
		$newMapVersion->setVersion(0);
		$newMapVersion->setRemarks(kCurrentContext::$ks);
		$newMapVersion->save();
		$newMapVersion->syncMapsToCache();
		$map->fromObject($newMapVersion);
		return $map;
	}
	/**
	 * Update configuration map
	 *
	 * @action update
	 * @param KalturaConfMaps $map
	 * @return KalturaConfMaps
	 * @throws KalturaErrors::MAP_DOES_NOT_EXIST
	 */
	function updateAction(KalturaConfMaps $map)
	{
		$map->relatedHost = strtolower($map->relatedHost);
		//get map by values name / hostname
		$dbMap = ConfMapsPeer::getMapByVersion($map->name, $map->relatedHost);
		if(!$dbMap)
		{
			throw new KalturaAPIException(KalturaErrors::MAP_DOES_NOT_EXIST );
		}
		$map->validateContent();

		$newMapVersion = new ConfMaps();
		$newMapVersion->addNewMapVersion($dbMap, $map->content, $map->changeDescription);
		$newMapVersion->syncMapsToCache();
		$map->fromObject($newMapVersion);
		return $map;
	}

	/**
	 * List configuration maps
	 *
	 * @action list
	 * @param KalturaConfMapsFilter $filter
	 * @return KalturaConfMapsListResponse
	 * @throws KalturaErrors::MISSING_MAP_NAME
	 */
	function listAction(KalturaConfMapsFilter $filter)
	{
		kApiCache::disableCache();
		$pager = new KalturaFilterPager();
		$response = $filter->getListResponse($pager);
		return $response;
	}

	/**
	 * Get configuration map
	 *
	 * @action get
	 * @param KalturaConfMapsFilter $filter
	 * @return KalturaConfMaps
	 */
	function getAction(KalturaConfMapsFilter $filter)
	{
		kApiCache::disableCache();
		$confMap = $filter->getMap();
		return $confMap;
	}

	/**
	* List configuration maps names
	*
	* @action getMapNames
	* @return KalturaStringArray
	*/
	public function getMapNamesAction()
	{
		$mapNames= ConfMapsPeer::retrieveMapsNames();
		$result =  KalturaStringArray::fromDbArray($mapNames);
		return $result;
	}

	/**
	 * Get configuration map cache key
	 *
	 * @action getCacheVersionId
	 * @return string
	 */
	public function getCacheVersionIdAction()
	{
		return kConf::getCachedVersionId();
	}

	/**
	 * Get batch configuration map
	 *
	 * @action getBatchMap
	 * @return string
	 * @param string $hostName
	 */
	function getBatchMapAction($hostName)
	{
		kApiCache::disableCache();
		$res = IniUtils::getBatchConfigFromFS();
		if (!$res)
		{
			$batchMapNames = array('batch','workers');
			/*  @var kRemoteMemCacheConf $remoteCache  */
			$remoteCache = kCacheConfFactory::getInstance(kCacheConfFactory::REMOTE_MEM_CACHE);
			$map = $remoteCache->loadByHostName($batchMapNames ,$hostName);
			if (!empty($map))
			{
				$res = IniUtils::arrayToIniString($map);
			}
		}
		return json_encode($res);
	}
}

