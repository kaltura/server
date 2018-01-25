
<?php

/**
 * Basic entry vendor task job data 
 *
 * @package plugins.reach
 * @subpackage model
 *
 */
class kEntryVendorTaskJobData
{
	/**
	 *  @var int
	 */
	public $serviceType;
	
	/**
	 * @var int
	 */
	public $serviceFeature;
	
	/**
	 * @var int
	 */
	public $turnAroundTime;
	
	/**
	 * @return the $serviceType
	 */
	public function getServiceType() { return $this->serviceType; }
	
	/**
	 * @return the $serviceFeature
	 */
	public function getServiceFeature() { return $this->serviceFeature; }
	
	/**
	 * @return the $turnAroundTime
	 */
	public function getTurnAroundTime() { return $this->turnAroundTime; }
	
	/**
	 * @param VendorServiceType $serviceType
	 */
	public function setServiceType($serviceType) { $this->serviceType = $serviceType; }
	
	/**
	 * @param VendorServiceFeature $serviceFeature
	 */
	public function setServiceFeature($serviceFeature) { $this->serviceFeature = $serviceFeature; }
	
	/**
	 * @param VendorServiceTurnAroundTime $turnAroundTime
	 */
	public function setTurnAroundTime($turnAroundTime) { $this->turnAroundTime = $turnAroundTime; }
	
}