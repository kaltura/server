<?php
/**
 * @package Core
 * @subpackage model.data
 */
class kScope
{
	/**
	 * @var string
	 */
	protected $referrer;
	
	/**
	 * @var string
	 */
	protected $ip;
	
	/**
	 * @var ks
	 */
	protected $ks;
	
	/**
	 * @var string
	 */
	protected $userAgent;
	
	/**
	 * Unix timestamp (In seconds) to be used to test entry scheduling, keep null to use now.
	 * @var int
	 */
	protected $time;
	
	/**
	 * Unix timestamp (In seconds) to be used to test entry scheduling, keep null to use now.
	 * @var array<kValue>
	 */
	protected $dynamicValues = array();
	
	/**
	 * @var string
	 */
	protected $entryId;
	
	/**
	 * Indicates what contexts should be tested 
	 * No contexts means any context
	 * 
	 * @var array of ContextType
	 */
	protected $contexts = array();
	
	
	public function __construct()
	{
		$this->setIp(requestUtils::getRemoteAddress());
		$this->setReferrer(isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : null);
		$this->setUserAgent(isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : null);	
		$this->setKs(kCurrentContext::$ks_object ? kCurrentContext::$ks_object : null);
	}
	
	/**
	 * @param string $v
	 */
	public function setReferrer($v)
	{
		$this->referrer = $v;
	}
	
	/**
	 * @param string $v
	 */
	public function setIp($v)
	{
		$this->ip = $v;
	}
	
	/**
	 * @param ks $v
	 */
	public function setKs($v)
	{
		if(is_string($v))
			$v = ks::fromSecureString($v);
			
		$this->ks = $v;
	}
	
	/**
	 * @param string $userAgent
	 */
	public function setUserAgent($userAgent) 
	{
		$this->userAgent = $userAgent;
	}

	/**
	 * @return string
	 */
	public function getReferrer()
	{
		return $this->referrer;
	}
	
	/**
	 * @return string
	 */
	public function getIp()
	{
		return $this->ip;
	}
	
	/**
	 * @return ks
	 */
	public function getKs()
	{
		return $this->ks;
	}
	
	/**
	 * @return string the $userAgent
	 */
	public function getUserAgent() 
	{
		return $this->userAgent;
	}

	/**
	 * @return int $time
	 */
	public function getTime() 
	{
		if(is_null($this->time))
		{
			return kApiCache::getTime();
		}
			
		return $this->time;
	}

	/**
	 * @param int $time
	 */
	public function setTime($time) 
	{
		$this->time = $time;
	}

	public function resetDynamicValues() 
	{
		$this->dynamicValues= array();
	}

	/**
	 * @param string $key
	 * @param kValue $value
	 */
	public function addDynamicValue($key, kValue $value) 
	{
		$this->dynamicValues[$key] = $value;
	}

	/**
	 * @param string $key
	 * @return bool wasRemoved
	 */
	public function removeDynamicValue($key)
	{
		if ($this->dynamicValues[$key])
		{
			unset($this->dynamicValues[$key]);
			return true;
		}
		return false;
	}

	/**
	 * @return array
	 */
	public function getDynamicValues($keyPrefix = '', $keySuffix = '')
	{
		$values = array();
		foreach($this->dynamicValues as $key => $value)
		{
			/* @var $value kValue */
			if($value instanceof IScopeField)
				$value->setScope($this);
				
			$dynamicValue = $value->getValue();
			if(is_null($dynamicValue))
				$dynamicValue = '';
				
			$values[$keyPrefix . $key . $keySuffix] = $dynamicValue;
		}
		
		return $values;
	}
	
	/**
	 * @param string $v
	 */
	public function setEntryId($v)
	{
		$this->entryId = $v;
	}
	
	/**
	 * @param array $contexts array of ContextType
	 */
	public function setContexts(array $contexts) 
	{
		$this->contexts = $contexts;
	}
	
	/**
	 * @return string
	 */
	public function getEntryId()
	{
		return $this->entryId;
	}	
	
	/**
	 * @return array of ContextType
	 */
	public function getContexts() 
	{
		return $this->contexts;
	}

	/**
	 * @param int $context enum of ContextType
	 * @return bool
	 */
	public function isInContext($context)
	{
		if(!is_array($this->contexts) || !count($this->contexts))
			return true;
			
		return in_array($context, $this->contexts);
	}
}