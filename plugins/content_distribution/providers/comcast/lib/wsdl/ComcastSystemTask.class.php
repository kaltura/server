<?php


class ComcastSystemTask extends ComcastStatusObject
{				
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			case 'template':
				return 'ComcastArrayOfSystemTaskField';
			case 'contentClass':
				return 'ComcastContentClass';
			case 'diagnostics':
				return 'ComcastArrayOfstring';
			case 'taskType':
				return 'ComcastTaskType';
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
	/**
	 * @var ComcastArrayOfSystemTaskField
	 **/
	public $template;
				
	/**
	 * @var ComcastContentClass
	 **/
	public $contentClass;
				
	/**
	 * @var long
	 **/
	public $contentID;
				
	/**
	 * @var string
	 **/
	public $contentOwner;
				
	/**
	 * @var long
	 **/
	public $contentOwnerAccountId;
				
	/**
	 * @var string
	 **/
	public $contentTitle;
				
	/**
	 * @var string
	 **/
	public $destination;
				
	/**
	 * @var string
	 **/
	public $destinationLocation;
				
	/**
	 * @var ComcastArrayOfstring
	 **/
	public $diagnostics;
				
	/**
	 * @var int
	 **/
	public $failedAttempts;
				
	/**
	 * @var string
	 **/
	public $item;
				
	/**
	 * @var string
	 **/
	public $job;
				
	/**
	 * @var long
	 **/
	public $jobID;
				
	/**
	 * @var int
	 **/
	public $percentComplete;
				
	/**
	 * @var boolean
	 **/
	public $refresh;
				
	/**
	 * @var string
	 **/
	public $requiredServiceToken;
				
	/**
	 * @var string
	 **/
	public $serviceToken;
				
	/**
	 * @var string
	 **/
	public $source;
				
	/**
	 * @var string
	 **/
	public $sourceLocation;
				
	/**
	 * @var ComcastTaskType
	 **/
	public $taskType;
				
}


