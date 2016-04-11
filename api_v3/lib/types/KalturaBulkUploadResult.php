<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaBulkUploadResult extends KalturaObject
{
    /**
     * The id of the result
     * 
     * @var int
	 * @readonly
     */
    public $id;

	/**
	 * The id of the parent job
	 * 
     * @var bigint
     */
    public $bulkUploadJobId;

	/**
	 * The index of the line in the CSV
	 * 
     * @var int
     */
    public $lineIndex;

	/**
     * @var int
     */
    public $partnerId;

    /**
     * @var KalturaBulkUploadResultStatus
     */
    public $status;
	
	/**
     * @var KalturaBulkUploadAction
     */
    public $action;


	/**
     * @var string
     */
    public $objectId;
    
    /**
     * @var int
     */
    public $objectStatus;


	/**
     * @var KalturaBulkUploadObjectType
     */
    public $bulkUploadResultObjectType;

	/**
	 * The data as recieved in the csv
	 * 
     * @var string
     */
    public $rowData;

	/**
     * @var string
     */
    public $partnerData;

	/**
     * @var string
     */
    public $objectErrorDescription;

	/**
     * @var KalturaBulkUploadPluginDataArray
     */
    public $pluginsData;
    
    /**
     * @var string
     */
    public $errorDescription;
    
    /**
     * @var string
     */
    public $errorCode;
    
    /**
     * @var int
     */
    public $errorType;
    
    
	private static $mapBetweenObjects = array
	(
		"id",
	    "bulkUploadJobId",
	    "lineIndex",
	    "partnerId",
		"status",
		"action",
		"objectId",
		"bulkUploadResultObjectType" => "objectType",
	    "objectStatus",
	    "rowData",
	    "partnerData",
	    "objectErrorDescription",
	    "errorDescription",
	    "errorCode",
	    "errorType",
	);
	
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$mapBetweenObjects);
	}
	
	public function toInsertableObject ( $object_to_fill = null , $props_to_skip = array() )
	{
	    if(is_null($object_to_fill))
	    {
			KalturaLog::alert("No object returned from toInsertableObject, object_to_fill [" . get_class($object_to_fill) . "], this [" . get_class($this) . "] line [" . __LINE__ . "]");
			return null;
	    }
	        
		$dbObject = parent::toInsertableObject($object_to_fill, $props_to_skip);
		if(!$dbObject)
		{
			KalturaLog::alert("No object returned from toInsertableObject, object_to_fill [" . get_class($object_to_fill) . "], this [" . get_class($this) . "] line [" . __LINE__ . "]");
			return null;
		}
		
		$pluginsData = $this->createPluginDataMap();
		$dbObject->setPluginsData($pluginsData);
		
		return $dbObject;
	}
	
	/**
	 * @return array
	 */
	protected function createPluginDataMap ()
	{
	    $pluginsData = array();
	    if($this->pluginsData && $this->pluginsData instanceof KalturaBulkUploadPluginDataArray)
		{
			foreach($this->pluginsData as $data)
			{
				if($data instanceof KalturaBulkUploadPluginData)
					$pluginsData[$data->field] = $data->value;
			}
		}
		KalturaLog::debug("Plugins data array:\n" . print_r($pluginsData, true));
		return $pluginsData;
	}
}