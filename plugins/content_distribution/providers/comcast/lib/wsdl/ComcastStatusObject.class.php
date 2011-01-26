<?php


abstract class ComcastStatusObject extends ComcastBusinessObject
{				
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			case 'status':
				return 'ComcastStatus';
			case 'statusDetail':
				return 'ComcastStatusDetail';
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
	/**
	 * @var boolean
	 **/
	public $refreshStatus;
				
	/**
	 * @var ComcastStatus
	 **/
	public $status;
				
	/**
	 * @var string
	 **/
	public $statusDescription;
				
	/**
	 * @var ComcastStatusDetail
	 **/
	public $statusDetail;
				
	/**
	 * @var string
	 **/
	public $statusMessage;
				
}


