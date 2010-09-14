<?php
class BatchJobWrapper extends objectWrapperBase
{

	protected $basic_fields = array ( "id" , "jobType" , "jobSubType"  );
	
	protected $regular_fields_ext = array ( "data" , "status" , "abort" , "progress" , "message", "description" ,  "updatesCount" , "createdAt" , "updatedAt" , "entryId" );
	
	protected $detailed_fields_ext = array ();
	
	protected $detailed_objs_ext = array ( "entry" );
	
	protected $objs_cache = array ( "entry" => "entry,entryId" ); 

	protected $updateable_fields = array ( "data", "status", "abort", "checkAgainTimeout", "progress", "message", "description",
		"updatesCount", "processorExpiration" );
	
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