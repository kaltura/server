<?php
class kshowWrapper extends objectWrapperBase
{
	protected $basic_fields = array ( "id" , "name" , "partnerId" , "subpId" );
	
	protected $regular_fields_ext = array ( "puserId" , "tags" , "description" , "status" , "type" ,  "formatType" , 
		"mediaType" ,"introId" , "showEntryId" , "createdAt" , "createdAtAsInt" , "indexedCustomData3" ,
		/*"producerId" ,*/ "version" , "groupId" ,"permissions" , "partnerData" , "thumbnailUrl" , "allowQuickEdit" );
	
	protected $detailed_fields_ext = array (  "plays" , "views" , "votes" , "comments" , "favorites" , "rank" , "entries" , "contributors" , "subscribers", "lengthInMsecs") ;
	
	protected $detailed_objs_ext = array ( "kuser" , "intro" , "showEntry" ,  "entrys" );
	
	// for a list of all objects - this is MUCH heavier than and single object (can be a long list)
	protected $detailed_obj_lists_ext = array ( "entrys" );
	
	protected $objs_cache = array ( /*"kuser" => "kuser,producerId" , 
		"intro" => "entry,introId" , 
		"showEntry" => "entry,showEntryId" ,  
		"entrys" => "*entry,id"*/);

	protected $updateable_fields = array ( "name"  , "description" , "tags" , "indexedCustomData3" , "groupId" ,"permissions" , "partnerData" ,
		"allowQuickEdit" );
	
	public function describe() 
	{
		return 
			array (
				"display_name" => "KShow",
				"desc" => ""
			);
	}
	
	public function getUpdateableFields()
	{
		return $this->updateable_fields; 
	}		
}
?>