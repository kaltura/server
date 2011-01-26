<?php


class ComcastSystemStatus extends ComcastBusinessObject
{				
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			case 'template':
				return 'ComcastArrayOfSystemStatusField';
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
	/**
	 * @var ComcastArrayOfSystemStatusField
	 **/
	public $template;
				
	/**
	 * @var dateTime
	 **/
	public $buildDate;
				
	/**
	 * @var dateTime
	 **/
	public $currentDate;
				
	/**
	 * @var long
	 **/
	public $queuedConnections;
				
	/**
	 * @var string
	 **/
	public $rootAccount;
				
	/**
	 * @var long
	 **/
	public $rootAccountID;
				
	/**
	 * @var string
	 **/
	public $serverAddress;
				
	/**
	 * @var string
	 **/
	public $serverName;
				
	/**
	 * @var string
	 **/
	public $softwareVersion;
				
	/**
	 * @var dateTime
	 **/
	public $startDate;
				
	/**
	 * @var long
	 **/
	public $upTime;
				
	/**
	 * @var string
	 **/
	public $upTimeWithUnits;
				
	/**
	 * @var float
	 **/
	public $usageTrackingLoad;
				
	/**
	 * @var string
	 **/
	public $webXML;
				
}


