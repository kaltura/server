<?php
/**
 *  @package infra
 *  @subpackage DB
 */
class KalturaPDO extends PropelPDO
{
	public function __construct($dsn, $username = null, $password = null, $driver_options = array())
	{
		parent::__construct($dsn, $username, $password, $driver_options);
		$this->setAttribute(PDO::ATTR_STATEMENT_CLASS, array('KalturaStatement'));
	}
	
	public function exec($sql)
	{
		KalturaLog::debug($sql);
		return parent::exec($sql);
	}

	public function query()
	{
		$args	= func_get_args();
		
		$sql = $args[0];
		KalturaLog::debug($sql);
		
		if (version_compare(PHP_VERSION, '5.3', '<'))
			return call_user_func_array(array($this, 'parent::query'), $args);
		
		return call_user_func_array('parent::query', $args);
	}
}
