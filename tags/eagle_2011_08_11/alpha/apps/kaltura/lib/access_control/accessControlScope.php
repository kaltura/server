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
	public function setUserAgent($userAgent) {
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
	 * @return string
	 */
	public function getEntryId()
	{
		return $this->entryId;
	}	
	
	/**
	 * @return string the $userAgent
	 */
	public function getUserAgent() {
		return $this->userAgent;
	}

	/**
	 * @return accessControlScope
	 */
	public static function partialInit()
	{
		$scope = new accessControlScope();
		$scope->setIp(requestUtils::getRemoteAddress());
		$scope->setReferrer(isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : null);
		$scope->setUserAgent(isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : null);
		return $scope;
	}
	
	
}