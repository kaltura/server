<?php

/**
 *
 * @package Core
 * @subpackage model
 */ 
class kUrlRecognizer
{

	const NOT_RECOGNIZED = 0;
	const RECOGNIZED_OK = 1;
	const RECOGNIZED_NOT_OK = 2;

	/**
	 * @var array
	 */
	protected  $hosts;
	
	protected  $uriPrefix;
	
	public function isRecognized($requestOrigin) {
		// Check whether one of the hosts is similar to the request origin
		$hosts = explode(",", $this->getHosts());
		if(!in_array($requestOrigin, $hosts))
			return false;
			
		$uri = $_SERVER["REQUEST_URI"];
		if($this->getUriPrefix() && strpos($uri, $this->getUriPrefix()) !== 0)
			return false;
			
		return true;
	}
	
	/**
	 * @return the $hosts
	 */
	public function getHosts() {
		return $this->hosts;
	}

	/**
	 * @param multitype: $hosts
	 */
	public function setHosts($hosts) {
		$this->hosts = $hosts;
	}
	
	/**
	 * @return the $uriPrefix
	 */
	public function getUriPrefix() {
		return $this->uriPrefix;
	}

	/**
	 * @param field_type $uriPrefix
	 */
	public function setUriPrefix($uriPrefix) {
		$this->uriPrefix = $uriPrefix;
	}


	
}
