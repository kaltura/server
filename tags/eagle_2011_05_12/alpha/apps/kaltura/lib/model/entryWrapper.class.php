<?php
class entryWrapper extends objectWrapperBase
{
	protected $basic_fields = array ( "id" , "name" , "partnerId" , "subpId" );
	
	protected $regular_fields_ext = array ( "puserId"  , "tags" , "status" , "type" ,  "mediaType" ,"kshowId" , /*"kuserId" , */
		"createdAt" , "createdAtAsInt" ,  "dataPath", "rank" , "totalRank" , "anonymous"  , "duration" ,
		"source" ,  "sourceId" , "sourceLink" , "dataUrl" , "thumbnailUrl" , "groupId" , "partnerData" , 
		"version", "conversionQuality", "permissions" , "desiredVersion", "editorType" ,
		"indexedCustomData1" , "hasRealThumb" , "mediaId" , "width" , "height", "userScreenName", "screenName" , "siteUrl" , "description", "downloadUrl",
		"mediaDate", "licenseType" , "credit", "displayCredit", "views" , "votes" , "plays" , "adminTags" , "modifiedAt" , "count", "countDate" ,
		"partnerLandingPage" , "userLandingPage", "securityPolicy"
	);
	
	protected $detailed_fields_ext = array ( "comments" , "favorites"  , "dataContent" , "allVersionsFormatted" , 
		"moderationStatus" , "moderationCount" , "contributorScreenName" ) ;
	
	protected $detailed_objs_ext = array ( "kuser" , "kshow"   );
	
	protected $objs_cache = array ( "kuser" => "kuser,kuserId" , "kshow" => "kshow,kshowId" ); 

	protected $updateable_fields = array ( "name"  , "tags" , "type" , "mediaType" , "source" ,  "sourceId" , "sourceLink" , 
			"licenseType" , "credit"  , "groupId" , "partnerData", "conversionQuality", "permissions" , "dataContent" , "desiredVersion" ,
			"url" , "thumbUrl" , "filename" , "realFilename" , "indexedCustomData1" ,
			"thumbOffset" ,
			"mediaId", "screenName", "siteUrl" , "description", "mediaDate" , "conversionQuality", "securityPolicy" );
	
	// allow to set some other field in some cases - logic should be set in the caller funtion
	protected $updateable_fields_ext = array ( "adminTags" );
				
	public function describe() 
	{
		return 
			array (
				"display_name" => "Entry",
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