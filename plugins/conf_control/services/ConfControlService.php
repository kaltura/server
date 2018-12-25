<?php
/**
 * Created by IntelliJ IDEA.
 * User: moshe.maor
 * Date: 12/11/2018
 * Time: 4:42 PM
 */
/**
 * @service confControl
 * @package plugins.confControl
 * @subpackage api.services
 */
class ConfControlService extends KalturaBaseService
{
	protected function kalturaNetworkAllowed($actionName)
	{
		//TODO
		return true;
	}

	/**
	 * Add configuration mapp
	 *
	 * @action add
	 * @param KalturaConfigMap $map
	 * @param string $relatedHost
	 * @return KalturaConfigMap
	 */
	function addAction(KalturaConfigMap $map,$relatedHost)
	{

	}
	/**
	 * Add configuration mapp
	 *
	 * @action update
	 * @param KalturaConfigMap $map
	 * @return KalturaConfigMap
	 * @throws KalturaAPIException
	 */
	function updateAction(KalturaConfigMap $map)
	{
		//get map by values name / hostname
		$dbMap = ConfMapsPeer::getLatestMap($map->name , $map->relatedHost);
		if(!$dbMap)
		{
			throw new KalturaAPIException(KalturaErrors::MAP_NOT_EXIST );
		}
		$map->validateContent();
		$newMapVersion = new ConfMaps();
		$newMapVersion->addNewMapVersion($dbMap, $map->content);
		$map->fromObject($newMapVersion);
		return $map;
	}

	/**
	 * List configuration maps
	 *
	 * @action list
	 * @param KalturaConfigMapFilter $filter
	 * @return KalturaConfControlListResponse
     * @throws KalturaAPIException MISSING_MAP_NAME
	 */
	function listAction(KalturaConfigMapFilter $filter = null)
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
	 * @param KalturaConfigMapFilter $filter
	 * @return KalturaConfigMap
	 */
	function getAction(KalturaConfigMapFilter $filter)
	{
		$confMap = $filter->getMap();
		return $confMap;
	}

<<<<<<< HEAD
=======
	/**
	 * @param $mapName
	 * @param $hostPatern
	 *
	 * @return KalturaConfigMap
	 */
	protected function getMap($mapName , $hostPatern)
	{
		$confMap = new KalturaConfigMap();
		$hostPatern = str_replace('*','#', $hostPatern);
		/*  @var kRemoteMemCacheConf $remoteCache  */
		$remoteCache = kCacheConfFactory::getInstance(kCacheConfFactory::REMOTE_MEM_CACHE);
		$map = $remoteCache->loadByHostName($mapName, $hostPatern);
		if(!empty($map))
		{
			$confMap->sourceLocation = KalturaConfMapSourceLocation::DB;
			$confMap->isEditable = true;
		}
		else
		{
			/*  @var kFileSystemConf $confFs  */
			$confFs = kCacheConfFactory::getInstance(kCacheConfFactory::FILE_SYSTEM);
			$map = $confFs->loadByHostName($mapName, $hostPatern);
			$confMap->sourceLocation = KalturaConfMapSourceLocation::FS;
			$confMap->isEditable = false;
		}
		if(empty($map))
		{
			return null;
		}
		$confMap->name = $mapName;
		$confMap->content = json_encode($map);
>>>>>>> 97eca3b6795858b61826ba1a9a7cbbc9309308ec


}