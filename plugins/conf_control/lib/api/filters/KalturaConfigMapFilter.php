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
		if(!$this->nameEqual || $this->nameEqual=='')
		{
			return $response;
		}
		$items = new KalturaConfigMapArray();

		//Check if map exist in file system or in remote cache
		$remoteCache = kCacheConfFactory::getInstance(kCacheConfFactory::REMOTE_MEM_CACHE);
		$hostList =$remoteCache->getHostList($this->nameEqual ,$this->relatedHostEqual );
		if($hostList)
		{
			foreach ($hostList as $host)
			{
				$dbMapObject = ConfMapsPeer::getLatestMap($this->nameEqual,$host);
				$apiMapObject = new KalturaConfigMap();
				$apiMapObject->fromObject($dbMapObject);
				$apiMapObject->sourceLocation = KalturaConfMapSourceLocation::DB;
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
		return $response;
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
		$hostPatern = str_replace('*','#', $this->relatedHostEqual);
		/*  @var kRemoteMemCacheConf $remoteCache  */
		$remoteCache = kCacheConfFactory::getInstance(kCacheConfFactory::REMOTE_MEM_CACHE);
		$map = $remoteCache->loadByHostName($this->nameEqual, $hostPatern);
		if(!empty($map))
		{
			$confMap->sourceLocation = KalturaConfMapSourceLocation::DB;
			$confMap->isEditable = true;
		}
		else
		{
			/*  @var kFileSystemConf $confFs  */
			$confFs = kCacheConfFactory::getInstance(kCacheConfFactory::FILE_SYSTEM);
			$map = $confFs->loadByHostName($this->nameEqual, $hostPatern);
			$confMap->sourceLocation = KalturaConfMapSourceLocation::FS;
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
