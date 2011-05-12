<?php
class kAuditTrailFileSyncCreateInfo extends kAuditTrailInfo
{
	/**
	 * @var string
	 */
	protected $version;

	/**
	 * @var int
	 */
	protected $objectSubType;

	/**
	 * @var int
	 */
	protected $dc;

	/**
	 * @var bool
	 */
	protected $original;

	/**
	 * @var int
	 */
	protected $fileType;
	
	/**
	 * @return the $version
	 */
	public function getVersion() {
		return $this->version;
	}

	/**
	 * @return the $objectSubType
	 */
	public function getObjectSubType() {
		return $this->objectSubType;
	}

	/**
	 * @return the $dc
	 */
	public function getDc() {
		return $this->dc;
	}

	/**
	 * @return the $original
	 */
	public function getOriginal() {
		return $this->original;
	}

	/**
	 * @return the $fileType
	 */
	public function getFileType() {
		return $this->fileType;
	}

	/**
	 * @param $version the $version to set
	 */
	public function setVersion($version) {
		$this->version = $version;
	}

	/**
	 * @param $objectSubType the $objectSubType to set
	 */
	public function setObjectSubType($objectSubType) {
		$this->objectSubType = $objectSubType;
	}

	/**
	 * @param $dc the $dc to set
	 */
	public function setDc($dc) {
		$this->dc = $dc;
	}

	/**
	 * @param $original the $original to set
	 */
	public function setOriginal($original) {
		$this->original = $original;
	}

	/**
	 * @param $fileType the $fileType to set
	 */
	public function setFileType($fileType) {
		$this->fileType = $fileType;
	}
}
