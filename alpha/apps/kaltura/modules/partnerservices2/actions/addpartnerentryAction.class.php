<?php
/**
 * @package api
 * @subpackage ps2
 */
class addpartnerentryAction extends addentryAction
{
    public function describe()
    {
        return
            array(
                "display_name" => "addPartnerEntry",
                "desc" => "Add entry to a kshow - the entry will belong to the partner NOT a user" ,
                "in" => array(
                    "mandatory" => array(
                        "kshow_id" => array("type" => "string", "desc" => "Add the entry to thie kshow"),
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
                        "uid" => array("type" => "string", "desc" => "The user id of the partner. Is exists, the ks will be generated for this user and "),
                        )
                    ),
                "out" => array (
                    "ks" => array("type" => "string", "desc" => "Kaltura Session - a token used as an input for the rest of the services") ,
                    ),
                "errors" => array (
                    APIErrors::INVALID_KSHOW_ID,
                    APIErrors::INVALID_ENTRY_ID,
                    APIErrors::NO_ENTRIES_ADDED,
                    APIErrors::UNKNOWN_MEDIA_SOURCE,
                )
            );
    }

	protected function ticketType ()	{		return self::REQUIED_TICKET_ADMIN;	}
	
    protected function getGroup()
    {
    	return myPartnerUtils::PARTNER_GROUP;
    }
}
?>