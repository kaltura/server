<?php
/**
 * @package Core
 * @subpackage model
 */
class kExtendedFeature
{
	/**
	 * @var int
	 */
	protected $extendedFeature;
	
	/**
	 * @return the $extendedFeature
	 */
	public function getExtendedFeature() {
		return $this->extendedFeature;
	}

	/**
	 * @param int $extendedFeature
	 */
	public function setExtendedFeature($extendedFeature) {
		$this->extendedFeature = $extendedFeature;
	}

}