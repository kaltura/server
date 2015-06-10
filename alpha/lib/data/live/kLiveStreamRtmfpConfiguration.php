<?php
class kLiveStreamRtmfpConfiguration extends kLiveStreamConfiguration
{
	/**
	 * @var string
	 */
	protected $groupspec;
	
	/**
	 * @var string
	 */
	protected $multicastStreamName;
	/**
	 * @return the $groupspec
	 */
	public function getGroupspec() {
		return $this->groupspec;
	}

	/**
	 * @return the $multicastStreamName
	 */
	public function getMulticastStreamName() {
		return $this->multicastStreamName;
	}

	/**
	 * @param string $groupspec
	 */
	public function setGroupspec($groupspec) {
		$this->groupspec = $groupspec;
	}

	/**
	 * @param string $multicastStreamName
	 */
	public function setMulticastStreamName($multicastStreamName) {
		$this->multicastStreamName = $multicastStreamName;
	}

	


}