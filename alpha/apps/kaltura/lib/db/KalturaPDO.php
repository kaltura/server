<?php

/**
 *  @package server-infra
 *  @subpackage DB
 */
class KalturaPDO extends PropelPDO
{
	/**
	 * Use to set logged info for Kaltura logger
	 */
	const KALTURA_ATTR_NAME = -1001;
		
	/**
	 * Use to disable transaction
	 */
	const KALTURA_ATTR_NO_TRANSACTION = 'noTransaction';
	
	/**
	 * Sets the number of retries of doSave()
	 */
	const SAVE_MAX_RETRIES = 4; 
	
	protected static $comment = null;
	protected $kalturaOptions = array();
	protected $connectionName = null;
	protected $hostName = null;
	protected $enableComments = true;
	protected $configKey = null;

	/* (non-PHPdoc)
	 * @see PDO::__construct()
	 */
	public function __construct($dsn, $username = null, $password = null, $driver_options = array(), $config_key = null)
	{
		if(isset($driver_options[KalturaPDO::KALTURA_ATTR_NAME]))
		{
			$this->connectionName = $driver_options[KalturaPDO::KALTURA_ATTR_NAME];
			$this->kalturaOptions = DbManager::getKalturaConfig($this->connectionName);
		}
		
		list($mysql, $connection) = explode(':', $dsn);
		$arguments = explode(';', $connection);
		foreach($arguments as $argument)
		{
			list($argumentName, $argumentValue) = explode('=', $argument);
			if(strtolower($argumentName) == 'host')
			{
				$this->hostName = $argumentValue;
				break;
			}
		}
		$this->configKey = $config_key;
					
		$connStart = microtime(true);

		parent::__construct($dsn, $username, $password, $driver_options);

		$connTook = microtime(true) - $connStart;
		
		KalturaLog::debug("conn took - $connTook seconds to $dsn");
		
		KalturaMonitorClient::monitorConnTook($dsn, $connTook);		

		$this->setAttribute(PDO::ATTR_STATEMENT_CLASS, array('KalturaStatement'));
	}

	public function setCommentsEnabled($enabled) 
	{
		$this->enableComments = $enabled;
	}

	public function getConnectionName() 
	{
		return $this->connectionName;
	}

	public function getHostName() 
	{
		return $this->hostName;
	}

	public function getComment() 
	{
		if(!self::$comment)
		{
			$uniqueId = new UniqueId();
			self::$comment = (isset($_SERVER["HOSTNAME"]) ? $_SERVER["HOSTNAME"] : gethostname());
			self::$comment .= "[$uniqueId]";
		}
		
		return self::$comment . "[$this->connectionName]";
	}
	
	protected function getCommentWrapped() 
	{
		if(!$this->enableComments)
			return '';
			
		$commentBody = $this->getComment();
		return "/* $commentBody */ ";
	}
	
	/* (non-PHPdoc)
	 * @see PropelPDO::prepare()
	 */
	public function prepare($sql, $driver_options = array())
	{
		$comment = $this->getCommentWrapped();
		$sql = $comment . $sql;
		
		return parent::prepare($sql, $driver_options);
	}
	
	/* (non-PHPdoc)
	 * @see PDO::exec()
	 */
	public function exec($sql)
	{
		$comment = $this->getCommentWrapped();
		$sql = $comment . $sql;
		
		KalturaLog::debug($sql);
		
		$sqlStart = microtime(true);
		try
		{
			$result = parent::exec($sql);
		}
		catch(PropelException $pex)
		{
			KalturaLog::alert($pex->getMessage());
			throw new PropelException("Database error");
		}
		$sqlTook = microtime(true) - $sqlStart;
		KalturaLog::debug("Sql took - " . $sqlTook . " seconds");
		KalturaMonitorClient::monitorDatabaseAccess($sql, $sqlTook, $this->hostName);
		
		return $result;
	}

	public function queryAndFetchAll($sql, $fetchStyle, &$sqlConditions, $columnIndex = 0, $filter = null)
	{
		$finalSql = str_replace(kApiCache::KALTURA_COMMENT_MARKER, $this->getComment(), $sql);
		
		KalturaLog::debug($finalSql);
		
		$sqlStart = microtime(true);
		$stmt = parent::query($finalSql);
		
		$sqlTook = microtime(true) - $sqlStart;
		KalturaLog::debug("Sql took - " . $sqlTook . " seconds");
		KalturaMonitorClient::monitorDatabaseAccess($sql, $sqlTook, $this->hostName);
		
		if (!$stmt)
			return false;
		
		if ($fetchStyle == PDO::FETCH_COLUMN)
			$result = $stmt->fetchAll($fetchStyle, $columnIndex);
		else
			$result = $stmt->fetchAll($fetchStyle);
		
		if(is_null($result))
			return false;
			
		if(!$result)
			$result = array();
			
		$filteredResult = kApiCache::filterQueryResult($result, $filter);
	
		$sqlConditions[] = array($this->configKey, $sql, $fetchStyle, $columnIndex, $filter, $filteredResult);		
		
		return $filteredResult;
	}
	
	public function xxx()
	{
		$distributed_sphinx_dsn = "mysql:host=127.0.0.1;port=9312;";
		$prod_sphinx_dsn = "mysql:host=pa-sphinx25;port=9312;";
		$index_0_dsn = "mysql:host=127.0.0.1;port=9312;";
		$index_1_dsn = "mysql:host=127.0.0.1;port=9313;";
		$index_2_dsn = "mysql:host=127.0.0.1;port=9314;";
		$index_3_dsn = "mysql:host=127.0.0.1;port=9315;";
		$index_4_dsn = "mysql:host=127.0.0.1;port=9316;";
		$index_5_dsn = "mysql:host=127.0.0.1;port=9317;";
		$index_6_dsn = "mysql:host=127.0.0.1;port=9318;";
		$index_7_dsn = "mysql:host=127.0.0.1;port=9319;";
		
		$distributedConn = new PDO($distributed_sphinx_dsn);
		$prodConn = new PDO($prod_sphinx_dsn);
		$index_0 = new PDO($index_0_dsn);
		$index_1 = new PDO($index_1_dsn);
		$index_2 = new PDO($index_2_dsn);
		$index_3 = new PDO($index_3_dsn);
		$index_4 = new PDO($index_4_dsn);
		$index_5 = new PDO($index_5_dsn);
		$index_6 = new PDO($index_6_dsn);
		$index_7 = new PDO($index_7_dsn);
		
		$connectionPerIndex = array(
			0 => $index_0,
			1 => $index_1,
			2 => $index_2,
			3 => $index_3,
			4 => $index_4,
			5 => $index_5,
			6 => $index_6,
			7 => $index_7,
		);
		
		$queries = file('/root/yossi/split_partner_index_files/sphinx_entry_queries_pa_32.txt');
		$queries = array_map('trim',$queries);
		
		foreach ($queries as $query)
		{
			preg_match("/partner_id = (\d*)/", $q, $matches);
			$partner_id = $matches[1];
			
			if(!$partner_id)
			{
				echo "Partner_id not found for query [$query], continue to next query\n";
				continue;
			}
			
			$s = microtime(true);
			$distributedConn->query($q);
			$took = microtime(true) - $s;
			echo "distributed index query took [" . $took . "]\n";
			
			$s = microtime(true);
			$prodConn->query($q);
			$took = microtime(true) - $s;
			echo "prod index query took [" . $took . "]\n";
			
			$s = microtime(true);
			$connectionPerIndex[$partner_id%8]->query($q);
			$took = microtime(true) - $s;
			echo "Dedicated index query took [" . $took . "]\n";
		}
	}
	
	/* (non-PHPdoc)
	 * @see PDO::query()
	 */
	public function query()
	{
		kApiCache::disableConditionalCache();
	
		$args = func_get_args();
		
		$sql = $args[0];
		KalturaLog::debug($sql);
		
		$comment = $this->getCommentWrapped();
		$sql = $comment . $sql;
		
		$sqlStart = microtime(true);
		try
		{
			if (version_compare(PHP_VERSION, '5.3', '<'))
				$result = call_user_func_array(array($this, 'parent::query'), $args);
			else
				$result = call_user_func_array('parent::query', $args);
		}
		catch(PropelException $pex)
		{
			KalturaLog::alert($pex->getMessage());
			throw new PropelException("Database error");
		}
		$sqlTook = microtime(true) - $sqlStart;
		KalturaLog::debug("Sql took - " . $sqlTook . " seconds");
		KalturaMonitorClient::monitorDatabaseAccess($sql, $sqlTook, $this->hostName);
		
		return $result;
	}
	
	public function getKalturaOption($option)
	{
		if(isset($this->kalturaOptions[$option]))
			return $this->kalturaOptions[$option];
			
		return null;
	}
	
	public function beginTransaction()
	{
		if($this->getKalturaOption(KalturaPDO::KALTURA_ATTR_NO_TRANSACTION))
			return true;
		
		return parent::beginTransaction();
	}
}
