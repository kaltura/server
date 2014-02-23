<?php
/**
* @package plugins.drm
* @subpackage model
*/
class PlayReadyCopyRight extends PlayReadyRight
{
	/**
	 * @var int
	 */
	private $copyCount;
	
	/**
	 * @var array of PlayReadyCopyEnablerType
	 */
	private $copyEnablers;
	
	/**
	 * @return the $copyCount
	 */
	public function getCopyCount() {
		return $this->copyCount;
	}

	/**
	 * @return the $copyEnablers
	 */
	public function getCopyEnablers() {
		return $this->copyEnablers;
	}

	/**
	 * @param int $copyCount
	 */
	public function setCopyCount($copyCount) {
		$this->copyCount = $copyCount;
	}

	/**
	 * @param array $copyEnablers
	 */
	public function setCopyEnablers($copyEnablers) {
		$this->copyEnablers = $copyEnablers;
	} 
}