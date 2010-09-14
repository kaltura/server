<?php

class myMetadataUtils
{
	const METADATA_EDITOR_SIMPLE = "Simple";
	const METADATA_EDITOR_ADVANCED = "Keditor";
	const METADATA_QUICK_EDIT = "QuickEdit";
	
	const LAST_PENDING_TIMESTAMP_ELEM_NAME = "LastPendingTimeStamp";
	const PENDNIG_INTERVAL_IN_SECONDS = 43200; // half a day

	static private $textSlidesStyles = array(
		array("solid" => 13434624, "args" => array("fontColor" => "3355443", "fontSize" => "44", "textPosition" => "Center", "rotation" => "-7.022793835573214", "fontName" => "Amsterdam")),
		array("solid" => 6684774, "args" => array("fontColor" => "16777215", "fontSize" => "40", "textPosition" => "Center", "rotation" => "0", "fontName" => "Arista")),
		array("solid" => 0, "args" => array("fontColor" => "16776960", "fontSize" => "39", "textPosition" => "Center", "rotation" => "0", "fontName" => "Berthsid")),
		array("solid" => 16777215, "args" => array("fontColor" => "10027008", "fontSize" => "56", "textPosition" => "Center", "rotation" => "-6.141110616876816", "fontName" => "Chopin")),
		array("solid" => 13421721, "args" => array("fontColor" => "16711680", "fontSize" => "43", "textPosition" => "Center", "rotation" => "-28.1062698208019", "fontName" => "Scribblings")),
		array("solid" => 3368601, "args" => array("fontColor" => "65535", "fontSize" => "72", "textPosition" => "Center", "rotation" => "0", "fontName" => "Coolvetica"))
		);

	static public function getTextSlideAssets($item, $assetStartTime, $lenTimeSecs)
	{
		$vidassets = '';
		$overlays = '';

		$style = $item->getAttribute ( "style" ) - 1;

		if ($style < count(self::$textSlidesStyles))
		{
			$k_id = $item->getAttribute ( "entry_id" );
			$line1 = $item->getAttribute ( "label1" );
			$line2 = $item->getAttribute ( "label2" );

			$styleProps = self::$textSlidesStyles[$style];
			$bg_color = $styleProps["solid"];
			$args = $styleProps["args"];

			$argsStr = '';
			foreach($args as $name => $value)
				$argsStr .=	'<argument id="'.$name.'" value="'.$value.'"/>';

			$line = $line1.'&#xD;'.$line2;

			$vidassets .=
				'<vidAsset k_id="'.$k_id.'" type="SOLID" name="'.$bg_color.'" url="SOLID">'.
					'<StreamInfo file_name="SOLID" start_time="'.$assetStartTime.'" len_time="'.$lenTimeSecs.'" posX="0" posY="0" start_byte="-1" end_byte="-1" total_bytes="-1" real_seek_time="-1" volume="1" pan="0" isSingleFrame="0" real_start_byte="-1" real_end_byte="-1"/>'.
					'<EndTransition type="None" StartTime="'.$lenTimeSecs.'" length="0" />'.
				'</vidAsset>';

			$overlays .=
				'<Plugin type="simpleTextOverlay" StartTime="'.$assetStartTime.'" length="'.$lenTimeSecs.'">'.
					'<arguments>'.
						'<name>simpleTextOverlay</name>'.
						'<version>1.00</version>'.
						'<arguments>'.
							$argsStr.
							'<argument id="text" value="'.$line.'"/>'.
						'</arguments>'.
					'</arguments>'.
				'</Plugin>';
		}

		return array($vidassets, $overlays);
	}

	static public function getExtData($kshow_id, $partner_id)
	{
		$kshow = kshowPeer::retrieveByPK( $kshow_id );
		$show_entry_id = $kshow->getShowEntryId();
		$show_entry = entryPeer::retrieveByPK( $show_entry_id );

		$show_entry_data_key = $show_entry->getSyncKey(entry::FILE_SYNC_ENTRY_SUB_TYPE_DATA);

		if ($show_entry->getMediaType() != entry::ENTRY_MEDIA_TYPE_SHOW)
			return array(null, null);

		$content = kFileSyncUtils::file_get_contents($show_entry_data_key);

		if ($content == "")
			return array(null, null);

		$xml_doc = new DOMDocument();
		$xml_doc->loadXML( $content );

		$xpath = new DOMXPath($xml_doc);

		$extDataNodelist = $xpath->query("//ExtData[@partner_id='$partner_id']");

		$node = null;

		if ($extDataNodelist && $extDataNodelist->length)
		{
			$node = $extDataNodelist->item(0);
		}

		return array($xml_doc, $node);
	}

	static public function setMetadata($content, $kshow, $show_entry , $ignore_current = false, $version_info = null )
	{
		$xml_content = "";

		$show_entry_data_key = $show_entry->getSyncKey(entry::FILE_SYNC_ENTRY_SUB_TYPE_DATA);
		
		$current_content = ($show_entry->getMediaType() != entry::ENTRY_MEDIA_TYPE_SHOW) ? "" : kFileSyncUtils::file_get_contents( $show_entry_data_key );
		if ( $ignore_current ) $current_content = "";

		$update_kshow = false;

		// compare the content and store only if different
		if ( $content != $current_content )
		{
			$show_entry->setData( "metadata.xml" );
			// re-create data key (to get latest version)
			$show_entry_data_key = $show_entry->getSyncKey(entry::FILE_SYNC_ENTRY_SUB_TYPE_DATA);

			//$version = myContentStorage::getVersion($file_name);
			//$comments = "file path: " . $file_name . "\n";
			$comments = "version: " . $show_entry_data_key->version . "\n";

			$total_duration = 0;
			$editor_type = null;
			$content = 	myFlvStreamer::fixMetadata ($content, $show_entry_data_key->version , $total_duration , $editor_type);

			// TODO - make fixMetadata a more generic funciton to return the editorType as well
 
			// save info file about the version
			if (isset($version_info))
			{
				$content = self::addVersionInfo($content, $version_info);
			}
			
			kFileSyncUtils::file_put_contents($show_entry_data_key, $content, true);

			//fixme $content = myFlvStreamer::modifiedByKeditor ( $content );

			// total_duration is in seconds with 2 digits after the decimal point
			$show_entry->setLengthInMsecs ( $total_duration * 1000 );
			$show_entry->setMediaType(entry::ENTRY_MEDIA_TYPE_SHOW);
			$show_entry->setEditorType ( $editor_type );
			$show_entry->setModifiedAt(time());		// update the modified_at date
			$show_entry->save();

			$xml_content = kFileSyncUtils::file_get_contents( $show_entry_data_key ); // replaced__getFileContent
			
			$update_kshow = true;

			$show_entry_id = $show_entry->getId();
			// update the roughcut_entry table
			$all_entries_for_roughcut = self::getAllEntries ( $content );
			roughcutEntry::updateRoughcut( $show_entry->getId(), $show_entry->getVersion(), $show_entry->getKshowId()  , $all_entries_for_roughcut );

			$xml_content = $content;
		}
		else
		{
			$xml_content = $current_content;
			$comments = "old and new files are the same";
		}

		if ( ! $kshow->getHasRoughcut() && $kshow->getIntroId() != $show_entry->getId())
		{
			$kshow->setHasRoughcut( true );
			$update_kshow = true;
		}

		myStatisticsMgr::incKshowUpdates( $kshow );
		$kshow->save();

		return array($xml_content, $comments, $update_kshow);
	}

	public static function getAllEntries ( $content_or_doc )
	{
		if ( $content_or_doc instanceof DOMDocument )
		{
			$xml_doc = $content_or_doc;
		}
		else // assume it's a string
		{
			$xml_doc = new DOMDocument();
			$xml_doc->loadXML( $content_or_doc );
		}

		$xpath = new DOMXPath($xml_doc);

		$entry_list = array();

		self::getAllEntriesOfType ( $xpath , "//VideoAssets/vidAsset" , $entry_list );
		self::getAllEntriesOfType ( $xpath , "//AudioAssets/AudAsset" , $entry_list );
		self::getAllEntriesOfType ( $xpath , "//VoiceAssets/voiAsset" , $entry_list );

		return $entry_list;
	}

	private static function getAllEntriesOfType ( $xpath , $query , &$list )
	{
		$assets = $xpath->query ( $query );
		$was_modified = false;
		foreach ( $assets as $asset )
		{
			// fix only VIDEO assets
  			$type = $asset->getAttribute ( "type" );

  			if ( $type != "VIDEO" && $type != "AUDIO" && $type != "IMAGE" ) continue;

  			$k_id =  $asset->getAttribute ( "k_id" );
  			if ( ! in_array ( $k_id , $list ) )
  			{
  				$list[] = $k_id;
  			}
		}

		return $list;
	}

	// TODO - decide on number to protect againt very big file manipulation
	const MAX_ENTRIES_IN_METADATA = 50;
	
	// ASSUME : NO MULTIPLE ROUGHCUT !!!!
	// the entry will be added to the current $content !
	static public function addEntryToMetadata ( $content, $entry , $current_kshow_version = null, $version_info = null )
	{
		if ( !$entry)
		{
			return null;
		}

		$media_type = $entry->getMediaType();
		if ( ! ( $media_type == entry::ENTRY_MEDIA_TYPE_VIDEO ||  $media_type == entry::ENTRY_MEDIA_TYPE_IMAGE ) )
		{
			// ignote enatiers that are not images or video
			return null;
		}

		if (isset($version_info))
		{
			$content = self::addVersionInfo($content, $version_info);
		}
		
		$xml_doc = new DOMDocument();
		$xml_doc->loadXML( $content );

		//return $xml_doc->saveXML();

		$xpath = new DOMXPath($xml_doc);

		$totalTime = 0;
		$seqDuarations = $xpath->query("//MetaData/SeqDuration");
		$one_and_only_seqDuaration = null;

		if ( $seqDuarations )
		{
			$one_and_only_seqDuaration = $seqDuarations->item(0);
			if ( $one_and_only_seqDuaration )
				$totalTime = $one_and_only_seqDuaration->nodeValue;
			else
			{
				$totalTime = 0; 
				$one_and_only_seqDuaration = null;
			}
		}
		
		else
			$totalTime = 0; 
		$should_save = false;

		$host_name = null;
		// set the updated list in the xml
		$host_elem = kXml::getFirstElement( $xml_doc , "Host" );
		if ( $host_elem==null || empty ( $host_elem->nodeValue ) || strlen ( $host_elem->nodeValue ) < 6 )
		{
			$host_name = requestUtils::getHost();
			$metadata = kXml::getFirstElement( $xml_doc , "MetaData" );
			kXml::setChildElement( $xml_doc , $metadata , "Host" , $host_name , false );
			$should_save = true;
		}
		else
		{
			$host_name = $host_elem->nodeValue;
		}

		$prev_transition_duration = 0;

		$vidAsset_parentNode = kXml::getFirstElement ( $xml_doc , "VideoAssets" );

		$vidAssets = $xpath->query("//VideoAssets/vidAsset");
		$vidAssets_count = 0;
		foreach ( $vidAssets as $vidAsset )
		{
			if ( $vidAssets_count == 0 )
			{
				if ($vidAsset_parentNode == null )
					$vidAsset_parentNode = $vidAsset->parentNode;
			}
			$end_transition = kXml::getFirstElement ( $vidAsset , "EndTransition" );
			if ( $end_transition  )
			{
				// by the end of the loop we'll have the last transisiotn in hand
				$cross = $end_transition->getAttribute ( "cross" );
				if ( $cross == "1" )
				{
					$prev_transition_duration = $end_transition->getAttribute ( "length" );
				}
				else
				{
					$prev_transition_duration = 0;
				}
				
			}

			$vidAssets_count++;
		}

		$entry_id = $entry->getId();

		// if the entry has an error - remove the pending
		if ( $entry->getStatus() == entry::ENTRY_STATUS_ERROR_CONVERTING )
		{
			// return the XML - new if modified and the original if not
			if ( $current_kshow_version != null)
			{
//				$entry->setUpdateWhenReady ( $current_kshow_version );
				$should_save = self::updatePending ( $xml_doc , $entry_id , false );
				// increment the count on the show entry
			}
			if ( $should_save )
				return $xml_doc->saveXML(); // this is if the host has been added
			return $content;
		}


		if ( $entry->getStatus() != entry::ENTRY_STATUS_READY )
		{
			// return the XML - new if modified and the original if not
			if ( $current_kshow_version != null)
			{
				$entry->setUpdateWhenReady ( $current_kshow_version );
				$should_save = self::updatePending ( $xml_doc , $entry_id , true );
				// increment the count on the show entry
				$show_entry = $entry->getKshow()->getShowEntry();
				if($show_entry)
				{
					$show_entry->incInCustomData ( "pending_entries" );
					$show_entry->incInCustomData ( "moderated_entries" );
					$show_entry->save();
				}
			}
			if ( $should_save )
				return $xml_doc->saveXML(); // this is if the host has been added
			return $content;
		}

		if ( $vidAssets_count >= self::MAX_ENTRIES_IN_METADATA )
		{
			KalturaLog::log ( "Exceeded number of entries in metadata [" . self::MAX_ENTRIES_IN_METADATA . "]. Will not add entry [{$entry_id}] to file." );
			$should_save = false;
			return null;
		}
		
		
		// staart at the beginning of the asset
		$startTime = 0;

		if ($media_type == entry::ENTRY_MEDIA_TYPE_VIDEO)
		{
			$isVideo = true;
			$media_type_str = 'VIDEO';
			$real_length = $entry->getLengthInMsecs() / 1000;
			if ( $real_length <= 0 )
			{
				$lenTime = 10; // hard-coded first 10 seconds
			}
			else
			{
				$lenTime = $real_length;
			}
		}
		else if ($media_type == entry::ENTRY_MEDIA_TYPE_IMAGE)
		{
			$isVideo = false;
			$media_type_str = 'IMAGE';
			$lenTime = 4;
		}
		else
		{
			return $content ; // leave untouched
		}
		$addLastFadeoutTime = 1;

		$media_name = $entry->getName();

		$media_url = $entry->getDataUrl();
		$relMedia_url = strstr($media_url, "/content");

		if ($media_type == entry::ENTRY_MEDIA_TYPE_VIDEO )
		{
		// for video - change the URL of the asset to use the clipper
/* http://www6.localhost.com/index.php/keditorservices/flvclipper?entry_id=12353 */
			$clipper_path = $host_name . "/index.php/keditorservices/flvclipper?entry_id=$entry_id" ;
			$media_url = $clipper_path;
		}

		$transition_duration =1 ;

		$host_id = 1 + ( $media_url % 7 );

		// make sure that the media_url is formated properly otherwize the editor wrongly modifies the URL
		$media_url = str_replace ( "/localhost/" , "/www{$host_id}.localhost.com/" , $media_url );

		$transition_type = "dissolve"; // the dissolve transition is from the cross family. It should not be used with the simple editor
		
		$fixed_media_name = kString::xmlEncode($media_name);
		$newVidasset = "\n" .
		'		<vidAsset k_id="'.$entry_id.'" type="'.$media_type_str.'" name="'.$fixed_media_name.'" url="'.$media_url.'">'. "\n".
		'			<StreamInfo file_name="'.$relMedia_url.'" start_time="'.$startTime.'" len_time="'.$lenTime.'"
						posX="0" posY="0" start_byte="-1" end_byte="-1" total_bytes="-1" real_seek_time="-1" volume="1"
						pan="0" isSingleFrame="0" real_start_byte="-1" real_end_byte="-1" Clipped_Start="0" Clipped_Len="' . $lenTime . '"/>'. "\n".
		'			<EndTransition cross="0" type="'. $transition_type .'" StartTime="'.($lenTime-$transition_duration).'" length="' . $transition_duration . '">'. "\n".
		'				<arguments>'. "\n".
		'					<name>'. $transition_type .'</name>'. "\n".
		'					<version>1.00</version>'. "\n".
		'					<arguments/>'. "\n".
		'				</arguments>'. "\n".
		'			</EndTransition>'. "\n".
		'		</vidAsset>' . "\n";

		// 	if there is a transition for the previous asset - calculate it's length
		$totalTime = $totalTime + $lenTime - $prev_transition_duration;

		// update the pending element if needed
		$should_save = self::updatePending ( $xml_doc , $entry_id , true );

KalturaLog::log ( "Will append to xml\n{$newVidasset}" );

		$temp_xml_doc = new DOMDocument();
		$temp_xml_doc->loadXML ( $newVidasset );
//		echo $temp_xml_doc->saveXML( );
		$new_node = $temp_xml_doc->documentElement;
		$new_vid_asset = $xml_doc->importNode( $new_node ,true) ;


		if (!$vidAsset_parentNode)
		{
			KalturaLog::log ( "No VideoAssets parent node for entry [{$entry_id}] content [{$content}]." );
			return null;
		}
		
		// insert the new video asset as the last one in the "vidAssets" element
		$vidAsset_parentNode->appendChild( $new_vid_asset );

		$one_and_only_seqDuaration->nodeValue =  $totalTime ;
  		return $xml_doc->saveXML();
	}


	public static function updateAllMetadataVersionsRelevantForEntry ( $entry , $version_to_update )
	{
		// TODO - null entry
		$kshow = $entry->getKshow();
		
		if ( ! $kshow ) return null;
		
		// TODO - null kshow
		$show_entry = $kshow->getShowEntry();
		
		if ( ! $show_entry ) return null;
		
		$show_entry->decInCustomData ( "pending_entries" );
		$show_entry->save();

		// entries can be os status ENTRY_STATUS_READY or ENTRY_STATUS_ERROR_CONVERTING
		$status= $entry->getStatus();

		// TODO - null entry
		$versions = $show_entry->getAllVersions();

		// LIMIT the metadata files we update to 5 
		$versions = array_slice ( $versions , -5 );
		
		$versions_updated = array();

		KalturaLog::log ( "updateAllMetadataVersionsRelevantForEntry [" . $entry->getId() . "] version_to_update [$version_to_update] status [$status]");
		foreach ( $versions as $version_arr )
		{
			list ( $file_name , $size  , $timestamp , $version  ) = $version_arr;
			if ( $version >= $version_to_update )
			{
				$metadata_for_version = $show_entry->getMetadata ( $version );
				if ( $metadata_for_version )
				{
					$new_metadata = myMetadataUtils::addEntryToMetadata ( $metadata_for_version , $entry  );
					if ($new_metadata)
						$show_entry->setMetadata ( null , $new_metadata , true , null , $version );
					$versions_updated[]=$version;
				}
			}
		}

		KalturaLog::log ( "updateAllMetadataVersionsRelevantForEntry [" . $entry->getId() . "] updated " . implode ("," , $versions_updated ) );
		return $versions_updated;
	}


	// get the pedning entries from the XML metadata
	public static function getPending ( DOMDocument $xml_doc )
	{
		$pending_node = kXml::getFirstElement( $xml_doc , "Pending" );

		// get the current list from xml
		$already_pending_arr = array();
		$already_pending = "";
		if ( $pending_node )
		{
			$already_pending = $pending_node->nodeValue;
		}

		if ( strlen ( $already_pending ) > 0 )
		{
			$already_pending_arr = explode ( "," , $already_pending );
		}

		return array($already_pending, $already_pending_arr);
	}

	// add the xml node "Pending" if does not yet exist
	// $add: true -> add , false -> remove
	// will return whether the XML was modified

	public static function updatePending ( DOMDocument &$xml_doc , $entry_id , $add )
	{
		// get the current list from xml
		list($already_pending, $already_pending_arr) = self::getPending($xml_doc);

		// manipulate the array
		if  ($add )
		{
			kArray::addToArray ( $already_pending_arr , $entry_id , true );
		}
		else
		{
			// remove from array
			kArray::removeFromArray ( $already_pending_arr , $entry_id );
		}

		if ( count ( $already_pending_arr ) > 0 )
			$already_pending = implode ( "," , $already_pending_arr );
		else
			$already_pending = "";

		// set the updated list in the xml
		$metadata = kXml::getFirstElement( $xml_doc , "MetaData" );
		$should_save = kXml::setChildElement( $xml_doc , $metadata , "Pending" , $already_pending , true );
		$timestamp =  empty ($already_pending) ? "" : time();
		kXml::setChildElement( $xml_doc , $metadata , self::LAST_PENDING_TIMESTAMP_ELEM_NAME , $timestamp , true );

		return $should_save;
	}

	public static function setPending ( DOMDocument &$xml_doc , $pending_obj )
	{
		if ( is_array ( $pending_obj ))
		{
			$pending_str = implode ( "," , $pending_obj );
		}
		else
		{
			$pending_str = $pending_obj;
		}

		$metadata = kXml::getFirstElement( $xml_doc , "MetaData" );
		$should_save = kXml::setChildElement( $xml_doc , $metadata , "Pending" , $pending_str , true );
		$timestamp =  empty ($pending_str) ? "" : time();
		kXml::setChildElement( $xml_doc , $metadata , self::LAST_PENDING_TIMESTAMP_ELEM_NAME , $timestamp , true );
		return $should_save;
	}

	/**
	 * if content includes the string  'LastPendingTimeStamp' - check it's not a day old.
	 * if it is - update then show_entry incInCustomData ( "pending_entries" );
	 *
	 */
	public static function updateEntryForPending ( $show_entry , $version , $content )
	{
		$count = $show_entry->getFromCustomData( "pending_entries" );
		if ( strpos ( $content , self::LAST_PENDING_TIMESTAMP_ELEM_NAME ) !== false )
		{
			if ( $count < 2 )
			{
				// TODO - don't use the dom, use simple string parsing !!
				$xml_doc = new DOMDocument();
				$xml_doc->loadXML( $content );

				$timestamp_elem = kXml::getFirstElement ( $xml_doc , self::LAST_PENDING_TIMESTAMP_ELEM_NAME );
				if ( $timestamp_elem )
				{
					$time = $timestamp_elem->nodeValue;
					if ( time() - $time > self::PENDNIG_INTERVAL_IN_SECONDS )
					{
						// we would like to reduce the number of writes to the DB if possible.
						// because the batch will clean up these entries anyway, all we need is to make sure
						// the count is greater than 0.
						// if it is already at least 1 and after increment greater than 1 - no need to update DB.
						$count = $show_entry->incInCustomData ( "pending_entries" );
	 					$show_entry->save();
					}
				}
			}
		}

		return $count;
	}


	// get the thumb url from the XML metadata
	public static function getThumbUrl ( DOMDocument $xml_doc )
	{
		return kXml::getFirstElementAsText($xml_doc , "ThumbUrl");
	}

	public static function updateThumbUrlFromMetadata ( $xml_metadata, $thumb_url )
	{
		$xml_doc = new DOMDocument();
		$xml_doc->loadXML( $xml_metadata );

		$res =  self::updateThumbUrl($xml_doc ,$thumb_url );
		if ($res )
		{
			return $xml_doc->saveXML();
		}
		return $xml_metadata;
	}

	public static function updateThumbUrl ( DOMDocument &$xml_doc , $thumb_url )
	{
		// set the updated list in the xml
		$metadata = kXml::getFirstElement( $xml_doc , "MetaData" );
		$should_save = kXml::setChildElement( $xml_doc , $metadata , "ThumbUrl" , $thumb_url , true );

		return $should_save;
	}


	public static function getDuration ( $content )
	{
		$xml_doc = new DOMDocument();
		$xml_doc->loadXML( $content );

		$xpath = new DOMXPath($xml_doc);
		$seqDuarations = $xpath->query("//MetaData/SeqDuration");

		foreach( $seqDuarations as $seqDuaration )
		{
			$totalTime = $seqDuaration->nodeValue;
			return $totalTime  ;
		}
	}

	public static function addVersionInfo($xml_metadata, $version_info)
	{
		$xml_doc = new DOMDocument();
		$xml_doc->loadXML( $xml_metadata );

		$metadata = kXml::getFirstElement( $xml_doc, "MetaData" );
		kXml::setChildElement( $xml_doc, $metadata, "KuserId", @$version_info["KuserId"], false );
		kXml::setChildElement( $xml_doc, $metadata, "PuserId", @$version_info["PuserId"], false );
		kXml::setChildElement( $xml_doc, $metadata, "ScreenName", @$version_info["ScreenName"], false );
		kXml::setChildElement( $xml_doc, $metadata, "UpdatedAt", time(), false );
		
		return $xml_doc->saveXML();
	}
}
?>