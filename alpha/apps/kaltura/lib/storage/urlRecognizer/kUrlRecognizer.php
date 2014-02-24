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

}
