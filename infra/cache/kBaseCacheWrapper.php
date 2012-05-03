<?php

/**
 * @package infra
 * @subpackage cache
 */
abstract class kBaseCacheWrapper
{	
	/**
	 * @param string $key
	 * @param int $defaultExpiry
	 * @return mixed or false on error
	 */
	abstract public function get($key, $defaultExpiry = 0);
	
	/**
	 * @param array $keys
	 * @param int $defaultExpiry
	 * @return array or false on error
	 */
	public function multiGet($keys, $defaultExpiry = 0)
	{
		$result = array();
		foreach ($keys as $key)
		{
			$curResult = $this->get($key, $defaultExpiry);
			if ($curResult !== false)
			{
				$result[$key] = $curResult;
			}
		}
		return $result;
	}

	/**
	 * @param string $key
	 * @param mixed $var
	 * @param int $expiry
	 * @param int $defaultExpiry
	 */
	abstract public function set($key, $var, $expiry = 0, $defaultExpiry = 0);

	/**
	 * This function is required since this code can run before the autoloader
	 * 
	 * @param string $msg
	 */
	protected static function safeLog($msg)
	{
		if (class_exists('KalturaLog'))
			KalturaLog::debug($msg);
	}
}
