<?php
/**
 * @package Core
 * @subpackage model.wrappers
 */
class BatchJobWrapper extends objectWrapperBase
{

	protected $basic_fields = array ( "id" , "jobType" , "jobSubType"  );
	
	protected $regular_fields_ext = array ( "data" , "status" , "executionStatus" , "message", "description" , "createdAt" , "updatedAt" , "entryId" );
	
	protected $detailed_fields_ext = array ();
	
	protected $detailed_objs_ext = array ( "entry" );
	
	protected $objs_cache = array ( "entry" => "entry,entryId" ); 

	protected $updateable_fields = array ( "data", "status", "executionStatus", "checkAgainTimeout", "message", "description",
		"processorExpiration" );
	
	public function getUpdateableFields()
	{
		return $this->updateable_fields;
	}
	
	public function describe() 
	{
		return 
			array (
				"display_name" => "BatchJob",
				"desc" => ""
			);
	}
}
?>