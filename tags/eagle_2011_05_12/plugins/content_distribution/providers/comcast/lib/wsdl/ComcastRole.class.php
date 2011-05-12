<?php


class ComcastRole extends ComcastBusinessObject
{				
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			case 'template':
				return 'ComcastArrayOfRoleField';
			case 'capabilities':
				return 'ComcastArrayOfCapability';
			case 'externalGroups':
				return 'ComcastArrayOfstring';
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
	/**
	 * @var ComcastArrayOfRoleField
	 **/
	public $template;
				
	/**
	 * @var boolean
	 **/
	public $allowAPICalls;
				
	/**
	 * @var boolean
	 **/
	public $allowConsoleAccess;
				
	/**
	 * @var ComcastArrayOfCapability
	 **/
	public $capabilities;
				
	/**
	 * @var boolean
	 **/
	public $copyToNewAccounts;
				
	/**
	 * @var boolean
	 **/
	public $disabled;
				
	/**
	 * @var ComcastArrayOfstring
	 **/
	public $externalGroups;
				
	/**
	 * @var boolean
	 **/
	public $grantByDefault;
				
	/**
	 * @var boolean
	 **/
	public $grantFutureCapabilities;
				
	/**
	 * @var boolean
	 **/
	public $showHomeTab;
				
	/**
	 * @var string
	 **/
	public $title;
				
}


