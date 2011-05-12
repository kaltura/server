<?php

class KalturaTestDataConfiguration extends KalturaTestDataBase
{
	/**
	 * 
	 * The test configuration additional data
	 * @var array - key => value pairs
	 */
	private $additionalData = array();
	
	/**
	 * 
	 * @return additionalData - the test configuration additional data array
	 */
	public function getAdditionalData()
	{
		return $this->additionalData;
	}

	/**
	 * 
	 * Sets the additional data array
	 * @param array $additionalData
	 */
	public function setAdditionalData(array $additionalData)
	{
		$this->additionalData = $additionalData;
	}
}