<?php


class ComcastJob extends ComcastStatusObject
{				
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			case 'template':
				return 'ComcastArrayOfJobField';
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
	/**
	 * @var ComcastArrayOfJobField
	 **/
	public $template;
				
	/**
	 * @var boolean
	 **/
	public $automaticallyDelete;
				
	/**
	 * @var boolean
	 **/
	public $hasFailedTasks;
				
	/**
	 * @var boolean
	 **/
	public $processInOrder;
				
	/**
	 * @var boolean
	 **/
	public $ready;
				
	/**
	 * @var int
	 **/
	public $tasksRemaining;
				
	/**
	 * @var string
	 **/
	public $title;
				
}


