<?php
class kAuditTrailTextInfo extends kAuditTrailInfo
{
	/**
	 * @var string
	 */
	protected $info;
	
	/**
	 * @return the $info
	 */
	public function getInfo() {
		return $this->info;
	}

	/**
	 * @param $info the $info to set
	 */
	public function setInfo($info) {
		$this->info = $info;
	}
}
