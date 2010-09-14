<?php
require_once ( "myInsertEntryHelper.class.php");
require_once ( "myKshowUtils.class.php");
require_once ( "defPartnerservices2Action.class.php");
require_once ( "myPartnerUtils.class.php");


class addentryAction extends defPartnerservices2Action
{
    public function describe()
    {
        return
            array(
                "display_name" => "addEntry",
                "desc" => "Add entry to a kshow" ,
                "in" => array(
                    "mandatory" => array(
                        "kshow_id" => array("type" => "string", "desc" => "Add the entry to this kshow"),
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
               			"quick_edit" => array("type" => "boolean", "desc" => "automatically add the entry to the roughcut"),
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

    // check to see if already exists in the system = ask to fetch the puser & the kuser
    // don't ask for  KUSER_DATA_KUSER_DATA - because then we won't tell the difference between a missing kuser and a missing puser_kuser
    public function needKuserFromPuser ( )
    {
        // need the kuser_id to set as the kuser of this entry
        return self::KUSER_DATA_KUSER_ID_ONLY;
    }

    protected function addUserOnDemand ( )
    {
        return self::CREATE_USER_FORCE;
    }

    protected function getObjectPrefix()
    {
    	return "entry";
    }
    
    protected function getGroup() { return null;}
     
    /**
     Will allow creation of multiple entries
     ASSUME - the prefix of the entries is entryX_ where X is the index starting at 1
     */
    public function executeImpl ( $partner_id , $subp_id , $puser_id , $partner_prefix , $puser_kuser )
    {
        $logger = sfLogger::getInstance();
        self::$escape_text = true;

/*		if ( !$puser_kuser )
        {
            $this->addError( "No such user ..." );
            return;

        }
    */
        // TODO - validate if the user can add entries to this kshow

        $kshow_id = $this->getP ( "kshow_id" );
        $show_entry_id = $this->getP ( "show_entry_id" );
        $conversion_quality = $this->getP ( "conversionquality" ); // must be all lower case
        
        if( strpos($kshow_id, 'entry-') !== false && !$show_entry_id )
        {
        	$show_entry_id = substr($kshow_id, 6);
        }
        
        $screen_name = $this->getP ( "screen_name" );
        $site_url = $this->getP ( "site_url" );

        $null_kshow = true;

        if ( $show_entry_id  )
        {
            // in this case we have the show_entry_id (of the relevant roughcut) - it suppresses the kshow_id
            $show_entry = entryPeer::retrieveByPK( $show_entry_id );
            if ( $show_entry  )
            {
                $kshow_id = $show_entry->getKshowId();
            }
            else
            {
                $kshow_id = null;
            }
        }

		if ( $kshow_id === kshow::SANDBOX_ID )
		{
			$this->addError ( APIErrors::SANDBOX_ALERT );
			return ;
		}
		
		$default_kshow_name = $this->getP ( "entry_name" , null );
		if ( ! $default_kshow_name ) $default_kshow_name = $this->getP ( "entry1_name" , null );		 
        if ( $kshow_id == kshow::KSHOW_ID_USE_DEFAULT )
        {
            // see if the partner has some default kshow to add to
            $kshow = myPartnerUtils::getDefaultKshow ( $partner_id, $subp_id , $puser_kuser , null , false , $default_kshow_name );
            $null_kshow = false;
            if ( $kshow ) $kshow_id = $kshow->getId();
        }
		elseif ( $kshow_id == kshow::KSHOW_ID_CREATE_NEW )
        {
            // if the partner allows - create a new kshow 
            $kshow = myPartnerUtils::getDefaultKshow ( $partner_id, $subp_id , $puser_kuser , null , true , $default_kshow_name);
            $null_kshow = false;
            if ( $kshow ) $kshow_id = $kshow->getId();
        }        
        else
        {
            $kshow = kshowPeer::retrieveByPK( $kshow_id );
        }

        if ( ! $kshow )
        {
            // the partner is attempting to add an entry to some invalid or non-existing kwho
            $this->addError( APIErrors::INVALID_KSHOW_ID, $kshow_id );
            return;
        }

        // find permissions from kshow
		$permissions = $kshow->getPermissions();
		
        $kuser_id = $puser_kuser->getKuserId();

        // for now - by default use quick_edit
		$partner = PartnerPeer::retrieveByPK( $partner_id );
		
		// TODO - once the CW 
        $quick_edit = myPolicyMgr::getPolicyFor( "allowQuickEdit" , /*$this->getP ( "quick_edit" , null ),*/ $kshow, $partner );
        // let the user override the quick_edit propery
		if ( $this->getP ( "quick_edit" ) == '0' ||  $this->getP ( "quick_edit" ) == "false"  ) $quick_edit = false 	;
        if ( $quick_edit == '0' || $quick_edit === "false" || !$quick_edit || $quick_edit == false  )
        {
sfLogger::getInstance()->err ( '$quick_edit: [' . $quick_edit . ']' );        	
            $quick_edit = false;
            
            //$quick_edit = true;
        }

        // works in one of 2 ways:
        // 1. get no requested name - will create a new kshow and return its details
        // 2. get some name - tries to fetch by name. if already exists - return it
        $new_entry_count = 0;
        $entries = array();
        $notification_ids = array();
        $notifications = array();
		
        $field_level = $this->isAdmin() ? 2 : 1;
		$updateable_fields  = null;
		
		$imported_entries_count = 0;
		
        for ( $i=0 ; $i<= $partner->getAddEntryMaxFiles() ; ++$i )
        {
        	if ( $i == 0 )$prefix = $this->getObjectPrefix() . "_";
        	else $prefix = $this->getObjectPrefix() . "$i" . "_";
            $file_name = $this->getP ( $prefix . "realFilename" ) ;
            if ( ! ( $this->getP ( $prefix . "name" ) || $file_name) )
            {
                continue;
            }

            // get the new properties for the kuser from the request
            $entry = new entry();

            $obj_wrapper = objectWrapperBase::getWrapperClass( $entry , 0 );
			if ( ! $updateable_fields  ) $updateable_fields = $obj_wrapper->getUpdateableFields( $field_level );
            
           
            // fill the entry from request
            $fields_modified = baseObjectUtils::fillObjectFromMap ( $this->getInputParams() ,
                $entry ,
                $prefix ,
                $updateable_fields );
            // check that mandatory fields were set
            // TODO

            sfLogger::getInstance()->err ( "addentry: fields_modified: " . print_r ( $fields_modified , true ) );

            $entry_source = $entry->getSource() ;
            
            if (!$entry->getType()) // this is the default for backward compatiblity
	            $entry->setType(entry::ENTRY_TYPE_MEDIACLIP);
	            
			$token = $this->getKsUniqueString();
            
	        $entry_full_path = "";
			if ( $entry_source == entry::ENTRY_MEDIA_SOURCE_FILE )
            {
            	$entry->setSourceLink($file_name);
                $file_alias = $this->getP ( $prefix .  "filename" );
                $file_extension = strtolower(pathinfo( $this->getP ( $prefix .  "realFilename" ) , PATHINFO_EXTENSION  ));
                $entry_full_path = myUploadUtils::getUploadPath( $token , $file_alias , null , $file_extension );
            	if (!file_exists($entry_full_path)) {
                	sfLogger::getInstance()->err ( "Invalid UPLOAD PATH [".$entry_full_path."] while trying to add entry for partner id [".$partner_id."] with token [".$token."] & original name [".$this->getP($prefix."name")."]");
                	$this->addError(APIErros::INVALID_FILE_NAME);
                	continue;
                }
            	myEntryUtils::setEntryTypeAndMediaTypeFromFile($entry, $entry_full_path);
            }
            
//            No reason to rais the error
//            Remarked by Tan-Tan
//            
//            // when we reached this point the type and media type must be set
//            if ($entry->getType() == entry::ENTRY_TYPE_AUTOMATIC || $entry->getMediaType() == entry::ENTRY_MEDIA_TYPE_AUTOMATIC)
//            {
//				sfLogger::getInstance()->err ( "unknown TYPE [".$entry->getType()."] or MEDIA_TYPE [".$entry->getMediaType()."] for file [$entry_full_path]");
//				$this->addError ( APIErrors::CANNOT_USE_ENTRY_TYPE_AUTO_IN_IMPORT );
//            	continue;
//            }
            	
            // limit two kinds of media 
            // 1. not images - video/audio which are big files
            // 2. media which wasnt directly uploaded by the owner (the owner real content)
            if ($entry->getMediaType() != entry::ENTRY_MEDIA_TYPE_IMAGE && $entry_source != entry::ENTRY_MEDIA_SOURCE_FILE)
            {
            	if ($imported_entries_count >= 4)
            		continue;
            		
            	++$imported_entries_count;
            }
            
            // the conversion_quality is set once for the whole list of entries
            if ( $conversion_quality )
            	$entry->setConversionQuality( $conversion_quality );
            else
            {
            	// HACK - if the conversion_quality was not set in the proper way - 
            	// see if the partner_data holds a hack - string that starts with conversionQuality= - this is set when the CW is opened in the KMC
            	// the conversionQuality is of format conversionQuality=XXX;<the rest of the text>
            	// 
            	if ( kString::beginsWith( $entry->getPartnerData() , "conversionQuality:" ) )
            	{
            		$partner_data_arr = explode ( ";" , $entry->getPartnerData() , 2 );
            		$conversion_quality_arr = explode ( ":" , $partner_data_arr[0] );
            		$conversion_quality = @$conversion_quality_arr[1]; // the value of the conversion_quality
            		
            		$entry->setPartnerData( @$partner_data_arr[1] );  // the rest of the string
            		$entry->setConversionQuality( $conversion_quality );
            	}
            }
            	
            $insert = true;
            $entry_modified = false;
            $create_entry = true;

            // I don't remember why we set the kshow to null every time ...
            // but when we fetched it automatically - hang on to it !
            if ( $null_kshow )	$kshow = null;
            if( $entry_source == entry::ENTRY_MEDIA_SOURCE_KALTURA_USER_CLIPS ||  $entry_source == "100")
            {
				if ($entry_source == "100")
            		$entry_id = $this->getP ("media{$i}_id" );
            	else
                	$entry_id = $this->getP ( $prefix . "id" ); // $this->getP ( $prefix . "url" );
                	
				if ( $entry_id === null )
				{
					$entry_id  = $entry->getMediaId();
				}
                
                if ( $entry_id )
                {
                    $entry = entryPeer::retrieveByPK( $entry_id );

                    if ( $entry )
                    {
                        $create_entry = false;
                        $insert = false;
                    }
                    else
                    {
                        $this->addError( APIErrors::INVALID_ENTRY_ID , $this->getObjectPrefix() , $entry_id );
                        return ;
                    }
                }
            }

            $new_entry_count++;

            if ( $create_entry )
            {
                $entry->setPartnerId( $partner_id );
                $entry->setSubpId ( $subp_id );
                $entry->setKuserId( $kuser_id );
                $entry->setKshowId ( $kshow_id );
                //$entry->setStatus ( entry::ENTRY_STATUS_READY );
                $entry->setSiteUrl ( $site_url );
                $entry->setScreenName( $screen_name );
                $entry->setStatusReady ( );
                if ( $this->getGroup() ) $entry->setGroupId ( $this->getGroup() );
                if ( $entry->getPermissions () === null ) $entry->setPermissions( $permissions ); // inherited from the enclosing kshow
                $entry->save();

                $entry_modified = true;

                if ( !$entry->getName() )
                {
                    if ( $file_name )
                    {
                        // TODO - fix the file_name to fit
                        $entry->setName( $file_name );
                    }
                    else
                    {
                        $entry->setName( $partner_prefix .  $entry->getId() );
                    }
                    $entry_modified = true;
                }

            // TODO - decide on file naming mechanism !!
            // there are 3 types of insert:
            // 1. upload - the file is assumed to be in the upload directory and it's name is explicitly set in the fname$i param
            // 2. webcam - the file is assumed to be in the webcam directory and it's name is explicitly set in the fname$i param
            // 3. URL - the url is given in the entry_url$i param
/*
            $media_source = $this->getParam('entry_media_source');
            $media_type = $this->getParam('entry_media_type');
            $entry_url = $this->getParam('entry_url');
            $entry_source_link = $this->getParam('entry_source_link');
            $entry_fileName = $this->getParam('entry_data');
            $entry_thumbNum = $this->getParam('entry_thumb_num', 0);
            $entry_thumbUrl  = $this->getParam('entry_thumb_url', '');
            $entry_from_time  = $this->getParam('entry_from_time', 0);
            $entry_to_time  = $this->getParam('entry_to_time', 0);

            $should_copy = $this->getParam('should_copy' , false );
            $skip_conversion = $this->getParam('skip_conversion' , false );
*/
                $paramsArray = array ('entry_media_source' => $entry->getSource() ,
                    'entry_media_type' => $entry->getMediaType() ,
                    );

    //			$entry_source = $entry->getSource() ;

                if ( $entry_source == entry::ENTRY_MEDIA_SOURCE_FILE )
                {
                    $paramsArray["entry_full_path"] = $entry_full_path;
                }
                elseif ($entry_source == entry::ENTRY_MEDIA_SOURCE_WEBCAM )
                {
                    $file_alias = $this->getP ( $prefix .  "filename" );
                    $paramsArray["webcam_suffix"] = /*$token . "_" . */$file_alias  ;
                    $paramsArray['entry_from_time'] = $this->getP ( $prefix .  "fromTime" , 0 );
                    $paramsArray['entry_to_time'] = $this->getP ( $prefix .  "toTime" , 0 );
                    
                }
                elseif( $entry_source == entry::ENTRY_MEDIA_SOURCE_KALTURA ||
                        $entry_source == entry::ENTRY_MEDIA_SOURCE_KALTURA_PARTNER ||
                        $entry_source == entry::ENTRY_MEDIA_SOURCE_KALTURA_PARTNER_KSHOW ||
                        $entry_source == entry::ENTRY_MEDIA_SOURCE_KALTURA_KSHOW ||
                        $entry_source == entry::ENTRY_MEDIA_SOURCE_KALTURA_USER_CLIPS) // we might reach here if we can't find the existing entry
                {
                    // optimize - no need to actually go through the import and conversion phase
                    // find the source entry_id from the url
/*
                    $entry_url = $this->getP ( $prefix . "url" );
                    $entry_thumb_url = $this->getP ( $prefix .  "thumbUrl" );

                    if ( myEntryUtils::copyData( $entry_url , $entry_thumb_url , $entry ) )
*/
                    $source_entry_id = $this->getP ("media{$i}_id" ); // $this->getP ( $prefix . "url" );
					if ( $source_entry_id === null )
					{
						$source_entry_id  = $entry->getMediaId();
					}
                    if ( myEntryUtils::copyData( $source_entry_id , $entry ) )
                    {
                        // copy worked ok - no need to use insertEntryHelper
                        //$entry->setStatus ( entry::ENTRY_STATUS_READY );
                        // force the data to be ready even if the policy is to moderate - this is kaltura's content and was already approved
                        // (roman) true argument removed, so kaltura's content will be moderated according to partner's moderation settings
                        $entry->setStatusReady ();
                        $insert = false;
                        $entry_modified = true;
                        $entry->save();
                    }
                }
                elseif( $entry_source == entry::ENTRY_MEDIA_SOURCE_URL ||
                    in_array( $entry_source , myMediaSourceFactory::getAllMediaSourceProvidersIds() ) )
                {
                    // the URL is relevant
                    $paramsArray["entry_url"] = $this->getP ( $prefix . "url" );
                    $paramsArray["entry_thumb_url"] = $this->getP ( $prefix .  "thumbUrl" );
                    // TODO - these fields are already set in the entry -
                    // the code in myInsertEntryHelper is redundant
                    $paramsArray["entry_license"] = $entry->getLicenseType();
                    $paramsArray["entry_credit"] = $entry->getCredit();
                    $paramsArray["entry_source_link"] = $entry->getSourceLink();
                    $paramsArray["entry_tags"] = $entry->getTags() ;
                }
                else
                {
                    $this->addError( APIErrors::UNKNOWN_MEDIA_SOURCE, $entry->getSource() );
                    $insert = false;
                }

                if ( $insert )
                {
                    sfLogger::getInstance()->err ( "paramsArray" . print_r ( $paramsArray , true ) );

                    $insert_entry_helper = new myInsertEntryHelper($this , $kuser_id, $kshow_id, $paramsArray );
                    $insert_entry_helper->setPartnerId( $partner_id , $subp_id );
                    $insert_entry_helper->insertEntry( $token , $entry->getType() , $entry->getId() , $entry->getName() , $entry->getTags() , $entry );
                    $insert_entry_helper->getEntry();

                    $this->addDebug( "added_entry$i" , $entry->getName() );
                }
            } // create_entry = true

sfLogger::getInstance()->err ( 'id: ' . $entry->getId() . ' $quick_edit:' . $quick_edit );

            if ( $quick_edit )
            {
            	kLog::log("quick edit with kshow_id [$kshow_id]");
                if ( !$kshow) $kshow = kshowPeer::retrieveByPK( $kshow_id ); // this i
                if ( !$kshow )
                {
                    // BAD - this shount not be !
                    $this->addError( APIErrors::INVALID_KSHOW_ID, $kshow_id );
                    return ;
                }

                $metadata = $kshow->getMetadata();
                if ($metadata !== null) // probably the roughcut doesnt exist
                {
	            	kLog::log("Having metadata");
	            	
	                $relevant_kshow_version = 1 + $kshow->getVersion(); // the next metadata will be the first relevant version for this new entry
					//sfLogger::getInstance()->err ( 'id: ' . $entry->getId() . "[$relevant_kshow_version]" );
	
	                $version_info = array();
					$version_info["KuserId"] = $puser_kuser->getKuserId();
					$version_info["PuserId"] = $puser_id;
					$version_info["ScreenName"] = $puser_kuser->getPuserName();
				
	                $new_metadata = myMetadataUtils::addEntryToMetadata ( $metadata , $entry ,$relevant_kshow_version, $version_info );
	                $entry_modified = true;
	                if ( $new_metadata )
	                {
	                    // TODO - add thumbnail only for entries that are worthy - check they are not moderated !
	                    $thumb_modified = myKshowUtils::updateThumbnail ( $kshow , $entry , false );
	
	                    if ( $thumb_modified )
	                    {
	                        $new_metadata = myMetadataUtils::updateThumbUrlFromMetadata ( $new_metadata , $entry->getThumbnailUrl() );
	                    }
	                    // it is very important to increment the version count because even if the entry is deferred
	                    // it will be added on the next version
	
		                if ( ! $kshow->getHasRoughcut (  ) )
		                {
		                	// make sure the kshow now does have a roughcut
		                	$kshow->setHasRoughcut ( true );	
		                	$kshow->save();
		                }
	
	                    $kshow->setMetadata ( $new_metadata, true ) ;
	                }
	                // no need to save kshow - the modification of the metadata was done to the show_entry which will propagate any chages to the kshow in it's own save method
                }
            }

            if ( $entry_modified )
            {
                $entry->save();
            }

            //if ( $entry->isReady() )
            {
            	$not_id = null;
            	// send a synch notification
                @list ( $not_id  ,  $not , $url , $params , $serialized_params )= myNotificationMgr::createNotification( kNotificationJobData::NOTIFICATION_TYPE_ENTRY_ADD , $entry , null , null );
                if ($not_id)
                {
	                $notification_ids[] = $not_id;
	                $notifications[] = array ( "url" => $url , "params" => $serialized_params );
                }
            }

            $wrapper = objectWrapperBase::getWrapperClass( $entry , objectWrapperBase::DETAIL_LEVEL_REGULAR);
            $entries [ $prefix ] = $wrapper;
            //$this->addMsg ( $prefix , $wrapper);

            // because the kshow's entrys field was changes do to this update, we have to remove object from cahce
            // TODO - think of some reverse way to automatically remove from the cache from within the wrapper
            // call some - update cache where the kshow_id plays a part in the update
            $wrapper->removeFromCache( "kshow" , $kshow_id , "entrys" );

            $this->addDebug ( "added_fields" , $fields_modified , $prefix );
        }

        $this->addMsg ( "entries" , $entries );
        if ( count ( $notification_ids ) > 0 )
        {
        	$this->addMsg ( "notifications" , $notifications );
        }
        
        if ( $new_entry_count < 1 )
        {
            $this->addError( APIErrors::NO_ENTRIES_ADDED );
            $this->addDebug ( "no_new_entries" , "You have to have at least one new entry with a field called 'entry1_name'" );
        }
        $this->addMsg ( "new_entry_count" , $new_entry_count );

    }
}
?>