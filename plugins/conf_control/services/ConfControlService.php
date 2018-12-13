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
	 * @param string $relatedHost
	 * @return KalturaConfigMap
	 */
	function updateAction(KalturaConfigMap $map,$relatedHost)
	{
        //validate input
            //1. Maps can only be unpdated
            //2. Only DB maps

        //Insert value to DB

        //Get list of all memcache servers

        //Sync values to all memcache servers

        //Reset the configuration key
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
		if(!$filter->name || $filter->name=='')
        {
            throw new KalturaAPIException(KalturaErrors::MISSING_MAP_NAME);
        }

		$items = new KalturaConfigMapArray();

		//Check if map exist in file system or in remote cache
		$remoteCache = kCacheConfFactory::getInstance(kCacheConfFactory::REMOTE_MEM_CACHE);
		$hostList =$remoteCache->getRelatedHostList($filter->name ,$filter->relatedHost );
		if($hostList)
		{
			foreach ($hostList as $host)
			{
				$mapObject = new KalturaConfigMap();
				$mapObject->name = $filter->name;
				$mapObject->relatedHost = $host;
				$mapObject->sourceLocation = KalturaConfMapSourceLocation::DB;
				$mapObject->content = $remoteCache->getMap($filter->name, $host);
				$items->insert($mapObject);
			}
		}
		else
		//Check in file system
        {
			$fileSystemCache = kCacheConfFactory::getInstance(kCacheConfFactory::FILE_SYSTEM);
			$fileNames = $fileSystemCache->getIniFilesList($filter->name ,$filter->relatedHost);
			foreach ($fileNames as $fileName)
			{
				$mapObject = new KalturaConfigMap();
			    list($mapObject->name , $mapObject->relatedHost ,$mapObject->content )  = $fileSystemCache->getMapInfo($fileName);
				$mapObject->sourceLocation = KalturaConfMapSourceLocation::FS;
				$items->insert($mapObject);
			}
        }

		$response = new KalturaConfControlListResponse();
		$response->objects = $items;
		$response->totalCount = count($items);

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
		$confMap = $this->getMap($filter->name , $filter->relatedHost);
		return $confMap;
	}

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
		if($map)
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
		if(!$map)
		{
			return null;
		}
		$confMap->name = $mapName;
		$confMap->content = json_encode($map);

		return $confMap;
	}

}