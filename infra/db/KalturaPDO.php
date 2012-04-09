<?php

/**
 *  @package infra
 *  @subpackage DB
 */
class KalturaPDO extends PropelPDO
{
	/**
	 * Attribute to use to set logged info for Kaltura logger
	 */
	const KALTURA_ATTR_NAME = -1001;
	
	protected static $comment = null;
	
	protected $connectionName = null;
	protected $hostName = null;
	
	protected $enableComments = true;
	
	/* (non-PHPdoc)
	 * @see PDO::__construct()
	 */
	public function __construct($dsn, $username = null, $password = null, $driver_options = array())
	{	
		if(isset($driver_options[self::KALTURA_ATTR_NAME]))
		{
			$this->connectionName = $driver_options[self::KALTURA_ATTR_NAME];
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
			self::$comment = (isset($_SERVER["HOSTNAME"]) ? $_SERVER["HOSTNAME"] : '');
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
		
		try
		{
			return parent::exec($sql);
		}
		catch(PropelException $pex)
		{
			KalturaLog::alert($pex->getMessage());
			throw new PropelException("Database error");
		}
	}

	/* (non-PHPdoc)
	 * @see PDO::query()
	 */
	public function query()
	{
		$args = func_get_args();
		
		$sql = $args[0];
		KalturaLog::debug($sql);
		
		$comment = $this->getCommentWrapped();
		$sql = $comment . $sql;
		
		try
		{
			if (version_compare(PHP_VERSION, '5.3', '<'))
				return call_user_func_array(array($this, 'parent::query'), $args);
			
			return call_user_func_array('parent::query', $args);
		}
		catch(PropelException $pex)
		{
			KalturaLog::alert($pex->getMessage());
			throw new PropelException("Database error");
		}
	}
}
