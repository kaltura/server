<?php

class kCouchbaseCacheQuery
{	
	/**
	 * @var string
	 */  
	private $designDocumentName;
	
	/**
	 * @var string
	 */  
	private $viewName;
	
	/**
	 * @var int
	 */
	private $offset;
	
	/**
	 * @var int
	 */
	private $limit;
	
	/**
	 * @var boolean
	 */
	private $descending = null;
	
	/**
	 * @var array
	 */  
	private $startKey = array();
	
	/**
	 * @var array
	 */  
	private $endKey = array();
	
	/**
	 * @var string
	 */  
	private $startKeyDocId = null;
	
	/**
	 * @var string
	 */  
	private $endKeyDocId = null;
	
	/**
	 * @var boolean
	 */
	private $group = null;
	
	/**
	 * @var int
	 */
	private $groupLevel = null;
	
	/**
	 * @var boolean
	 */
	private $inclusiveEnd = true;
	
	/**
	 * @var array|string
	 */  
	private $key = null;
	
	/**
	 * @var array
	 */  
	private $keys = array();
	
	/**
	 * @var boolean
	 */
	private $reduce = null;
	
	/**
	 * One of false, ok, update_after
	 * @var boolean | string 
	 */
	private $stale = null;
	
	/**
	 * @var int
	 */
	private $connectionTimeout = null;
	
	/**
	 * @param string $designDocumentName
	 * @param string $viewName
	 */
	public function __construct($designDocumentName, $viewName)
	{
		$this->designDocumentName = $designDocumentName;
		$this->viewName = $viewName;
	}
	
	/**
	 * @param int $offset
	 */
	public function setOffset($offset)
	{
		$this->offset = $offset;
	}

	/**
	 * @param int $limit
	 */
	public function setLimit($limit)
	{
		$this->limit = $limit;
	}

	/**
	 * @param boolean $descending
	 */
	public function setDescending($descending)
	{
		$this->descending = $descending;
	}

	/**
	 * @param string $startKeyDocId
	 */
	public function setStartKeyDocId($startKeyDocId)
	{
		$this->startKeyDocId = $startKeyDocId;
	}

	/**
	 * @param string $endKeyDocId
	 */
	public function setEndKeyDocId($endKeyDocId)
	{
		$this->endKeyDocId = $endKeyDocId;
	}

	/**
	 * @param boolean $group
	 */
	public function setGroup($group)
	{
		$this->group = $group;
	}

	/**
	 * @param int $groupLevel
	 */
	public function setGroupLevel($groupLevel)
	{
		$this->groupLevel = $groupLevel;
	}

	/**
	 * @param boolean $inclusiveEnd
	 */
	public function setInclusiveEnd($inclusiveEnd)
	{
		$this->inclusiveEnd = $inclusiveEnd;
	}

	/**
	 * @param boolean $reduce
	 */
	public function setReduce($reduce)
	{
		$this->reduce = $reduce;
	}

	/**
	 * @param boolean $stale
	 */
	public function setStale($stale)
	{
		$this->stale = $stale;
	}

	/**
	 * @param int $connectionTimeout
	 */
	public function setConnectionTimeout($connectionTimeout)
	{
		$this->connectionTimeout = $connectionTimeout;
	}

	/**
	 * @param string $key
	 * @param string $value
	 */
	public function addStartKey($key, $value)
	{
		$this->startKey[$key] = $value;
	}

	/**
	 * @param string $key
	 * @param string $value
	 */
	public function addEndKey($key, $value)
	{
		$this->endKey[$key] = $value;
	}

	/**
	 * @param string $key
	 * @param string $value
	 */
	public function addKey($key, $value)
	{
		if(!is_array($this->key))
			$this->key = array();
		
		$this->key[$key] = $value;
	}

	/**
	 * @param array $keys
	 */
	public function setKeys(array $keys)
	{
		$this->keys = $keys;
	}

	/**
	 * @param string $key
	 */
	public function setKey($key)
	{
		$this->key = $key;
	}
	
	public function toQuery()
	{
		$query = CouchbaseViewQuery::from($this->designDocumentName, $this->viewName);
		
		if(!is_null($this->offset))
			$query = $query->skip($this->offset);
			
		if(!is_null($this->limit))
			$query = $query->limit($this->limit);
			
		if(!is_null($this->stale))
			$query = $query->stale($this->stale === false ? 'false' : $this->stale);
			
		$custom = array();
		
		if(!is_null($this->descending))
			$custom['descending'] = $this->descending ? 'true' : 'false';
			
		if(!is_null($this->startKeyDocId))
			$custom['startkey_docid'] = $this->startKeyDocId;
			
		if(!is_null($this->endKeyDocId))
			$custom['endkey_docid'] = $this->endKeyDocId;
			
		if(!is_null($this->group))
			$custom['group'] = $this->group ? 'true' : 'false';
			
		if(!is_null($this->groupLevel))
			$custom['group_level'] = $this->groupLevel;
			
		if(!is_null($this->inclusiveEnd))
			$custom['inclusive_end'] = $this->inclusiveEnd ? 'true' : 'false';
			
		if(!is_null($this->reduce))
			$custom['reduce'] = $this->reduce ? 'true' : 'false';
			
		if(!is_null($this->connectionTimeout))
			$custom['connection_timeout'] = $this->connectionTimeout;
			
		if(count($this->startKey))
			$custom['startkey'] = json_encode(array_values($this->startKey));
			
		if(count($this->endKey))
			$custom['endkey'] = json_encode(array_values($this->endKey));
			
		if(is_array($this->key) && count($this->key))
			$custom['key'] = json_encode(array_values($this->key));
		elseif($this->key)
			$custom['key'] = json_encode($this->key);
			
		if(count($this->keys))
			$custom['keys'] = json_encode(array_values($this->keys));

		if(count($custom))
			$query = $query->custom($custom);
		
		if(!is_null($this->offset) && !is_null($this->limit))
			$this->offset += $this->limit;
		
		return $query;
	}
}


class kCouchbaseCacheListItem
{
	/**
	 * @var string
	 */
	private $id;
	
	/**
	 * @var array
	 */
	private $key;
	
	/**
	 * @var array
	 */
	private $data;
	
	public function __construct(array $meta)
	{
		if(isset($meta['id']))
			$this->id = $meta['id'];
		
		$this->key = $meta['key'];
		$this->data = $meta['value'];
	}
	
	/**
	 * @return string
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * @return array
	 */
	public function getKey()
	{
		return $this->key;
	}

	/**
	 * @return array
	 */
	public function getData()
	{
		return $this->data;
	}
}


class kCouchbaseCacheList
{
	/**
	 * @var int
	 */
	private $totalCount = 0;
	
	/**
	 * @var array<kCouchbaseCacheListItem>
	 */
	private $objects = array();
	
	public function __construct(array $meta = null)
	{
		if(is_null($meta))
		{
			return;
		}
		
		if(isset($meta['total_rows']))
			$this->totalCount = $meta['total_rows'];
		
		foreach($meta['rows'] as $row)
		{
			$this->objects[] = new kCouchbaseCacheListItem($row);
		}
	}
	
	/**
	 * @return int
	 */
	public function getTotalCount()
	{
		return $this->totalCount;
	}
	
	/**
	 * @return int
	 */
	public function getCount()
	{
		return count($this->objects);
	}

	/**
	 * @return array<kCouchbaseCacheListItem>
	 */
	public function getObjects()
	{
		return $this->objects;
	}
}

class kCouchbaseCacheWrapper extends kBaseCacheWrapper
{
	const ERROR_CODE_THE_KEY_ALREADY_EXISTS_IN_THE_SERVER = 12;
	const ERROR_CODE_THE_KEY_DOES_NOT_EXIST_IN_THE_SERVER = 13;
	const ERROR_CODE_OPERATION_TIMEOUT_IN_THE_SERVER = 23;
	
	/**
	 * @var string
	 */
	protected $name;
	
	/**
	 * @var boolean
	 */
	protected $debug;
	
	/**
	 * @var CouchbaseBucket
	 */
	protected $bucket;
	
	/**
	 * @var array<array> ['designDocumentName' => $, 'viewName' => $]
	 */
	protected $views = array();
	
	/* (non-PHPdoc)
	 * @see kBaseCacheWrapper::doInit()
	 */
	protected function doInit($config)
	{
		if (!class_exists('CouchbaseCluster') )
		{
			return false;
		}

		if(isset($config['debug']) && $config['debug'])
			$this->debug = true;
			
		$cluster = new CouchbaseCluster($config['dsn'], $config['username'], $config['password']);
		try
		{
			$this->name = $config['name'];
			
			if($this->debug)
				KalturaLog::debug("Bucket name [$this->name]");
				
			$connStart = microtime(true);
			$this->bucket = $cluster->openBucket($this->name);
			$connTook = microtime(true) - $connStart;
			self::safeLog("connect took - {$connTook} seconds to {$config['dsn']} bucket {$this->name}");
		}
		catch(CouchbaseException $e)
		{
			KalturaLog::err($e);
			return false;
		}

		if(isset($config['properties']))
		{
			foreach($config['properties'] as $propertyName => $propertyValue)
				$this->bucket->$propertyName = $propertyValue;
		}

		if(isset($config['views']))
		{
			foreach($config['views'] as $view => $viewConfig)
			{
				list($designDocumentName, $viewName) = explode(',', $viewConfig, 2);
				$this->views[$view] = array(
					'designDocumentName' => trim($designDocumentName),
					'viewName' => trim($viewName)
				);
			}
		}
		
		return true;
	}

	/* (non-PHPdoc)
	 * @see kBaseCacheWrapper::doGet()
	 */
	protected function doGet($key, $retries = 1)
	{
		$couchbaseError = null;
		do
		{
			if ($this->debug)
				KalturaLog::debug("Trying to get from couchbase. attempts left: $retries");
			try
			{
				$meta = $this->bucket->get($key);

				if ($this->debug)
					KalturaLog::debug("key [$key], meta [" . print_r($meta, true) . "]");

				return $meta->value;
			} catch (CouchbaseException $e)
			{
				$couchbaseError = $e;
				if ($e->getCode() == self::ERROR_CODE_THE_KEY_DOES_NOT_EXIST_IN_THE_SERVER)
					return false;
			}
			$retries--;
		}
		while ($retries >= 0);

		KalturaLog::err("No retries left for Couchbase get operation for key [$key]");
		throw $couchbaseError;
	}
	
	/* (non-PHPdoc)
	 * @see kBaseCacheWrapper::doMultiGet()
	 */
	protected function doMultiGet($keys)
	{
		try
		{
			$metas = $this->bucket->get($keys);
			
			if($this->debug)
				KalturaLog::debug("key [" . print_r($keys, true) . "], metas [" . print_r($metas, true) . "]");
				
			$values = array();
			foreach($metas as $meta)
			{
				if($meta->error && $meta->error->getCode() != self::ERROR_CODE_THE_KEY_DOES_NOT_EXIST_IN_THE_SERVER)
				{
					throw $meta->error;
				}
				$values[] = $meta->value;
			}
				
			return $values;
		}
		catch(CouchbaseException $e)
		{
			if($e->getCode() == self::ERROR_CODE_THE_KEY_DOES_NOT_EXIST_IN_THE_SERVER)
				return false;
				
			throw $e;
		}
	}
	
	/* (non-PHPdoc)
	 * @see kBaseCacheWrapper::doIncrement()
	 */
	protected function doIncrement($key, $delta = 1)
	{
		$meta = $this->bucket->counter($key, $delta);
		
		if($this->debug)
			KalturaLog::debug("key [$key], meta [" . print_r($meta, true) . "]");
			
		return $meta->value;
	}

	/* (non-PHPdoc)
	 * @see kBaseCacheWrapper::doSet()
	 */
	protected function doSet($key, $var, $expiry = 0, $retries = 1)
	{
		$couchbaseError = null;

		if ($this->debug)
			KalturaLog::debug("Bucket name [$this->name] key [$key] var [" . print_r($var, true) . "]");

		do
		{
			if ($this->debug)
				KalturaLog::debug("Trying to upsert to couchbase. attempts left: $retries");
			try
			{
				$meta = $this->bucket->upsert($key, $var, array(
					'expiry' => $expiry
				));

				return is_null($meta->error);
			} catch (CouchbaseException $e)
			{
				KalturaLog::err($e);
				$couchbaseError = $e;
				if ($e->getCode() != self::ERROR_CODE_OPERATION_TIMEOUT_IN_THE_SERVER)
					throw $e;
			}
			$retries--;
		} while ($retries >= 0);

		KalturaLog::err("No retries left for Couchbase upsert operation for key [$key]");
		throw $couchbaseError;
	}

	/* (non-PHPdoc)
	 * @see kBaseCacheWrapper::doAdd()
	 */
	protected function doAdd($key, $var, $expiry = 0)
	{
		if($this->debug)
			KalturaLog::debug("key [$key], var [" . print_r($var, true) . "]");
			
		try
		{
			$meta = $this->bucket->insert($key, $var, array(
				'expiry' => $expiry
			));
		}
		catch(CouchbaseException $e)
		{
			if($e->getCode() == self::ERROR_CODE_THE_KEY_ALREADY_EXISTS_IN_THE_SERVER)
				return false;
			
			throw $e;
		}
		
		return is_null($meta->error);
	}

	/* (non-PHPdoc)
	 * @see kBaseCacheWrapper::doDelete()
	 */
	protected function doDelete($key)
	{
		if($this->debug)
			KalturaLog::debug("key [$key]");
			
		try
		{
			$meta = $this->bucket->remove($key);
			return is_null($meta->error);
		}
		catch(CouchbaseException $e)
		{
			if($e->getCode() == self::ERROR_CODE_THE_KEY_DOES_NOT_EXIST_IN_THE_SERVER)
				return false;
				
			throw $e;
		}
	}

	/**
	 * @param string $key
	 * @param mixed $var
	 * @param int $expiry
	 */
	public function replace($key, $var, $expiry = 0)
	{
		$meta = $this->bucket->replace($key, $var, array(
			'expiry' => $expiry
		));
		
		if($this->debug)
			KalturaLog::debug("key [$key] var [" . print_r($var, true) . "] meta [" . print_r($meta, true) . "]");
		
		return is_null($meta->error);
	}

	/**
	 * @param string $key
	 * @param mixed $var
	 */
	public function append($key, $var)
	{
		$meta = $this->bucket->append($key, $var);
		
		if($this->debug)
			KalturaLog::debug("key [$key] var [" . print_r($var, true) . "] meta [" . print_r($meta, true) . "]");
			
		return $meta->value;
	}

	/**
	 * @param string $key
	 * @param mixed $var
	 */
	public function prepend($key, $var)
	{
		$meta = $this->bucket->prepend($key, $var);
		
		if($this->debug)
			KalturaLog::debug("key [$key] var [" . print_r($var, true) . "] meta [" . print_r($meta, true) . "]");
			
		return $meta->value;
	}

	/**
	 * @param string $key
	 * @param mixed $var
	 */
	public function getAndTouch($key)
	{
		try
		{
			$meta = $this->bucket->get($key);
			
			if($this->debug)
				KalturaLog::debug("key [$key]");
				
			return $meta->value;
		}
		catch(CouchbaseException $e)
		{
			if($e->getCode() == self::ERROR_CODE_THE_KEY_DOES_NOT_EXIST_IN_THE_SERVER)
				return false;
				
			throw $e;
		}
		return false;
	}

	/**
	 * @param array $keys
	 * @param boolean
	 */
	public function multiDelete(array $keys)
	{
		try
		{
			$metas = $this->bucket->remove($keys);
			
			if($this->debug)
				KalturaLog::debug("key [" . print_r($keys, true) . "]");
				
			return true;
		}
		catch(CouchbaseException $e)
		{
			if($e->getCode() == self::ERROR_CODE_THE_KEY_DOES_NOT_EXIST_IN_THE_SERVER)
				return false;
				
			throw $e;
		}
	}

	/**
	 * @param array $keys
	 * @param mixed $var
	 */
	public function multiGetAndTouch(array $keys)
	{
		try
		{
			$metas = $this->bucket->get($keys);
			
			if($this->debug)
				KalturaLog::debug("key [" . implode(', ', $keys) . "], metas [" . print_r($metas, true) . "]");
				
			$values = array();
			foreach($keys as $key)
			{
				$meta = $metas[$key];
				if($meta->error)
				{
					KalturaLog::warning($meta->error->getMessage());
				}
				
				$values[$key] = $meta->value;
			}
				
			return $values;
		}
		catch(CouchbaseException $e)
		{
			if($e->getCode() == self::ERROR_CODE_THE_KEY_DOES_NOT_EXIST_IN_THE_SERVER)
				return false;
				
			throw $e;
		}
	}

	/**
	 * @param string $view
	 * @return kCouchbaseCacheQuery
	 */
	public function getNewQuery($view)
	{
		if(!isset($this->views[$view]))
		{
			KalturaLog::err("Couchbase view [$view] not found");
			return null;
		}
			
		if($this->debug)
			KalturaLog::debug("Loads query [" . print_r($this->views[$view], true) . "]");
			
		$designDocumentName = $this->views[$view]['designDocumentName'];
		$viewName = $this->views[$view]['viewName'];
		return new kCouchbaseCacheQuery($designDocumentName, $viewName);
	}

	/**
	 * @param array $keys
	 * @param mixed $var
	 * @return kCouchbaseCacheList
	 */
	public function query(kCouchbaseCacheQuery $query)
	{
		$couchBaseQuery = $query->toQuery();
		$meta =  $this->bucket->query($couchBaseQuery);
		return new kCouchbaseCacheList($meta);
	}
}
