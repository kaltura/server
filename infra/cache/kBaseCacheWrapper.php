<?php

/**
 * @package infra
 * @subpackage cache
 */
abstract class kBaseCacheWrapper
{	
	/**
	 * @param string $key
	 * @return mixed or false on error
	 */
	abstract public function get($key);
	
	/**
	 * @param string $key
	 * @param mixed $var
	 * @param int $expiry
	 * @return bool false on error
	 */
	abstract public function set($key, $var, $expiry = 0);

	/**
	 * @param string $key
	 * @return bool false on error
	 */
	abstract public function delete($key);
	
	/**
	 * @param array $keys
	 * @return array or false on error
	 */
	public function multiGet($keys)
	{
		$result = array();
		foreach ($keys as $key)
		{
			$curResult = $this->get($key);
			if ($curResult !== false)
			{
				$result[$key] = $curResult;
			}
		}
		return $result;
	}

	/**
	 * @param string $key
	 * @param int $delta
	 * @return mixed false on error
	 */
	public function increment($key, $delta = 1)
	{
		$curVal = $this->get($key);
		if ($curVal === false)
			return false;
		$curVal += $delta;
		if ($this->set($key, $curVal) === false)
			return false;
		return $curVal;
	}
	
	/**
	 * @param string $key
	 * @param int $delta
	 * @return mixed false on error
	 */
	public function decrement($key, $delta = 1)
	{
		return $this->increment($key, -$delta);
	}

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
