<?php


class ComcastDirectory extends ComcastStatusObject
{				
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			case 'template':
				return 'ComcastArrayOfDirectoryField';
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
	/**
	 * @var ComcastArrayOfDirectoryField
	 **/
	public $template;
				
	/**
	 * @var boolean
	 **/
	public $disabled;
				
	/**
	 * @var boolean
	 **/
	public $grantAccessIfUnavailable;
				
	/**
	 * @var string
	 **/
	public $host;
				
	/**
	 * @var string
	 **/
	public $password;
				
	/**
	 * @var int
	 **/
	public $port;
				
	/**
	 * @var long
	 **/
	public $priority;
				
	/**
	 * @var string
	 **/
	public $scope;
				
	/**
	 * @var string
	 **/
	public $searchPattern;
				
	/**
	 * @var string
	 **/
	public $title;
				
	/**
	 * @var boolean
	 **/
	public $useSSL;
				
	/**
	 * @var string
	 **/
	public $userName;
				
}


