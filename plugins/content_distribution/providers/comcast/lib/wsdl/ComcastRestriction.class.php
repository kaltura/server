<?php


class ComcastRestriction extends ComcastBusinessObject
{				
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			case 'template':
				return 'ComcastArrayOfRestrictionField';
			case 'availableTimeUnits':
				return 'ComcastTimeUnits';
			case 'delivery':
				return 'ComcastDelivery';
			case 'expirationTimeUnits':
				return 'ComcastTimeUnits';
			case 'limitToEndUserNames':
				return 'ComcastArrayOfstring';
			case 'limitToExternalGroups':
				return 'ComcastArrayOfstring';
			case 'retentionTimeUnits':
				return 'ComcastTimeUnits';
			case 'unapproveTimeUnits':
				return 'ComcastTimeUnits';
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
	/**
	 * @var ComcastArrayOfRestrictionField
	 **/
	public $template;
				
	/**
	 * @var dateTime
	 **/
	public $availableDate;
				
	/**
	 * @var long
	 **/
	public $availableTime;
				
	/**
	 * @var ComcastTimeUnits
	 **/
	public $availableTimeUnits;
				
	/**
	 * @var ComcastDelivery
	 **/
	public $delivery;
				
	/**
	 * @var boolean
	 **/
	public $disabled;
				
	/**
	 * @var dateTime
	 **/
	public $expirationDate;
				
	/**
	 * @var long
	 **/
	public $expirationTime;
				
	/**
	 * @var ComcastTimeUnits
	 **/
	public $expirationTimeUnits;
				
	/**
	 * @var ComcastArrayOfstring
	 **/
	public $limitToEndUserNames;
				
	/**
	 * @var ComcastArrayOfstring
	 **/
	public $limitToExternalGroups;
				
	/**
	 * @var boolean
	 **/
	public $requireDRM;
				
	/**
	 * @var dateTime
	 **/
	public $retentionDate;
				
	/**
	 * @var long
	 **/
	public $retentionTime;
				
	/**
	 * @var ComcastTimeUnits
	 **/
	public $retentionTimeUnits;
				
	/**
	 * @var string
	 **/
	public $title;
				
	/**
	 * @var dateTime
	 **/
	public $unapproveDate;
				
	/**
	 * @var long
	 **/
	public $unapproveTime;
				
	/**
	 * @var ComcastTimeUnits
	 **/
	public $unapproveTimeUnits;
				
	/**
	 * @var boolean
	 **/
	public $useAirdate;
				
}


