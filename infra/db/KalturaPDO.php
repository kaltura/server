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
	
	protected $enableComments = true;
	
	/* (non-PHPdoc)
	 * @see PDO::__construct()
	 */
	public function __construct($dsn, $username = null, $password = null, $driver_options = array())
	{	
		if(isset($driver_options[self::KALTURA_ATTR_NAME]))
			$this->connectionName = $driver_options[self::KALTURA_ATTR_NAME];
			
		$connStart = microtime(true);

		parent::__construct($dsn, $username, $password, $driver_options);

		KalturaLog::debug("conn took - ". (microtime(true) - $connStart). " seconds to $dsn");

		$this->setAttribute(PDO::ATTR_STATEMENT_CLASS, array('KalturaStatement'));
	}

	public function setCommentsEnabled($enabled) 
	{
		$this->enableComments = $enabled;
	}

	protected function getComment() 
	{
		if(!$this->enableComments)
			return '';
			
		if(!self::$comment)
		{
			$uniqueId = new UniqueId();
			self::$comment = (isset($_SERVER["HOSTNAME"]) ? $_SERVER["HOSTNAME"] : '');
			self::$comment .= "[$uniqueId]";
		}
		
		return '/* ' . self::$comment . "[$this->connectionName] */ ";
	}
	
	/* (non-PHPdoc)
	 * @see PropelPDO::prepare()
	 */
	public function prepare($sql, $driver_options = array())
	{
		$comment = $this->getComment();
		$sql = $comment . $sql;
		
		return parent::prepare($sql, $driver_options);
	}
	
	/* (non-PHPdoc)
	 * @see PDO::exec()
	 */
	public function exec($sql)
	{
		KalturaLog::debug($sql);
		
		$comment = $this->getComment();
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
		
		$comment = $this->getComment();
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
