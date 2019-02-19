<?php
/**
 * @package plugins.confMaps
 * @subpackage api.filters
 */
class KalturaConfMapsFilter extends KalturaConfMapsBaseFilter
{
	public function getListResponse(KalturaFilterPager $pager, KalturaDetachedResponseProfile $responseProfile = null)
	{
		$response = new KalturaConfMapsListResponse();
		if(!$this->nameEqual || $this->nameEqual=='')
		{
			return $response;
		}
		$items = new KalturaConfMapsArray();

		//Check if map exist in file system or in remote cache
		$remoteCache = kCacheConfFactory::getInstance(kCacheConfFactory::REMOTE_MEM_CACHE);
		$hostList =$remoteCache->getHostList($this->nameEqual ,$this->relatedHostEqual );
		if($hostList)
		{
			foreach ($hostList as $host)
			{
				$dbMapObject = ConfMapsPeer::getMapByVersion($this->nameEqual, $host);
				$apiMapObject = new KalturaConfMaps();
				$apiMapObject->fromObject($dbMapObject);
				$apiMapObject->sourceLocation = KalturaConfMapsSourceLocation::DB;
				$apiMapObject->isEditable = true;
				$items->insert($apiMapObject);
			}
		}
		else		//Check in file system
		{
			$fileSystemCache = kCacheConfFactory::getInstance(kCacheConfFactory::FILE_SYSTEM);
			$fileNames = $fileSystemCache->getIniFilesList($this->nameEqual ,$this->relatedHostEqual);
			foreach ($fileNames as $fileName)
			{
				$mapObject = new KalturaConfMaps();
				list($mapObject->name , $mapObject->relatedHost ,$mapObject->content )  = $fileSystemCache->getMapInfo($fileName);
				$mapObject->sourceLocation = KalturaConfMapsSourceLocation::FS;
				$items->insert($mapObject);
				$mapObject->version = 1;
				$mapObject->isEditable = false;
			}
		}
		$response->objects = $items;
		$response->totalCount = count($items);
		return $response;
	}
	public function getCoreFilter()
	{
		return new ConfMapsFilter();
	}

	/**
	 * @return KalturaConfMaps
	 */
	public function getMap()
	{
		$confMap = new KalturaConfMaps();
		$hostPatern = str_replace('*','#', $this->relatedHostEqual);
		/*  @var kRemoteMemCacheConf $remoteCache  */
		$remoteCache = kCacheConfFactory::getInstance(kCacheConfFactory::REMOTE_MEM_CACHE);
		$map = null;
		if (!is_null($this->versionEqual))
		{
			$dbMap = ConfMapsPeer::getMapByVersion($this->nameEqual, $hostPatern, $this->versionEqual);
			if ($dbMap)
			{
				$confMap->fromObject($dbMap);
				$confMap->sourceLocation = KalturaConfMapsSourceLocation::DB;
				$confMap->isEditable = true;
				return $confMap;
			}
		}
		else
		{
			$map = $remoteCache->loadByHostName($this->nameEqual, $hostPatern);
		}
		if(!empty($map))
		{
			$confMap->sourceLocation = KalturaConfMapsSourceLocation::DB;
			$confMap->isEditable = true;
		}
		else
		{
			/*  @var kFileSystemConf $confFs  */
			$confFs = kCacheConfFactory::getInstance(kCacheConfFactory::FILE_SYSTEM);
			$map = $confFs->loadByHostName($this->nameEqual, $hostPatern);
			$confMap->sourceLocation = KalturaConfMapsSourceLocation::FS;
			$confMap->isEditable = false;
		}
		if(empty($map))
		{
			return null;
		}
		$confMap->name = $this->nameEqual;
		$confMap->content = json_encode($map);

		return $confMap;
	}
}
