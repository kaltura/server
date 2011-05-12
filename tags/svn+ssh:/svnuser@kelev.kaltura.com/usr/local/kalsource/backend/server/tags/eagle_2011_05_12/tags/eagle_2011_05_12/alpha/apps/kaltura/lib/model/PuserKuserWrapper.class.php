<?php
class PuserKuserWrapper extends objectWrapperBase
{
	// the PuserKuser id make more harm than good - 
	protected $basic_fields = array ( /*"id", */ "puserName" , "partnerId" , "subpId" );
	
	protected $regular_fields_ext = array ( "puserId" , "kuserId" , "customData" ,  "context" ,  "createdAt" );
	
	protected $detailed_fields_ext = array (  ) ;
	
	protected $detailed_objs_ext = array ( "kuser" );
	
	protected $objs_cache = array ( "kuser" => "kuser,kuserId" , );

	public function getUpdateableFields()
	{
		return array ( );
	}		
}
?>