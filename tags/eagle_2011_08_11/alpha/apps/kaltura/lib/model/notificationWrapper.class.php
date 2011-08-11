<?php
class notificationWrapper extends objectWrapperBase
{
	protected $basic_fields = array ( "id" , "partnerId" );
	
	protected $regular_fields_ext = array ( "puserId" , "type" , "typeAsString" ,  "status" , "numberOfAttempts" , "createdAt" , "notificationResult" , 
		"objectInfo" );
	
	protected $detailed_fields_ext = array ( ) ;
	
	protected $detailed_objs_ext = array ( );
	
	protected $objs_cache = array ( ) ;//"kuser" => "kuser,kuserId" , ); 

	protected $updateable_fields = array ( "id" , "status" , "notificationResult"  );
	
	public function describe() 
	{
		return 
			array (
				"display_name" => "Notification",
				"desc" => ""
			);
	}
	
	public function getUpdateableFields()
	{
		return $this->updateable_fields;
	}		
}
?>