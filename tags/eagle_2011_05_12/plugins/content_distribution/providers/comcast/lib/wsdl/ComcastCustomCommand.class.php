<?php


class ComcastCustomCommand extends ComcastBusinessObject
{				
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			case 'template':
				return 'ComcastArrayOfCustomCommandField';
			case 'requiredCapabilityTypes':
				return 'ComcastArrayOfCapabilityType';
			case 'views':
				return 'ComcastArrayOfAdminView';
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
	/**
	 * @var ComcastArrayOfCustomCommandField
	 **/
	public $template;
				
	/**
	 * @var string
	 **/
	public $URL;
				
	/**
	 * @var string
	 **/
	public $URLPassword;
				
	/**
	 * @var string
	 **/
	public $URLUserName;
				
	/**
	 * @var string
	 **/
	public $confirmationAlert;
				
	/**
	 * @var boolean
	 **/
	public $disabled;
				
	/**
	 * @var long
	 **/
	public $index;
				
	/**
	 * @var int
	 **/
	public $maximumItems;
				
	/**
	 * @var string
	 **/
	public $maximumItemsAlert;
				
	/**
	 * @var int
	 **/
	public $minimumItems;
				
	/**
	 * @var string
	 **/
	public $minimumItemsAlert;
				
	/**
	 * @var boolean
	 **/
	public $onlyForOwnedItems;
				
	/**
	 * @var string
	 **/
	public $onlyForOwnedItemsAlert;
				
	/**
	 * @var boolean
	 **/
	public $openInNewWindow;
				
	/**
	 * @var ComcastArrayOfCapabilityType
	 **/
	public $requiredCapabilityTypes;
				
	/**
	 * @var boolean
	 **/
	public $showAsDialog;
				
	/**
	 * @var boolean
	 **/
	public $showScrollbars;
				
	/**
	 * @var boolean
	 **/
	public $showToReadOnlyUsers;
				
	/**
	 * @var boolean
	 **/
	public $showToStandardUsers;
				
	/**
	 * @var string
	 **/
	public $title;
				
	/**
	 * @var boolean
	 **/
	public $useSelection;
				
	/**
	 * @var ComcastArrayOfAdminView
	 **/
	public $views;
				
	/**
	 * @var int
	 **/
	public $windowHeight;
				
	/**
	 * @var string
	 **/
	public $windowName;
				
	/**
	 * @var int
	 **/
	public $windowWidth;
				
}


