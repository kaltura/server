<?php
class kConfControlDb
{
	private $mapName;
	private $hostNameRegex;
	private $dsn;
	private $user;
	private $password;
	private $connection;
	private $memcacheObjects;

	function __construct($mapName , $hostNameRegex)
	{
		$this->initConfParams();
		$this->initPdoConnection();
		$this->initMemcacheObjects();
	}

	function setMapName($mapName)
	{
		$this->mapName = $mapName;
	}
	function setHostNameRegex($hostNameRegex)
	{
		$this->hostNameRegex = $hostNameRegex;
	}

	protected function initMemcacheObjects()
	{
		//Get list of memcache
		$memcacheList = kConf::get('write_address_list', 'kRemoteMemCacheConf');
		if(!$memcacheList)
		{
			throw new kCoreException('No write address of memcache servers found.');
		}
		$port = kConf::get('port', 'kRemoteMemCacheConf');
		$this->memcacheObjects = array();
		foreach($memcacheList as $memcacheItem)
		{
			$cacheObject = new kInfraMemcacheCacheWrapper();
			$ret = $cacheObject->init(array('host'=>$memcacheItem ,'port'=>$port));
			if(!$ret)
			{
				throw new kCoreException('Cannot open connectino to memcache host:{$memcacheItem} port:{$port}');
			}
			$this->memcacheObjects[] = $cacheObject;
		}
	}

	protected function getMapNameInCache()
	{
		return $this->mapName . kRemoteMemCacheConf::MAP_DELIMITER . $this->hostNameRegex;
	}

	/*
	 * This class will be used to update maps in the DB
	 * */
	public function update($content)
	{
		//Insert to DB
		$newVersion = $this->insertToDb($content);

		$mapNameInCache = $this->getMapNameInCache();
		//update all memcache
		foreach ($this->memcacheObjects as $memcacheObject)
		{
			$memcacheObject->set(kBaseConfCache::CONF_MAP_PREFIX.$mapNameInCache,$content);
		}
		$this->completeUpdate($mapNameInCache,$newVersion);
	}
	protected function completeUpdate($mapNameInCache,$newVersion)
	{
		//Update version in map list
		$mapListInCache = $this->memcacheObjects[0]->get(kRemoteMemCacheConf::MAP_LIST_KEY);

		$mapListInCache[$mapNameInCache] = $newVersion;
		$mapListInCache['UPDATED_AT']=date("Y-m-d H:i:s");
		foreach ($this->memcacheObjects as $memcacheObject)
		{
			$memcacheObject->set(kRemoteMemCacheConf::MAP_LIST_KEY, $mapListInCache);
		}
		//create new key and set all memcache
		$chacheKey = kBaseConfCache::generateKey();
		foreach ($this->memcacheObjects as $memcacheObject)
		{
			$memcacheObject->set(kBaseConfCache::CONF_CACHE_VERSION_KEY, $chacheKey);
		}
	}

	public function add($mapName , $hostNameRegex, $content)
	{

	}

	protected function insertToDb($content)
	{
		$content = str_replace('\/','/',$content);
		$content = str_replace('"','\"',$content);
		//Get the latest version for this mapName and hostname
		$newVersion = $this->getLatestVersion($this->mapName, $this->hostNameRegex) + 1;
		//
		$cmdLine = "insert into conf_maps (map_name,host_name,status,version,created_at,remarks,content)values('$this->mapName','$this->hostNameRegex',1,$newVersion,'".date("Y-m-d H:i:s")."','','$content');";
		$ret = $this->execute($this->connection,$cmdLine);
		if(!$ret)
		{
			throw new kCoreException('Fail to write into conf_maps table');
		}
		return $newVersion;
	}

	protected function getLatestVersion($mapName , $hostNameRegex)
	{
		$cmdLine = 'select version from conf_maps where conf_maps.map_name=\''.$mapName.'\' and conf_maps.host_name=\''.$hostNameRegex.'\' order by version desc limit 1 ;';
		$output1 = $this->query($this->connection,$cmdLine);
		$version = isset($output1['version']) ? $output1['version'] : 0;
		KalturaLog::debug("Found version - {$version}\r\n");
		return $version;
	}

	protected function initPdoConnection()
	{
		$this->connection = new PDO($this->dsn, $this->user, $this->password);
	}
	protected function initConfParams()
	{
		$dbMap = kConf::getMap('db');
		$defaultSource = $dbMap['datasources']['default'];
		$dbConfig = $dbMap['datasources'][$defaultSource]['connection'];
		$this->dsn = $dbConfig['dsn'];
		$this->user = $dbConfig['user'];
		$this->password = $dbConfig['password'];
	}
	protected function query($dbConnection,$commandLine)
	{
		KalturaLog::debug("executing: {$commandLine}\n");
		$statement = $dbConnection->query($commandLine);
		$output1 = $statement->fetch();
		return $output1;
	}
	protected function execute($dbConnection,$commandLine)
	{
		KalturaLog::debug("executing: {$commandLine}\n");
		$dbConnection->beginTransaction();
		$statement= $dbConnection->prepare($commandLine);
		$statement->execute();
		return $dbConnection->commit();
	}
	public function getContent()
	{
		return $this->memcacheObjects[0]->get(kBaseConfCache::CONF_MAP_PREFIX.$this->getMapNameInCache());
	}
	public function getVersion()
	{
		$mapList = $this->memcacheObjects[0]->get(kRemoteMemCacheConf::MAP_LIST_KEY);
		$version = isset($mapList[$this->getMapNameInCache()]) ? $mapList[$this->getMapNameInCache()] : 0;
		return $version;
	}
}