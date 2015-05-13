<?php

class kCouchbaseCacheQuery
{
	const VIEW_RESPONSE_PROFILE_OBJECT_SPECIFIC = 'objectSpecific';
//function (doc, meta) {
//	if (meta.type == "json") {
//		if(doc.type == "primaryObject"){
//			emit([doc.partnerId, doc.objectType, doc.objectId], doc.data);
//		}
//	}
//}

	const VIEW_RESPONSE_PROFILE_RELATED_OBJECT = 'relatedObject';
//function (doc, meta) {
//	if (meta.type == "json") {
//		if(doc.type == "relatedObject"){
//			emit([doc.partnerId, doc.triggerObjectType], {
//				"responseProfileKey": doc.responseProfileKey, 
//				"objectType": doc.objectType
//			});
//		}
//	}
//}
//reduce _count

	const VIEW_RESPONSE_PROFILE_SESSION_TYPE = 'sessionType';
//function (doc, meta) {
//	if (meta.type == "json") {
//		if(doc.type == "primaryObject"){
//			emit([doc.partnerId, doc.objectType], {
//				"protocol": doc.protocol, 
//				"ksType": doc.ksType, 
//				"userRole": doc.userRole
//			});
//		}
//	}
//}
//reduce _count
	
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
	 * @var array
	 */  
	private $key = array();
	
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
		$this->startKey[$key] = $value;
	}

	/**
	 * @param string $key
	 * @param string $value
	 */
	public function addKey($key, $value)
	{
		$this->startKey[$key] = $value;
	}

	/**
	 * @param array $keys
	 */
	public function setKeys(array $keys)
	{
		$this->keys = $keys;
	}
	
	public function toQuery()
	{
		$query = CouchbaseViewQuery::from($this->designDocumentName, $this->viewName);
		
		if(!is_null($this->offset))
			$query = $query->skip($this->offset);
			
		if(!is_null($this->limit))
			$query = $query->limit($this->limit);
			
		if(!is_null($this->stale))
			$query = $query->stale($this->stale);
			
		$custom = array();
		
		if(!is_null($this->descending))
			$custom['descending'] = $this->descending;
			
		if(!is_null($this->startKeyDocId))
			$custom['startkey_docid'] = $this->startKeyDocId;
			
		if(!is_null($this->endKeyDocId))
			$custom['endkey_docid'] = $this->endKeyDocId;
			
		if(!is_null($this->group))
			$custom['group'] = $this->group;
			
		if(!is_null($this->groupLevel))
			$custom['group_level'] = $this->groupLevel;
			
		if(!is_null($this->inclusiveEnd))
			$custom['inclusive_end'] = $this->inclusiveEnd;
			
		if(!is_null($this->reduce))
			$custom['reduce'] = $this->reduce;
			
		if(!is_null($this->connectionTimeout))
			$custom['connection_timeout'] = $this->connectionTimeout;
			
		if(count($this->startKey))
			$custom['startkey'] = array_values($this->startKey);
			
		if(count($this->endKey))
			$custom['endkey'] = array_values($this->endKey);
			
		if(count($this->key))
			$custom['key'] = array_values($this->key);
			
		if(count($this->keys))
			$custom['keys'] = array_values($this->keys);

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
		$this->id = $meta['id'];
		$this->key = $meta['key'];
		$this->data = $meta['data'];
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
	private $totalCount;
	
	/**
	 * @var array<kCouchbaseCacheListItem>
	 */
	private $objects = array();
	
	public function __construct(array $meta)
	{
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
		$cluster = new CouchbaseCluster($config['dsn'], $config['username'], $config['password']);
		$this->bucket = $cluster->openBucket($config['name']);
		
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
	}

	/* (non-PHPdoc)
	 * @see kBaseCacheWrapper::doGet()
	 */
	protected function doGet($key)
	{
		try
		{
			$meta = $this->bucket->get($key);
			return $meta->value;
		}
		catch(CouchbaseException $e)
		{
		}
		return false;
	}
	
	/* (non-PHPdoc)
	 * @see kBaseCacheWrapper::doMultiGet()
	 */
	protected function doMultiGet($keys)
	{
		try
		{
			$metas = $this->bucket->get($keys);
			$values = array();
			foreach($metas as $meta)
				$values[] = $meta->value;
				
			return $values;
		}
		catch(CouchbaseException $e)
		{
		}
		return false;
	}
	
	/* (non-PHPdoc)
	 * @see kBaseCacheWrapper::doIncrement()
	 */
	protected function doIncrement($key, $delta = 1)
	{
		$meta = $this->bucket->counter($key, $delta);
		return $meta->value;
	}

	/* (non-PHPdoc)
	 * @see kBaseCacheWrapper::doSet()
	 */
	protected function doSet($key, $var, $expiry = 0)
	{
		$meta = $this->bucket->upsert($key, $var, array(
			'expiry' => $expiry
		));
		
		return is_null($meta->error);
	}

	/* (non-PHPdoc)
	 * @see kBaseCacheWrapper::doAdd()
	 */
	protected function doAdd($key, $var, $expiry = 0)
	{
		$meta = $this->bucket->insert($key, $var, array(
			'expiry' => $expiry
		));
		
		return is_null($meta->error);
	}

	/* (non-PHPdoc)
	 * @see kBaseCacheWrapper::doDelete()
	 */
	protected function doDelete($key)
	{
		try
		{
			$meta = $this->bucket->remove($key);
			return is_null($meta->error);
		}
		catch(CouchbaseException $e)
		{
		}
		return false;
	}

	/**
	 * @param string $key
	 * @param mixed $var
	 * @param int $expiry
	 */
	public function replace($key, $var, $expiry = 0)
	{
		try
		{
			$meta = $this->bucket->replace($key, $var, array(
				'expiry' => $expiry
			));
			
			return is_null($meta->error);
		}
		catch(CouchbaseException $e)
		{
		}
		return false;
	}

	/**
	 * @param string $key
	 * @param mixed $var
	 */
	public function append($key, $var)
	{
		try
		{
			$meta = $this->bucket->append($key, $var);
			return $meta->value;
		}
		catch(CouchbaseException $e)
		{
		}
		return false;
	}

	/**
	 * @param string $key
	 * @param mixed $var
	 */
	public function prepend($key, $var)
	{
		try
		{
			$meta = $this->bucket->prepend($key, $var);
			return $meta->value;
		}
		catch(CouchbaseException $e)
		{
		}
		return false;
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
			return $meta->value;
		}
		catch(CouchbaseException $e)
		{
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
			$meta = $this->bucket->remove($keys);
			return is_null($meta->error);
		}
		catch(CouchbaseException $e)
		{
		}
		return false;
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
			$values = array();
			foreach($metas as $meta)
				$values[] = $meta->value;
				
			return $values;
		}
		catch(CouchbaseException $e)
		{
		}
		return false;
	}

	/**
	 * @param string $view
	 * @return kCouchbaseCacheQuery
	 */
	public function getNewQuery($view)
	{
		if(!isset($this->views[$view]))
			return null;
			
		KalturaLog::debug("Loads query [" . print_r($this->views[$view], true) . "]");
		list($designDocumentName, $viewName) = $this->views[$view];
		return new kCouchbaseCacheQuery($designDocumentName, $viewName);
	}

	/**
	 * @param array $keys
	 * @param mixed $var
	 * @return kCouchbaseCacheList
	 */
	public function query(kCouchbaseCacheQuery $query)
	{
		$meta =  $this->bucket->query($query);
		return new kCouchbaseCacheList($meta);
	}
}