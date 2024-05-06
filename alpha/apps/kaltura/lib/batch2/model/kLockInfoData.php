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
	 * @param int $lockVersion
	 */
	public function setLockVersion($lockVersion) {
		$this->lockVersion = $lockVersion;
	}

	/**
	 * @return int $urgency
	 */
	public function getUrgency() {
		return $this->urgency;
	}

	/**
	 * @return int $estimatedEffort
	 */
	public function getEstimatedEffort() {
		return $this->estimatedEffort;
	}

	/**
	 * @param int $urgency
	 */
	public function setUrgency($urgency) {
		$this->urgency = $urgency;
	}

	/**
	 * @param int $estimatedEffort
	 */
	public function setEstimatedEffort($estimatedEffort) {
		$this->estimatedEffort = $estimatedEffort;
	}
	
	/**
	 * @return int $priority
	 */
	public function getPriority() {
		return $this->priority;
	}
	
	/**
	 * @param int $priority
	 */
	public function setPriority($priority) {
		$this->priority = $priority;
	}
	
}
