<?php
/**
 * @package Core
 * @subpackage model.data
 */
class accessControlScope
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
	protected $entryId;
	
	/**
	 * @var string
	 */
	protected $userAgent;
	
	/**
	 * Indicates what contexts should be tested 
	 * No contexts means any context
	 * 
	 * @var array of accessControlContextType
	 */
	protected $contexts = array(accessControlContextType::PLAY);
	
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
	 * @param string $v
	 */
	public function setEntryId($v)
	{
		$this->entryId = $v;
	}
	
	/**
	 * @param string $userAgent
	 */
	public function setUserAgent($userAgent) 
	{
		$this->userAgent = $userAgent;
	}

	/**
	 * @param array $contexts array of accessControlContextType
	 */
	public function setContexts(array $contexts) 
	{
		$this->contexts = $contexts;
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
	 * @return string
	 */
	public function getEntryId()
	{
		return $this->entryId;
	}	
	
	/**
	 * @return string the $userAgent
	 */
	public function getUserAgent() 
	{
		return $this->userAgent;
	}

	/**
	 * @return array of accessControlContextType
	 */
	public function getContexts() 
	{
		return $this->contexts;
	}

	/**
	 * @param int $context enum of accessControlContextType
	 * @return bool
	 */
	public function isInContext($context)
	{
		if(!is_array($this->contexts) || !count($this->contexts))
			return true;
			
		return in_array($context, $this->contexts);
	}
}