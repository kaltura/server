<?php
/**
 * @package plugins.confControl
 * @subpackage api.filters
 */
class KalturaConfigMapFilter extends KalturaConfigMapBaseFilter
{
	public function getListResponse(KalturaFilterPager $pager, KalturaDetachedResponseProfile $responseProfile = null)
	{
		$response = new KalturaConfControlListResponse();
		if(!$this->name || $this->name=='')
		{
			return $response;
		}

		$items = new KalturaConfigMapArray();

		//Check if map exist in file system or in remote cache
		$remoteCache = kCacheConfFactory::getInstance(kCacheConfFactory::REMOTE_MEM_CACHE);
		$hostList =$remoteCache->getHostList($this->name ,$this->relatedHost );
		if($hostList)
		{
			foreach ($hostList as $host)
			{
				$confControlDb = new kConfControlDb();
				$confControlDb->setMapName($this->name);
				$confControlDb->setHostNameRegex($host);

				ConfMapsPeer::
				//TODO from object - from DB

				$mapObject = new KalturaConfigMap();
				$mapObject->name = $this->name;
				$mapObject->relatedHost = $host;
				$mapObject->sourceLocation = KalturaConfMapSourceLocation::DB;
				$mapObject->content = $confControlDb->getMapContent();
				$mapObject->version = $confControlDb->getMapVersionInCache();
				$mapObject->isEditable = true;
				$items->insert($mapObject);
			}
		}
		else		//Check in file system
		{
			$fileSystemCache = kCacheConfFactory::getInstance(kCacheConfFactory::FILE_SYSTEM);
			$fileNames = $fileSystemCache->getIniFilesList($this->name ,$filter->relatedHost);
			foreach ($fileNames as $fileName)
			{
				$mapObject = new KalturaConfigMap();
				list($mapObject->name , $mapObject->relatedHost ,$mapObject->content )  = $fileSystemCache->getMapInfo($fileName);
				$mapObject->sourceLocation = KalturaConfMapSourceLocation::FS;
				$items->insert($mapObject);
				$mapObject->version = 1;
				$mapObject->isEditable = false;
			}
		}
		$response->objects = $items;
		$response->totalCount = count($items);
	}
	public function getCoreFilter()
	{
		return new ConfMapsFilter();
	}

	/**
	 * @return KalturaConfigMap
	 */
	public function getMap()
	{
		$confMap = new KalturaConfigMap();
		$hostPatern = str_replace('*','#', $hostPatern);
		/*  @var kRemoteMemCacheConf $remoteCache  */
		$remoteCache = kCacheConfFactory::getInstance(kCacheConfFactory::REMOTE_MEM_CACHE);
		$map = $remoteCache->loadByHostName($this->mapName, $this->hostPatern);
		if(!empty($map))
		{
			$confMap->sourceLocation = KalturaConfMapSourceLocation::DB;
			$confMap->isEditable = true;
		}
		else
		{
			/*  @var kFileSystemConf $confFs  */
			$confFs = kCacheConfFactory::getInstance(kCacheConfFactory::FILE_SYSTEM);
			$map = $confFs->loadByHostName($this->mapName, $this->hostPatern);
			$confMap->sourceLocation = KalturaConfMapSourceLocation::FS;
			$confMap->isEditable = false;
		}
		if(empty($map))
		{
			return null;
		}
		$confMap->name = $this->mapName;
		$confMap->content = json_encode($map);

		return $confMap;
	}
}
