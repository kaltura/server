<?php

/**
 * @package infra
 * @subpackage cache
 */
abstract class kBaseCacheWrapper
{
	private $serializeData;
	
	/**
	 * @param array $config
	 * @return boolean
	 */
	public function init( $config )
	{
		$this->serializeData = isset($config['serializeData']) ? $config['serializeData'] : false;
		return $this->doInit( $config );
	}
	
	/**
	 * Overridable
	 * @see init()
	 */
	abstract protected function doInit($config);
	
	/**
	 * @param string $key
	 * @return mixed or false on error
	 */
	public function get($key)
	{
		$result = $this->doGet( $key );
		
		if ( $result !== false && $this->serializeData )
		{
			$result = @unserialize($result);
		}
		
		return $result;
	}
	
	/**
	 * Overridable
	 * @see get()
	 */
	abstract protected function doGet($key);

	/**
	 * @param string $key
	 * @param mixed $var
	 * @param int $expiry
	 * @return bool false on error
	 */
	public function set($key, $var, $expiry = 0)
	{
		if ( $this->serializeData )
		{
			$var = serialize($var);
		}
		
		return $this->doSet( $key, $var, $expiry );
	}
	
	/**
	 * Overridable
	 * @see set()
	 */
	abstract protected function doSet($key, $var, $expiry = 0);

	/**
	 * @param string $key
	 * @param mixed $var
	 * @param int $expiry
	 * @return bool false on error
	 */
	public function add($key, $var, $expiry = 0)
	{
		if ( $this->serializeData )
		{
			$var = serialize($var);
		}
		
		return $this->doAdd( $key, $var, $expiry );
	}
	
	/**
	 * Overridable
	 * @see add()
	 */
	abstract protected function doAdd($key, $var, $expiry = 0);

	/**
	 * @param string $key
	 * @return bool false on error
	 */
	public function delete( $key )
	{
		return $this->doDelete( $key );		
	}
	
	/**
	 * Overridable
	 * @see delete()
	 */
	abstract protected function doDelete($key);

	/**
	 * @param array $keys
	 * @return array or false on error
	 */
	public function multiGet($keys)
	{
		$result = $this->doMultiGet( $keys );

		// Result needs to be deserialized?
		if ( $result !== false && $this->serializeData )
		{
			if ( is_array( $keys ) )
			{
				$result = array_map( 'unserialize', $result );
			}
			else // Single object
			{
				$result = @unserialize( $result );
			}
		}
		
		return $result;
	}
	
	/**
	 * Overridable
	 * @see multiGet()
	 */
	protected function doMultiGet($keys)
	{
		$result = array();
		
		foreach ($keys as $key)
		{
			// Note: Calling doGet() instead of get() in order to make sure the result is not unserialized.
			//       The result will be unserialized later on in multiGet().
			$curResult = $this->doGet($key);
			
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
	public final function increment($key, $delta = 1)
	{
		if ( $this->serializeData )
		{
			// Force using the base class's implementation if serialization is required 
			$result = $this->baseIncrement($key, $delta);		
		}
		else
		{
			// Use overridable implementation
			$result = $this->doIncrement($key, $delta);
		}
		
		return $result;
	}
		
	/**
	 * Increment using get()/set() in order to make sure serialization properly takes place (if needed)
	 */
	private function baseIncrement($key, $delta = 1)
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
	 * Overridable, base implementation calls baseIncrement()
	 * Note: Applicable in case serialization is not required
	 */
	protected function doIncrement($key, $delta = 1)
	{
		return $this->baseIncrement($key, $delta);
	}
	
	/**
	 * @param string $key
	 * @param int $delta
	 * @return mixed false on error
	 */
	public final function decrement($key, $delta = 1)
	{
		if ( $this->serializeData )
		{
			// If serialization is required, we'll use the base class's implementation
			$result = $this->baseDecrement($key, $delta);
		}
		else
		{
			// Use overridable implementation
			$result = $this->doDecrement($key, $delta);
		}
		
		return $result;
	}
			
	/**
	 * Decrement using get()/set() in order to make sure serialization properly takes place (if needed)
	 */
	public function baseDecrement($key, $delta = 1)
	{
		return $this->increment($key, -$delta);
	}

	/**
	 * Overridable, base implementation calls baseDecrement()
	 */
	protected function doDecrement($key, $delta = 1)
	{
		return $this->baseDecrement($key, $delta);
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
