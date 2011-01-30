<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaControlPanelCommand extends KalturaObject implements IFilterable
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
	 * Creation date as Unix timestamp (In seconds)
	 *  
	 * @var int
	 * @readonly
	 * @filter gte,lte,order
	 */
	public $createdAt;


	
	/**
	 * Creator name
	 *  
	 * @var string
	 */
	public $createdBy;


	
	/**
	 * Update date as Unix timestamp (In seconds)
	 *  
	 * @var int
	 * @readonly
	 * @filter order
	 */
	public $updatedAt;


	
	/**
	 * Updater name
	 *  
	 * @var string
	 */
	public $updatedBy;


	
	/**
	 * Creator id
	 *  
	 * @var int
	 * @filter eq
	 */
	public $createdById;


	
	/**
	 * The id of the scheduler that the command refers to
	 *  
	 * @var int
	 */
	public $schedulerId;


	
	/**
	 * The id of the scheduler worker that the command refers to
	 *  
	 * @var int
	 */
	public $workerId;


	
	/**
	 * The id of the scheduler worker as configured in the ini file
	 *  
	 * @var int
	 */
	public $workerConfiguredId;


	
	/**
	 * The name of the scheduler worker that the command refers to
	 *  
	 * @var int
	 */
	public $workerName;


	
	/**
	 * The index of the batch process that the command refers to
	 *  
	 * @var int
	 */
	public $batchIndex;

	
	/**
	 * The command type - stop / start / config
	 *  
	 * @var KalturaControlPanelCommandType
	 * @filter eq,in
	 */
	public $type;
	
	
	/**
	 * The command target type - data center / scheduler / job / job type
	 *  
	 * @var KalturaControlPanelCommandTargetType
	 * @filter eq,in
	 */
	public $targetType;


	
	/**
	 * The command status
	 *  
	 * @var KalturaControlPanelCommandStatus
	 * @filter eq,in
	 */
	public $status;


	
	/**
	 * The reason for the command
	 *  
	 * @var string
	 */
	public $cause;


	
	/**
	 * Command description
	 *  
	 * @var string
	 */
	public $description;


	
	/**
	 * Error description
	 *  
	 * @var string
	 */
	public $errorDescription;

	
	
	private static $mapBetweenObjects = array
	(
		"id",
		"createdAt",
		"createdBy",
		"updatedAt",
		"updatedBy",
		"createdById",
		"schedulerId",
		"workerId",
		"workerConfiguredId",
		"workerName",
		"batchIndex",
		"type",
		"targetType",
		"status",
		"cause",
		"description",
		"errorDescription",
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