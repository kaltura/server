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
	
	protected static $comment = null;
	protected $kalturaOptions = array();
	protected $connectionName = null;
	protected $hostName = null;
	protected $enableComments = true;

	/* (non-PHPdoc)
	 * @see PDO::__construct()
	 */
	public function __construct($dsn, $username = null, $password = null, $driver_options = array())
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
			
		$connStart = microtime(true);

		parent::__construct($dsn, $username, $password, $driver_options);

		KalturaLog::debug("conn took - ". (microtime(true) - $connStart). " seconds to $dsn");

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
		KalturaLog::debug($sql);
		
		$comment = $this->getCommentWrapped();
		$sql = $comment . $sql;
		
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
		KalturaLog::debug("Sql took - " . (microtime(true) - $sqlStart) . " seconds");
		
		return $result;
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
		KalturaLog::debug("Sql took - " . (microtime(true) - $sqlStart) . " seconds");
		
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
		{
			KalturaLog::debug("Transactions disabled");
			return false;
		}
		
		return parent::beginTransaction();
	}
}
