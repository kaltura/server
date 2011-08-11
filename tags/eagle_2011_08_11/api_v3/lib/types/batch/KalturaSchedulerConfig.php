<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaSchedulerConfig extends KalturaObject 
{
	/**
	 * The id of the Category
	 * 
	 * @var int
	 * @readonly
	 * @filter eq,in
	 */
	public $id;


	
	/**
	 * Creator name
	 *  
	 * @var string
	 */
	public $createdBy;

	
	/**
	 * Updater name
	 *  
	 * @var string
	 */
	public $updatedBy;


	
	/**
	 * Id of the control panel command that created this config item 
	 *  
	 * @var string
	 */
	public $commandId;


	
	/**
	 * The status of the control panel command 
	 *  
	 * @var string
	 */
	public $commandStatus;


	
	/**
	 * The id of the scheduler 
	 *  
	 * @var int
	 */
	public $schedulerId;


	
	/**
	 * The configured id of the scheduler 
	 *  
	 * @var int
	 */
	public $schedulerConfiguredId;


	
	/**
	 * The name of the scheduler 
	 *  
	 * @var string
	 */
	public $schedulerName;


	
	/**
	 * The id of the job worker
	 *  
	 * @var int
	 */
	public $workerId;


	
	/**
	 * The configured id of the job worker
	 *  
	 * @var int
	 */
	public $workerConfiguredId;


	
	/**
	 * The name of the job worker
	 *  
	 * @var string
	 */
	public $workerName;


	
	/**
	 * The name of the variable
	 *  
	 * @var string
	 */
	public $variable;


	
	/**
	 * The part of the variable
	 *  
	 * @var string
	 */
	public $variablePart;


	
	/**
	 * The value of the variable
	 *  
	 * @var string
	 */
	public $value;
	
	
	private static $mapBetweenObjects = array
	(
		"id",
		"createdBy",
		"updatedBy",
		"commandId",
		"commandStatus",
		"schedulerId",
		"schedulerConfiguredId",
		"schedulerName",
		"workerId",
		"workerConfiguredId",
		"workerName",
		"variable",
		"variablePart",
		"value",
	);
	
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$mapBetweenObjects);
	}
	
	public function getExtraFilters()
	{
		return array();
	}
	
	public function getFilterDocs()
	{
		return array();
	}
}