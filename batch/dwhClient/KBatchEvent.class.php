<?php
require_once("bootstrap.php");

/*
 * 
 *   --------- This is strongly coupled with the ETL op_mon/load_batch_events. 
 * 	Hopefully will be genrated together with the ds_events table events table, and ETL CSV file structure ,--------- 
	,batch_client_version	varchar(20)
	,batch_event_type_id	smallint
	,batch_name	varchar(50)
	,batch_event_time	datetime
	,batch_session_id	varchar(50)
	,batch_type smallint
	,host_name	varchar(20)
	,location_id	int
	,section_id	int
	,batch_id	int
	,partner_id	int
	,entry_id varchar(20)
	,bulk_upload_id int
	,batch_parant_id int
	,batch_root_id int
	,batch_status smallint
	,batch_progress int
	,value_1 int
	,value_2 string
 * 
 * @package Scheduler
 * @subpackage DWH
 */
class KBatchEvent extends KDwhEventBase
{
	const EVENT_JOB_UPDATE = 1;
	const EVENT_JOB_FREE = 2;
	const EVENT_BATCH_UP = 3;
	const EVENT_BATCH_DOWN = 4;
	const EVENT_FILE_EXISTS = 5;
	const EVENT_FILE_DOESNT_EXIST = 6;
	const EVENT_KILLER_FILE_DOESNT_EXIST = 7;
	const EVENT_KILLER_FILE_IDLE = 8;
	const EVENT_KILLER_UP = 9;
	
	public $batch_client_version = null; 	// varchar(20)
	public $batch_event_type_id = null; 	// smallint
	public $batch_name = null; 				// varchar(50)
	public $batch_event_time = null;		// datetime
	public $batch_session_id = null;		// varchar(50)
	public $batch_type = null;				// smallint
	public $host_name = null;				// varchar(20)
	public $location_id = null;				// int
	public $section_id = null;				// int
	public $batch_id = null;				// int
	public $partner_id = null;				// int
	public $entry_id = null;				// varchar(20)
	public $bulk_upload_id = null;			// int 
	public $batch_parant_id = null;			// int
	public $batch_root_id = null; 			// int
	public $batch_status = null; 			// smallint
	public $batch_progress = null; 			// int
	public $value_1 = null; 				// int
	public $value_2 = null; 				// string
		
	public function toEventLine()
	{
		$str = 
			$this->batch_client_version . self::EVENT_FIELD_SEPARATOR .
			$this->batch_event_type_id . self::EVENT_FIELD_SEPARATOR .
			$this->batch_name . self::EVENT_FIELD_SEPARATOR .
			$this->batch_event_time . self::EVENT_FIELD_SEPARATOR .
			$this->batch_session_id . self::EVENT_FIELD_SEPARATOR .
			$this->batch_type . self::EVENT_FIELD_SEPARATOR .
			$this->host_name . self::EVENT_FIELD_SEPARATOR .
			$this->location_id . self::EVENT_FIELD_SEPARATOR .
			$this->section_id . self::EVENT_FIELD_SEPARATOR .
			$this->batch_id . self::EVENT_FIELD_SEPARATOR .
			$this->partner_id . self::EVENT_FIELD_SEPARATOR .
			$this->entry_id	. self::EVENT_FIELD_SEPARATOR .
			$this->bulk_upload_id . self::EVENT_FIELD_SEPARATOR .
			$this->batch_parant_id . self::EVENT_FIELD_SEPARATOR .
			$this->batch_root_id . self::EVENT_FIELD_SEPARATOR .
			$this->batch_status . self::EVENT_FIELD_SEPARATOR .
			$this->batch_progress . self::EVENT_FIELD_SEPARATOR .
			$this->value_1  . self::EVENT_FIELD_SEPARATOR .
			$this->value_2 
			. self::EVENT_LINE_DELIMITER;	
		return $str;	
	}
}
?>