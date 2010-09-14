<?php
class serviceDescriber
{
	public static function describe ( $clazz )
	{
			
	}
	
	var $desc = array (
		"startsession" => array (
			"ticket" => "" ,
			"privileges" => "" ,
			"in" => array (
				"mandatory" => array ( 
					"partner_id" => array("integer", null, "field description") ,
					"subp_id" => array("integer", null, "field description") ,
					"secret" => array("string", null, "field description") ,
					) ,
				"optional" => array (
					"uid" => "The user id of the partner. Is exists, the ks will be generated for this user and " ,				
					)
				),
			"out" => array (
				"ks" => "Kaltura Session - a token used as an input for the rest of the services" ,
				),
			"errors" => array (
			) 
		) ,
		
		"addentry" => array (
			"desc" => "Add entry to a kshow." ,
			"in" => array (
				"mandatory" => array ( 
					"kshow_id" => array("type" => "integer", "desc" => "Add the entry to thie kshow"),
					"entry" => array("type" => "entry", "desc" => "Description of entry object"), 
					// TODO: HOW TO DESCRIBE MULTIPLE ENTRIES?
		/*
					"entry1_name" => "Name of entry. Will be used for display and search capabilities" ,
					"entry1_source" => "Can be one of the following:" ,
					"entry1_mediaType" => "" ,
					"entry1_tags" => "" ,
					"entry1_filename" => "Used after the 'upload' or 'uploadjpeg' services. A unique alias for the uploaded file. Should matchs the filename in the 'upload' or 'uploadjpeg' services." ,
					"entry1_realFilename" => "Used after the 'upload' or 'uploadjpeg' services. The real filename from the disk. The extension (part after the last '.' character) of the file is used to figure out the file type.",
					"entry1_url" => "Used for importing a file from a remote URL, usually after using the 'search' or 'searchmediainfo' services. " ,
					"entry1_thumbUrl" => "Used for importing a thumbnail of a file from a remote URL, usually after using the 'search' or 'searchmediainfo' services. " ,
					"entry1_sourceLink" => "" ,
					"entry1_licenseType" => "", 
					"entry1_credit" => "",
	*/
					) ,
				"optional" => array (
					"uid" => array("type" => "string", "desc" => "The uuser id of the partner. Is exists, the ks will be generated for this user and "),				
					)
				),
			"out" => array (
				"ks" => array("type" => "string", "desc" => "Kaltura Session - a token used as an input for the rest of the services") ,
				),
			"errors" => array (
			) 
		) ,		
		
		"addmoderation" => array(
			"desc" => "blablabla",
			"in"   => array(
				"madatory" => array(
					"moderation" => array("type" => "moderation", "desc" => "blablabla")
				),
				"optional" => array(
					"detailed" => array("type" => "integer", "default" => "0")
				)
			),
			"out" => array(
				"moderation" => array("type" => "moderation", "detail_level" => array(objectWrapperBase::DETAIL_LEVEL_REGULAR, objectWrapperBase::DETAIL_LEVEL_DETAILED), "desc" => "blabla")
			),
			"errors" => array()
		),
		
		"listnotifications" => array(
			"desc" => "blablabla",
			"in"   => array(
				"madatory" => array(
					
				),
				"optional" => array(
					"page_size" => array("type" => "integer", "default" => 10),
					"page" => array("type" => "integer", "default" => 1),
					"filter" => array("type" => "notificationFilter")
				)
			),
			"out" => array(
				"count" => array("type" => "integer"),
				"page_size" => array("type" => "integer"),
				"page" => array("type" => "integer"),
				"notifications" => array("type" => "*notification", "detail_level" => objectWrapperBase::DETAIL_LEVEL_REGULAR, "desc" => "balbalba")
			),
			"errors" => array()
		),
	);
}
?>