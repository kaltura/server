<?php
class moderationWrapper extends objectWrapperBase
{
	protected $basic_fields = array ( "id" , "partnerId" );
	
	protected $regular_fields_ext = array ( "objectId" , "objectType" , /*"puserId" , "kuserId" ,*/  "status" , "comments" , 
		"createdAt", "groupId" , "reportCode" );
	
	protected $detailed_fields_ext = array ( ) ;
	
	protected $detailed_objs_ext = array ( /*"kuser" ,*/ "object" , );
	
	protected $objs_cache = array ( ) ;//"kuser" => "kuser,kuserId" , ); 

	protected $updateable_fields = array ( "comments" , /*"puserId" */ "objectType" , "objectId" , "reportCode"  );
	
	protected $updateable_fields_ext = array ( "status"  );
	
	public function describe() 
	{
		return 
			array (
				"display_name" => "Moderation",
				"desc" => ""
			);
	}
	
	public function getUpdateableFields( $level = 1 )
	{
		if ( $level <= 1 )
			return $this->updateable_fields;
		if ( $level == 2)
			return array_merge ( $this->updateable_fields , $this->updateable_fields_ext );
	}	
}
?>