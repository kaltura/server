<?php
class kuserWrapper extends objectWrapperBase
{
	protected $basic_fields = array ( "id" , "screenName" , "partnerId"  );
	
	protected $regular_fields_ext = array ( "puserId" , "fullName" , "email" , "dateOfBirth" , "picturePath" , "pictureUrl" , 
		"icon" , "aboutMe" , "tags" , "gender" , "createdAt" , "createdAtAsInt" , "partnerData" , "storageSize" );
	
	protected $detailed_fields_ext = array ( "country" , "state" , "city"  , "zip" , "urlList" , "networkHighschool" , "networkCollege" , "views" , "fans" , "entries" , "producedKshows" ) ;
	
	protected $detailed_objs_ext = array ( "kshows" , "entrys" );
	
	protected $objs_cache = array ( "kshows" => "*kshow,id" , "entrys" => "*entry,id" );
	
	
	protected $read_only_fields = array ( "id" , "picturePath" , "icon" , "createdAt" , "views" , "fans" , "entries" , "producedKshows" );
	
	protected $updateable_fields = array ( "screenName"  , "fullName" , "email" , "dateOfBirth" ,  "aboutMe" , "tags" , "gender"  ,
			 "country" , "state" , "city"  , "zip" , "urlList" , "networkHighschool" , "networkCollege" , "partnerData" );
	
	public function describe() 
	{
		return 
			array (
				"display_name" => "User",
				"desc" => ""
			);
	}
	
	public function getUpdateableFields()
	{
		return $this->updateable_fields;
	}
}
?>