<?php

/**
 *
 * @package Core
 * @subpackage model
 */ 
class kUrlRecognizer
{
	/**
	 * @var array
	 */
	protected  $hosts;
	
	protected  $uriPrefix;
	
	function addToken(&$value,$key) {
		$value .= "+token";
	}
	
	public function isRestricted(Partner $partner, $requestOrigin) {
		
		// Check whether one of the hosts is similar to the request origin
		$hosts = explode(",", $this->getHosts());
		if(!in_array($requestOrigin, $hosts))
			return true;
			
		$uri = $_SERVER["REQUEST_URI"];
		if($this->getUriPrefix())
			if(strpos($uri, $this->getUriPrefix()))
				array_walk($hosts,"addToken");
			
		$deliveryRestrictions = $partner->getDeliveryRestrictions();
		$deliveryRestrictionsArr = explode(",", $deliveryRestrictions);
		
		if(count(array_intersect($deliveryRestrictionsArr, $hosts)) > 0)
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
