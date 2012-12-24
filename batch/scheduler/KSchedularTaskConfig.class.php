<?php
/**
 * @package Scheduler
 */
class KSchedularTaskConfig extends Zend_Config_Ini
{
	public function __construct($configFileName, $workerName, $maxInstances)
	{
		parent::__construct($configFileName, $workerName, true);
	
		$this->name = $workerName;
		$this->maxInstances = $maxInstances;
		
		if($this->filter)
		{
			$filter = new KalturaBatchJobFilter();
			foreach($this->filter as $attr => $value)
				$filter->$attr = $value;
				
			$this->filter = $filter;
		}
	}
	
	public function getTaskIndex()
	{
		return $this->taskIndex;
	}
	
	/**
	 * @param $maxIdleTime the $maxIdleTime to set
	 */
	public function setMaxIdleTime($maxIdleTime)
	{
		$this->maxIdleTime = $maxIdleTime;
	}

	/**
	 * @return the $maxIdleTime
	 */
	public function getMaxIdleTime()
	{
		return $this->maxIdleTime;
	}
	
	/**
	 * @param $initOnly the $initOnly to set
	 */
	public function setInitOnly($initOnly)
	{
		$this->initOnly = $initOnly;
	}

	/**
	 * @return the $initOnly
	 */
	public function isInitOnly()
	{
		return $this->initOnly;
	}

	/**
	 * @param $dwhEnabled the $dwhEnabled to set
	 */
	public function setDwhEnabled($dwhEnabled)
	{
		if(is_null($this->dwhEnabled))
			$this->dwhEnabled = $dwhEnabled;
	}

	/**
	 * @param $dwhPath the $dwhPath to set
	 */
	public function setDwhPath($dwhPath)
	{
		if(is_null($this->dwhPath))
			$this->dwhPath = $dwhPath;
	}

	/**
	 * @return the $dwhEnabled
	 */
	public function getDwhEnabled()
	{
		return $this->dwhEnabled;
	}

	/**
	 * @return the $dwhPath
	 */
	public function getDwhPath()
	{
		return $this->dwhPath;
	}
		
	/**
	 * @param $timezone the $timezone to set
	 */
	public function setTimezone($timezone)
	{
		if(is_null($this->timezone))
			$this->timezone = $timezone;
	}

	/**
	 * @return the $timezone
	 */
	public function getTimezone()
	{
		return $this->timezone;
	}
	
	public function setTaskIndex($taskIndex)
	{
		$this->taskIndex = $taskIndex;
	}

	public function getSchedulerName()
	{
		return $this->schedulerName;
	}
	
	public function setSchedulerName($schedulerName)
	{
		$this->schedulerName = $schedulerName;
	}
	
	public function getSchedulerId()
	{
		return $this->schedulerId;
	}
	
	public function setSchedulerId($schedulerId)
	{
		$this->schedulerId = $schedulerId;
	}
	
	public function getPartnerId()
	{
		return $this->partnerId;
	}
	
	public function setPartnerId($partnerId)
	{
		if(is_null($this->partnerId))
			$this->partnerId = $partnerId;
	}

	public function getServiceUrl()
	{
		return $this->serviceUrl;
	}
	
	public function setServiceUrl($serviceUrl)
	{
		if(is_null($this->serviceUrl))
			$this->serviceUrl = $serviceUrl;
	}

	public function getSecret()
	{
		return $this->secret;
	}
	
	public function setSecret($secret)
	{
		if(is_null($this->secret))
			$this->secret = $secret;
	}

	public function getCurlTimeout()
	{
		return $this->curlTimeout;
	}
	
	public function setCurlTimeout($curlTimeout)
	{
		if(is_null($this->curlTimeout))
			$this->curlTimeout = $curlTimeout;
	}
}
