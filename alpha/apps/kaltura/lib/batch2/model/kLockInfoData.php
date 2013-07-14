<?php
/**
 * @package Core
 * @subpackage model.data
 */
class kLockInfoData 
{
	protected $lockVersion = 0;
	
	protected $urgency;
	
	protected $estimatedEffort;
	
	protected $priority;
	
	/**
	 * @return the $lockVersion
	 */
	public function getLockVersion() {
		return $this->lockVersion;
	}

	/**
	 * @param field_type $lockVersion
	 */
	public function setLockVersion($lockVersion) {
		$this->lockVersion = $lockVersion;
	}

	/**
	 * @return the $urgency
	 */
	public function getUrgency() {
		return $this->urgency;
	}

	/**
	 * @return the $estimatedEffort
	 */
	public function getEstimatedEffort() {
		return $this->estimatedEffort;
	}

	/**
	 * @param field_type $urgency
	 */
	public function setUrgency($urgency) {
		$this->urgency = $urgency;
	}

	/**
	 * @param field_type $estimatedEffort
	 */
	public function setEstimatedEffort($estimatedEffort) {
		$this->estimatedEffort = $estimatedEffort;
	}
	
	/**
	 * @return the $priority
	 */
	public function getPriority() {
		return $this->priority;
	}
	
	/**
	 * @param field_type $priority
	 */
	public function setPriority($priority) {
		$this->priority = $priority;
	}
	
}
