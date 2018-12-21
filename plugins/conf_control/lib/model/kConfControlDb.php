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

	function __construct()
	{
		$this->memcacheObjects = array();
		$this->initDbConfParams();
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

	/**
	 * init list of memcache objects that can be used to read / write
	 * @throws Exception
	 */
	protected function initMemcacheObjects()
	{
		$remoteCacheMap = kConf::getMap('kRemoteMemCacheConf');
		if(!isset($remoteCacheMap['write_address_list']) || !isset($remoteCacheMap['port']))
		{
			throw new Exception('Missing configuration , cannot load cache objects');
		}
		$port = $remoteCacheMap['port'];
		$memcacheList = $remoteCacheMap['write_address_list'];
		foreach($memcacheList as $memcacheItem)
		{
			$cacheObject = new kInfraMemcacheCacheWrapper();
			if(!$cacheObject->init(array('host'=>$memcacheItem ,'port'=>$port)))
			{
				throw new Exception('Cannot open connection to memcache host:{$memcacheItem} port:{$port}');
			}
			$this->memcacheObjects[] = $cacheObject;
		}
	}

	/**
	 * Build special keyword of map name in cache
	 * @return string
	 */
	protected function getMapNameInCache()
	{
		return $this->mapName . kRemoteMemCacheConf::MAP_DELIMITER . $this->hostNameRegex;
	}

	/**
	 * Call this function to update an existing map
	 * @param $content as associative array represeting INI file
	 */
	public function update($content)
	{
		//Insert to DB
		$newVersion = $this->updateMapInDb($content);

		//update all memcache
		$mapNameInCache = $this->getMapNameInCache();
		foreach ($this->memcacheObjects as $memcacheObject)
		{
			$memcacheObject->set(kBaseConfCache::CONF_MAP_PREFIX.$mapNameInCache,$content);
		}
		$this->updateMapCacheVersion($mapNameInCache,$newVersion);
	}

	/**
	 * Call this function to update an existing map
	 * @param $mapNameInCache - the keyname of the map in cache
	 * @param $newVersion - the increased version of the map
	 */
	protected function updateMapCacheVersion($mapNameInCache, $newVersion)
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

	/**
	 * Update exsiting map in the database
	 * @param $content - ini file as serialized json string
	 * @return the new version of the map
	 * @throws Exception
	 */
	protected function updateMapInDb($content)
	{

		$currentVersion = $this->getLatestVersion($this->mapName, $this->hostNameRegex);
		if(!$currentVersion)
		{
			throw new Exception('Map does not exist in DB');
		}
		return $this->insertMapRecordToDb($content , $currentVersion + 1);
	}

	/**
	 * Insert new map to the database
	 * @param $content - ini file as serialized json string
	 * @return the new version of the map
	 * @throws Exception
	 */
	protected function insertMapToDb($content)
	{
		if($this->getLatestVersion($this->mapName, $this->hostNameRegex))
		{
			throw new Exception('Map already exist in DB');
		}
		return $this->insertMapRecordToDb($content , 1);
	}

	/**
	 * Insert new map to the database
	 * @param $content - ini file as serialized json string
	 * @param $version - the version to set on the record in the DB
	 * @return the version of the map
	 * @throws Exception
	 */
	protected function insertMapRecordToDb($content , $version)
	{
		$content = str_replace('\/','/',$content);
		$content = str_replace('"','\"',$content);
		$ret = ConfMapsPeer::addNewMapVersion($this->mapName, $this->hostNameRegex, $content, $version );
		if(!$ret)
		{
			throw new Exception('Fail to write into conf_maps table');
		}
		return $version;
	}

	/**
	 * Insert new map to the database
	 * @param $mapName - name of the map
	 * @param $hostNameRegex - regex of related host
	 * @return the latest version of the map
	 */
	protected function getLatestVersion($mapName , $hostNameRegex)
	{
		$mapRecord = ConfMapsPeer::getLatestMap($mapName,$hostNameRegex);
		$version = $mapRecord->getVersion();
		KalturaLog::debug("Found version - {$version} for map {$mapName} hostNameRegex {$hostNameRegex}");
		return $version;
	}

	/**
	 * Init a connection to DB
	 */
	protected function initPdoConnection()
	{
		$this->connection = new PDO($this->dsn, $this->user, $this->password);
	}

	/**
	 * Init a connection to DB
	 */
	protected function initDbConfParams()
	{
		$dbMap = kConf::getMap('db');
		$defaultSource = $dbMap ['datasources'] ['default'];
		$dbConfig = $dbMap ['datasources'] [$defaultSource] ['connection'];
		$this->dsn = $dbConfig ['dsn'];
		$this->user = $dbConfig ['user'];
		$this->password = $dbConfig ['password'];
	}

	/**
	 * Execute a DB query
	 * @param $dbConnection connection object to DB
	 * @param $commandLine to execute
	 * @return the sql excution command
	 */
	protected function query($dbConnection,$commandLine)
	{
		KalturaLog::debug("executing: {$commandLine}\n");
		$statement = $dbConnection->query($commandLine);
		return $statement->fetch();
	}

	/**
	 * Execute a DB command line
	 */
	protected function execute($dbConnection,$commandLine)
	{
		KalturaLog::debug("executing: {$commandLine}\n");
		$dbConnection->beginTransaction();
		$statement= $dbConnection->prepare($commandLine);
		$statement->execute();
		return $dbConnection->commit();
	}

	/**
     * Returns map specific content from memcache
     * @returns string of JSON serialized ini map stored in cache
     */
	public function getMapContent()
	{
		return $this->memcacheObjects[0]->get(kBaseConfCache::CONF_MAP_PREFIX.$this->getMapNameInCache());
	}

	/**
	 * @returns version of the map in memcache
	 */
	public function getMapVersionInCache()
	{
		$mapList = $this->memcacheObjects[0]->get(kRemoteMemCacheConf::MAP_LIST_KEY);
		$version = isset($mapList[$this->getMapNameInCache()]) ? $mapList[$this->getMapNameInCache()] : 0;
		return $version;
	}
}