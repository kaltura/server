<?php
/**
 *  @package infra
 *  @subpackage DB
 */
class KalturaPDO extends PropelPDO
{
	/* (non-PHPdoc)
	 * @see PDO::__construct()
	 */
	public function __construct($dsn, $username = null, $password = null, $driver_options = array())
	{
		parent::__construct($dsn, $username, $password, $driver_options);
		$this->setAttribute(PDO::ATTR_STATEMENT_CLASS, array('KalturaStatement'));
	}
	
	/* (non-PHPdoc)
	 * @see PropelPDO::prepare()
	 */
	public function prepare($sql, $driver_options = array())
	{
		$comment = KalturaStatement::getComment();
		$sql = "/* $comment */ $sql";
		
		return parent::prepare($sql, $driver_options);
	}
	
	/* (non-PHPdoc)
	 * @see PDO::exec()
	 */
	public function exec($sql)
	{
		KalturaLog::debug($sql);
		
		$comment = KalturaStatement::getComment();
		$sql = "/* $comment */ $sql";
		
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
		KalturaLog::logByType($sql, KalturaLog::LOG_TYPE_TESTS);
		
		$comment = KalturaStatement::getComment();
		$sql = "/* $comment */ $sql";
		
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
