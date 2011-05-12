<?php
class kAuditTrailChangeItem
{
	/**
	 * @var string
	 */
	protected $descriptor;
	
	/**
	 * @var string
	 */
	protected $oldValue;
	
	/**
	 * @var string
	 */
	protected $newValue;
	
	/**
	 * @return the $descriptor
	 */
	public function getDescriptor() {
		return $this->descriptor;
	}

	/**
	 * @return the $oldValue
	 */
	public function getOldValue() {
		return $this->oldValue;
	}

	/**
	 * @return the $newValue
	 */
	public function getNewValue() {
		return $this->newValue;
	}

	/**
	 * @param $descriptor the $descriptor to set
	 */
	public function setDescriptor($descriptor) {
		$this->descriptor = $descriptor;
	}

	/**
	 * @param $oldValue the $oldValue to set
	 */
	public function setOldValue($oldValue) {
		$this->oldValue = $oldValue;
	}

	/**
	 * @param $newValue the $newValue to set
	 */
	public function setNewValue($newValue) {
		$this->newValue = $newValue;
	}
}
