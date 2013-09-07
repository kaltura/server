<?php
require_once(__DIR__ . '/WebexXmlObject.class.php');

class WebexXmlGetAPIVersion extends WebexXmlObject
{
	/**
	 *
	 * @var string
	 */
	protected $apiVersion;
	
	/**
	 *
	 * @var string
	 */
	protected $release;
	
	/**
	 *
	 * @var string
	 */
	protected $trainReleaseVersion;
	
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'apiVersion':
				return 'string';
	
			case 'release':
				return 'string';
	
			case 'trainReleaseVersion':
				return 'string';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/**
	 * @return string $apiVersion
	 */
	public function getApiVersion()
	{
		return $this->apiVersion;
	}
	
	/**
	 * @return string $release
	 */
	public function getRelease()
	{
		return $this->release;
	}
	
	/**
	 * @return string $trainReleaseVersion
	 */
	public function getTrainReleaseVersion()
	{
		return $this->trainReleaseVersion;
	}
	
}

