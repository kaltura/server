<?php
require_once ( "myContentStorage.class.php");

class myFlvStreamer
{
	const MISSING_VALUE = -1;
	const PADDING_TAG_SIZE = 224;
	const PADDING_TAG_TIME = 26;
	const PADDING_TAGS = 20;
	
	private $assetList;
	private $totalLength;
	private $streamInfo;
	private $pendingEntries;
	private $filePath;
	private $addPadding;
	
	private $metadata;

	public function myFlvStreamer ( $filePath, $timeline, $streamNum, $addPadding = false)
	{
		$this->addPadding = $addPadding;
		$contentRoot = myContentStorage::getFSContentRootPath();
		if(substr_count($filePath, $contentRoot))
		{
			$contentRoot = '';
		}
		$this->filePath = $contentRoot.$filePath;
		list ( $this->totalLength , $this->assetList  , $this->streamInfo, $this->pendingEntries ) = @self::getAssets( $filePath, $timeline, $streamNum, $addPadding);
	}

	public function pendingEntriesCount()
	{
		return count($this->pendingEntries);
	}

	public function getTotalLength( $include_metadata = true )
	{
		$metadata_size = 0;
		if ( $include_metadata )
		{
			$metadata_size  = strlen ( $this->printMetadata ( false ) );
		}
		return ( (int)$this->totalLength + $metadata_size );
	}

	public function printMetadata( $echo = true )
	{
		if ( !$this->metadata )
		{
			$this->metadata = self::createMetadataForStreamFlv ( $this->filePath , $this->assetList , $this->streamInfo, $this->addPadding );
		}

		if ( $echo ) echo $this->metadata ;
		else return $this->metadata ;
	}

	public function streamFlv()
	{
		self::streamFlvImpl ( $this->assetList , $this->streamInfo, $this->addPadding );
	}

	// TODO - should move to some metadata wrapper
	public static function getAllAssetsIds ( $filePath )
	{
		list ( $xml_doc , $xpath ) = self::getDomAndXpath ( $filePath );

		$asset_ids = self::getElementsList( $xpath , "*" );
		//$asset_ids = $xpath->query( "//VideoAssets/vidAsset" );

		$arr = array();
		foreach ( $asset_ids as $asset_id )
		{
			$arr[] = $asset_id->getAttribute ( "k_id" ) ;
			//start_time="0" len_time=

		}

		return $arr;
	}

	public static function getAllAssetsData ( $filePath )
	{
		list ( $xml_doc , $xpath ) = self::getDomAndXpath ( $filePath );

		$asset_ids = self::getElementsList( $xpath , "*" );
		//$asset_ids = $xpath->query( "//VideoAssets/vidAsset" );

		$arr = array();
		foreach ( $asset_ids as $asset_id )
		{
			$node = array();
			$node["id"] = $asset_id->getAttribute ( "k_id" ) ;
			$stream_info_elem = kXml::getFirstElement ( $asset_id , "StreamInfo" );
			$node["start_time"] = $stream_info_elem->getAttribute ( "start_time" );
			$node["len_time"] = $stream_info_elem->getAttribute ( "len_time" );
			//start_time="0" len_time=

			$arr[] = $node;
		}

		return $arr;
	}

	private static function getElementsList ( $xpath , $prefix )
	{
		return $xpath->query( "//".$prefix."[@type='VIDEO']|//".$prefix."[@type='IMAGE']|//".$prefix."[@type='AUDIO']|//".$prefix."[@type='VOICE']" );
	}

	private static function getDomAndXpath ( $filePath )
	{
		if ( substr_count ( $filePath  , myContentStorage::getFSContentRootPath() ) == 0  )
			$contentRoot = myContentStorage::getFSContentRootPath();
		else
			$contentRoot = "";
		$xml_doc = new DOMDocument();
		$xml_doc->loadXML( file_get_contents($contentRoot.$filePath) );


		$xpath = new DOMXPath($xml_doc);

		return array ( $xml_doc , $xpath );
	}

	/**
		Returns an array of total_length and a list of all relevant assets
		*/
	private static function getAssets($filePath, $timeline, $streamNum, $addPadding)
	{
		$contentRoot = myContentStorage::getFSContentRootPath();
		if(substr_count($filePath, $contentRoot))
			$contentRoot = '';
		
		$xml_doc = new DOMDocument();
		if ( ! file_exists( $contentRoot.$filePath ) )
		{
			return null;
		}

		try
		{
			@$xml_doc->loadXML( file_get_contents($contentRoot.$filePath) );
		}
		catch ( Exception $ex )
		{
			KalturaLog::log ( "Cannot find XML file at [" . $contentRoot.$filePath . "],  timeline:$timeline, streamNum:$streamNum"  );
			return null;
		}

		list($already_pending, $already_pending_arr) = myMetadataUtils::getPending($xml_doc);

		$fileTimestamp = filectime($contentRoot.$filePath) ;

		$xpath = new DOMXPath($xml_doc);

		$assets = $xpath->query($timeline == "video" ? "//VideoAssets/vidAsset" :
		($timeline == "audio" ? "//AudioAssets/AudAsset" : "//VoiceAssets/voiAsset"));

		$total_length = 13;
		$assetArray = array();

		$num = 2;

		foreach ( $assets as $asset )
		{
			$num = 3 - $num;

			$type = $asset->getAttribute ( "type" );
			if ( $type != "VIDEO" && $type != "AUDIO")
			continue;

			$stream_info_list = ($asset->getElementsByTagName ( "StreamInfo"));

			foreach ( $stream_info_list as $stream_info)
			{
				$file_name = $stream_info->getAttribute ( "file_name" );
				$start_byte = $stream_info->getAttribute ( "start_byte" );
				$end_byte = $stream_info->getAttribute ( "end_byte" );
				$start_byte_play = $stream_info->getAttribute ( "start_byte_play" );
				$end_byte_play = $stream_info->getAttribute ( "end_byte_play" );
				if ( $file_name == NULL || ($start_byte == NULL || $end_byte == NULL) && ($start_byte_play == NULL || $end_byte_play == NULL))
				{
					KalturaLog::log ("qqq INVALID ENTRY $file_name");
					echo "qqq INVALID ENTRY $file_name";
					return; //TODO handle invalid entries???
				}

				if ($streamNum == 3 || $num == $streamNum)
				{
					$asset_entry = entryPeer::retrieveByPK($asset->getAttribute( "k_id" ));
					$flavor_asset_edit = flavorAssetPeer::retrieveBestEditByEntryId($asset_entry->getId());
					$flavor_asset_play = flavorAssetPeer::retrieveBestPlayByEntryId($asset_entry->getId());
					
					// make sure asset exists before trying to get key or file path
					if($flavor_asset_edit)
					{
						$asset_file_name_edit = kFileSyncUtils::getReadyLocalFilePathForKey($flavor_asset_edit->getSyncKey(flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET), false);
					}
					// make sure asset exists before trying to get key or file path
					if($flavor_asset_play)
					{
						$asset_file_name_data = kFileSyncUtils::getReadyLocalFilePathForKey($flavor_asset_play->getSyncKey(flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET), false);
					}
					
					// make sure  both variable are set
					if(!$asset_file_name_data && $asset_file_name_edit)
						$asset_file_name_data = $asset_file_name_edit;
					
					if(!$asset_file_name_edit && $asset_file_name_data)
						$asset_file_name_edit = $asset_file_name_data;

					$assetArray[] = array("file_name_data" => $asset_file_name_data, "file_name_edit" => $asset_file_name_edit,
					"start_byte" => $start_byte, "end_byte" => $end_byte , "start_byte_play" => $start_byte_play , "end_byte_play" => $end_byte_play);

					$total_length += $stream_info->getAttribute ( "total_bytes" );
					
					if ($addPadding)
						$total_length += self::PADDING_TAG_SIZE * self::PADDING_TAGS;
				}
			}
		}

		$streamInfo = array( $filePath, $timeline, $streamNum , $fileTimestamp );

		return array($total_length, $assetArray, $streamInfo, $already_pending_arr );
	}

	// returns a string holding the metadata for the relevant assets
	private static function createMetadataForStreamFlv (  $filePath ,$assets , $streamInfo , $addPadding, $duration = null )
	{
		$metadata_content = self::getMetadataFromCache (  $filePath ,$streamInfo, $addPadding );
		if ( $metadata_content ) return $metadata_content;
		
		list($sizeList, $timeList, $filePositionsList) = self::iterateAssets( $assets , $streamInfo , $addPadding, false );

		$amfSerializer = new FLV_Util_AMFSerialize();

		$metadata = array();
		$metadata_data = array();
		if ( $duration )
		{
			$metadata_data["duration"] = (int)($duration/1000) ;
		}

		$metadata_data["bufferSizes"] = $sizeList;
		$metadata_data["times"] = $timeList;
		$metadata_data["filepositions"] = $filePositionsList;

		$res = $amfSerializer->serialize( 'onMetaData') . $amfSerializer->serialize( $metadata_data );
		$data_len = strlen($res);

		// first create a metadata tag with it's real size - this will be the offset of all the rest of the tags
		// create a metadata tag
		
		$metadata_size = myFlvHandler::TAG_WRAPPER_SIZE + $data_len;
		$metatagEndOffset = myFlvHandler::getHeaderSize() + $metadata_size;

		// second - create the real metadata tag with the values of all the following tags with correct offsets
		for ($i = 0 ; $i < count ( $sizeList ) ; ++$i )
			$sizeList[$i] += $metatagEndOffset;
			
		for ($i = 0 ; $i < count ( $filePositionsList ) ; ++$i )
			$filePositionsList[$i] += $metatagEndOffset;

		$metadata_data["bufferSizes"] = $sizeList;
		$metadata_data["filepositions"] = $filePositionsList;
		
		$res = $amfSerializer->serialize( 'onMetaData') . $amfSerializer->serialize( $metadata_data  );

		$metadata_content =  myFlvHandler::createMetadataTag($res);
		self::setMetadataInCache ( $filePath , $streamInfo , $addPadding, $metadata_content );
		return $metadata_content;
	}

	// writes the flv content to the stdout.
	private static function streamFlvImpl ( $assets , $streamInfo, $addPadding )
	{
		self::iterateAssets( $assets , $streamInfo, $addPadding, true );
	}


	// is common to both createMetadataForStreamFlv & streamFlv
	private  static function iterateAssets ( $assets , $streamInfo , $addPadding, $echo )
	{
		if ( $assets == null ) return null;

		$total_bytes = 0;

		list( $filePath, $timeline, $streamNum , $fileTimestamp ) = $streamInfo ;

		$lastTimeStamp = 0;

		$sizeList = array();
		$timeList = array();
		$filePositionsList = array();
		$sizeListTime = 1000;
		$dump_type = $echo ? myFlvHandler::GET_NEXT_TAG_ALL : myFlvHandler::GET_NEXT_TAG_META;
		
		if ($addPadding)
		{
			$silence_tag_data = '080000d1000000000000002efffb50c40003c00001a400000020000034800000'.
				'044c414d45332e39382e32555555555555555555555555555555555555555555'.
				'5555555555555555555555555555555555555555554c414d45332e39382e3255'.
				'5555555555555555555555555555555555555555555555555555555555555555'.
				'5555555555555555555555555555555555555555555555555555555555555555'.
				'5555555555555555555555555555555555555555555555555555555555555555'.
				'55555555555555555555555555555555555555555555555555555555000000dc';

			$silence_tag = pack("H*", $silence_tag_data);
		}
		
		foreach ( $assets as $asset )
		{
			// in the future this will always be true,for backward compatibility - make sure will work OK if
			// there is no edit flavor
			$use_multi_flavor = ($asset['file_name_edit'] && $asset['file_name_data']) && $timeline == "video";

			$number_of_iterations = $use_multi_flavor ? 2 : 1;

			for ( $i=0 ; $i < $number_of_iterations ; $i++ )
			{
				if ( $use_multi_flavor )
				{
					if ( $i == 0 )
					{
						$file_name = $asset['file_name_edit'];  // edit flavor with original attributes
						$start_byte = $asset['start_byte'];
						$end_byte = $asset['end_byte'];
					}
					else
					{
						$file_name = $asset['file_name_data'];	// original file name with '_play' attributes
						$start_byte = $asset['start_byte_play'];
						$end_byte = $asset['end_byte_play'];
					}

					KalturaLog::log( "myFlvStreamer:: ($i) using $file_name ($start_byte - $end_byte)" );

				}
				else
				{
					$file_name = $asset['file_name_data'];		// original file name with original attributes
					$start_byte = $asset['start_byte'];
					$end_byte = $asset['end_byte'];
				}

				if ($start_byte >= $end_byte) // edit flavor not used (play clip was used from keyframe)
					continue;
				$first_frame = true;
				
				KalturaLog::log("playing file [$file_name] from [$start_byte] to [$end_byte]");
				// if should echo - don't optimize (we need the actual data
				// if should not echo - use optimization
				$flv_wrapper = new myFlvHandler ( $file_name );
				$flv_wrapper->seek($start_byte);

				$flv_tag_prev = $flv_wrapper->getNextTag($dump_type);

				if ($flv_tag_prev == NULL) continue;

				KalturaLog::log("file [$file_name]: flv_tag_prev is not null");
				$videoTimeline = ($timeline == "video");

				while ($flv_tag_prev[myFlvHandler::TAG_FIELD_POS] < $end_byte)
				{
					$flv_current_tag = $flv_wrapper->getNextTag($dump_type);

					// dont write the last tag as we dont know its duration and we wont be able to give the next chunk
					// a percise timestamp
					if ( $flv_current_tag == NULL )
						break;

					$prev_tag_type = $flv_tag_prev[myFlvHandler::TAG_FIELD_TYPE];
					if ($prev_tag_type != myFlvHandler::TAG_TYPE_METADATA) // skip metadata
					{
						$prev_tag_timestamp = $flv_tag_prev[myFlvHandler::TAG_FIELD_TIMESTAMP];
						
						if ($first_frame)
						{
							$first_frame = false;
							$lastTimeStamp -= $prev_tag_timestamp;
						}

						// if the timeline is video dump both audio and video data chunks
						// otherwise (timeline is audio / voice) dump only audio data chunks
						if ($videoTimeline || $prev_tag_type == myFlvHandler::TAG_TYPE_AUDIO)
						{
							$currentTimeStamp = $prev_tag_timestamp + $lastTimeStamp;

							if ( $echo ) 
							{
								echo myFlvHandler::dumpTag($flv_tag_prev[myFlvHandler::TAG_FIELD_DATA], $lastTimeStamp);
							}
							else
							{
								$total_bytes += $flv_tag_prev[myFlvHandler::TAG_FIELD_SIZE];

								// we accumulate 3 types of metadata
								// filepositions and times - VIDEOS - each keyframe. AUDIO - each second (used for scrubbing validation)
								// bufferTimes - once per second (used for buffering calculations)

								if ($videoTimeline)
								{
									if ($flv_tag_prev[myFlvHandler::TAG_FIELD_KEYFRAME])
									{
										$filePositionsList[] = $total_bytes;
										$timeList[] = $currentTimeStamp;
									}
								}

								if ($sizeListTime < $currentTimeStamp)
								{
									$sizeList[] = $total_bytes;

									if (!$videoTimeline) // for audio add the filepositions and times to the metadata
									{
										$filePositionsList[] = $total_bytes;
										$timeList[] = $sizeListTime;
									}

									$sizeListTime += 1000;
								}
							}
						}
					}

					$flv_tag_prev = $flv_current_tag;
				}

				$lastTimeStamp += $flv_tag_prev[myFlvHandler::TAG_FIELD_TIMESTAMP];
			}
			
			// add silence padding between clips in order to give the flash player enough time to
			// execute some logic in between. 
			if ($addPadding)
			{
				$paddingTags = self::PADDING_TAGS;
				while($paddingTags--)
				{
					if ( $echo ) 
					{
						echo myFlvHandler::dumpTag($silence_tag, $lastTimeStamp);
					}
					$lastTimeStamp += self::PADDING_TAG_TIME;
					$total_bytes += self::PADDING_TAG_SIZE;
				}
			}
		}

		return array($sizeList, $timeList, $filePositionsList);
	}


	public static function modifiedByKeditor ( $content )
	{
		$xml_doc = new DOMDocument();
		$xml_doc->loadXML( $content );

		$xpath = new DOMXPath($xml_doc);

		$metadata_elem = $xpath->query("//Metadata" );

		$modified_by_keditor_list = $metadata_elem->getElementsByTagName( "Modified" );
		if ( $modified_by_keditor_list != null && $modified_by_keditor_list->length > 0 )
		{
			$modified_by_keditor = $modified_by_keditor_list->item(0)->nodeValue;
			return $content;
			//$modified_by_keditor->setValue
		}
		else
		{
			$newTextNode = $doc ->createTextNode("keditor");
			$modified = $xml_doc->createElement( "Modified" ) ;//, "keditor" );
			$modified->appendChild ( $newTextNode );
			$metadata_elem->appendChild ( $modified );
		}

		return $xml_doc->saveXML();

	}


	
	public static function fixMetadata ( $content , $version , &$total_duration , &$editor_type)
	{
		$xml_doc = new DOMDocument();
		$xml_doc->loadXML( $content );

		self::fixGeneralMetadata( $xml_doc, $version);
		self::getEditorType ( $xml_doc, $editor_type);
		
		self::updatePendingEntries ( $xml_doc );
		// Fix metadata before setting it in the file
		// myFlvHandler will help set adjust every video asset byte values from milliseconds
		$total_duration = 0;

		$xml_doc = self::fixMetadataImpl ( $xml_doc , $total_duration , "video");
		$xml_doc = self::fixMetadataImpl ( $xml_doc , $total_duration , "audio");
		$xml_doc = self::fixMetadataImpl ( $xml_doc , $total_duration , "voice");

		return $xml_doc->saveXML();
//		return $content;
	}

	public static function getEditorType ( $xml_doc , &$editor_type )
	{
		if ( ! $xml_doc ) return null;
		
		$list = $xml_doc->getElementsByTagName( "Application" );
		if ( $list != null && $list->length > 0 )
		{
			$value = $list->item(0)->nodeValue ;
			// fix the value - leave only valid characters 
			$editor_type = preg_replace ( "/[^a-zA-Z0-9\-_]/" , "" , $value  );
			return;
		}

		$editor_type = null;
	}
		
	private static function fixGeneralMetadata ( &$xml_doc , $version)
	{
		if ( ! $version )
		{
			return $xml_doc;
		}
/*
		$xml_doc = new DOMDocument();
		$xml_doc->loadXML( $content );
	*/
		$list = $xml_doc->getElementsByTagName( "ShowVersion" );
		if ( $list != null && $list->length > 0 )
		{
			$list->item(0)->nodeValue = $version;
		}

  		//return $xml_doc->saveXML();
		return $xml_doc;
	}

	// make sure that the entries that are in ready or error status are no longer pending entries
	// entries that were assumed to be pending but have arrived (either i
	public static function updatePendingEntries ( &$xml_doc )
	{
		$really_pending_entries = array();
		list ( $pending_str , $pending_arr ) = myMetadataUtils::getPending ( $xml_doc );
//		$entries_in_roughcut = myMetadataUtils::getAllEntries( $xml_doc );
		$pending_arr_not_in_roughcut = $pending_arr; //array();
		// we should not attemp to match pending entries with the list in the XML - they will never be there
		// assuming they are really pending  
/*		foreach ( $pending_arr as $pending )
		{
			if ( in_array ( $pending , $entries_in_roughcut ) )
			{
				// 	we need to keep this entry pending - it's used in the xml
				$pending_arr_not_in_roughcut[] = $pending;
			}
		}
*/
		if ( count ( $pending_arr_not_in_roughcut ) > 0 )
		{
			// get all the enrties that are really pending:
			$pending = entryPeer::retrievePendingEntries ( $pending_arr_not_in_roughcut );
			foreach ( $pending as $entry )
			{
				$really_pending_entries[] = $entry->getId();
			}
		}
		
		myMetadataUtils::setPending( $xml_doc, $really_pending_entries );
	}

	/**
	 * here we'll manipulate the video asset and set the from_byte & to_byte from the milliseconds
	 *
	 */
	private static function fixMetadataImpl ( &$xml_doc , &$total_duration, $timeline )
	{
self::log ( __METHOD__ );
/*
		$xml_doc = new DOMDocument();
		$xml_doc->loadXML( $content );
	*/

//		$meatadata_elem_list = $xml_doc->getElementsByTagName( "MetaData" );
//		if ( $meatadata_elem_list != null && $meatadata_elem_list->length > 0 )
			$duration_list = $xml_doc->getElementsByTagName( "SeqDuration" );
			if ( $duration_list != null && $duration_list->length > 0 )
			{
				$total_duration = $duration_list->item(0)->nodeValue;
			}

		$xpath = new DOMXPath($xml_doc);

		$assets = $xpath->query($timeline == "video" ? "//VideoAssets/vidAsset" :
			($timeline == "audio" ? "//AudioAssets/AudAsset" : "//VoiceAssets/voiAsset"));

		$lastTimestamp = 0;
		$real_start_byte = 0; // the start byte of the current clip in the final merged stream
		$calculated_total_bytes = 0;

		// use the entryPool and a 2-pass iteration to reduce the hits to the DB  
		$id_list = array();
		$entry_pool = new entryPool();
		// first pass - populate the entryPool in a single request to the DB		

self::log ( __METHOD__ , "Before assets");
		
		foreach ( $assets as $asset )
		{
  			$type = $asset->getAttribute ( "type" );
  			if ( $type != "VIDEO" && $type != "AUDIO") continue;

  			// fetch the file name from the DB
  			$asset_id =  $asset->getAttribute ( "k_id" );
  			$id_list[] = $asset_id;
		}
		
self::log ( __METHOD__ , "After assets" , count($id_list ) , $id_list  );
		
		if ( $id_list )
		{
			$entry_pool->addEntries( entryPeer::retrieveByPKsNoFilter( $id_list ) );
		}
	
		// second pass - the entryPool is supposed to already be populated 
		$was_modified = false;
		foreach ( $assets as $asset )
		{
			// fix only VIDEO assets
  			$type = $asset->getAttribute ( "type" );
  			if ( $type != "VIDEO" && $type != "AUDIO") continue;

  			// fetch the file name from the DB
  			$asset_id =  $asset->getAttribute ( "k_id" );
  			
self::log ( __METHOD__ , "in loop" , $asset_id );
  			//$entry = entryPeer::retrieveByPKNoFilter( $asset_id );
			$entry = $entry_pool->retrieveByPK( $asset_id ); // is supposed to exist already in the pool

			if ( $entry == NULL )
  			{
  				// set an error on the asset element
  				$asset->setAttribute ( "fix_status" , "error in k_id [$asset_id]" );
				$was_modified = true;
  				continue;
  			}
  			elseif ( $entry->getStatus() == entryStatus::DELETED )
  			{
  				// set an error on the asset element
  				$asset->setAttribute ( "fix_status" , "error in k_id [$asset_id] - asset was deleted" );
				$was_modified = true;
  				continue;
  			}

  			$file_name = null;
  			//TODO: need to work on only an FLV asset
			$flavor_asset_play = flavorAssetPeer::retrieveBestPlayByEntryId($entry->getId());
			if(!$flavor_asset_play)
			{
				KalturaLog::log(__METHOD__.' '.__LINE__.' no play flavor asset for entry '.$entry->getId());
			}
			else
			{
				$file_name = kFileSyncUtils::getReadyLocalFilePathForKey($flavor_asset_play->getSyncKey(flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET));
			}
			
			$use_multi_flavor = false;
			$flavor_asset_edit = flavorAssetPeer::retrieveBestEditByEntryId($entry->getId());
			if(!$flavor_asset_edit)
			{
				KalturaLog::log(__METHOD__.' '.__LINE__.' no edit flavor asset for entry '.$entry->getId());
			}
			else
			{
				$flv_file_name_edit = kFileSyncUtils::getReadyLocalFilePathForKey($flavor_asset_edit->getSyncKey(flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET));
				$use_multi_flavor = $flv_file_name_edit && file_exists($flv_file_name_edit) && $timeline == "video";
			}
			
			if(!$flv_file_name_edit && !$file_name)
			{
				KalturaLog::log(__METHOD__.' '.__LINE__.' no edit & play flavor assets for entry '.$entry->getId());
				continue;
			}

 			$flv_file_name = kFile::fixPath( $file_name );

  			$stream_info_list = ($asset->getElementsByTagName ( "StreamInfo"));

  			foreach ( $stream_info_list as $stream_info)
  			{
  				$file_name = "?";
  				try
  				{
  					$stream_info->setAttribute ( "file_name",  kFileSyncUtils::getReadyLocalFilePathForKey($flavor_asset_play->getSyncKey(flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET))); // replaced__getDataPath
		  			$start_byte = $stream_info->getAttribute ( "start_byte" );
		  			$end_byte = $stream_info->getAttribute ( "end_byte" );
		  			$total_bytes = $stream_info->getAttribute ( "total_bytes" );
		  			if ( $start_byte == NULL ) $start_byte = self::MISSING_VALUE;
		  			if ( $end_byte == NULL ) $end_byte = self::MISSING_VALUE;
		  			if ( $total_bytes == NULL ) $total_bytes = self::MISSING_VALUE;

		  			$len_time = floor(1000 * $stream_info->getAttribute ( "len_time" ));

		  			if (1||$start_byte == self::MISSING_VALUE || $end_byte == self::MISSING_VALUE || $total_bytes == self::MISSING_VALUE)
		  			{
		  				// set the values from start_time & len_time - the original numbers are in seconds (with a decimal point)
		  				$start_time = floor(1000 * $stream_info->getAttribute ( "start_time" ));
		  				$end_time = $start_time + $len_time;

						$real_start_byte += $calculated_total_bytes;
		  				$calculated_start_byte = 0;
						$calculated_end_byte = 0;
						$calculated_total_bytes = 0;
						$calculated_real_seek_time = 0;
						$calculated_start_byte_play = 0;
						$calculated_end_byte_play = 0;
						$calculated_total_bytes_play = 0;

/*		  				$file_name = $stream_info->getAttribute ( "file_name" );
						$flv_file_name = kFile::fixPath( myContentStorage::getFSContentRootPath() . $file_name );
						$ext = pathinfo ($flv_file_name, PATHINFO_EXTENSION);
						if ( $ext == NULL  )
							$flv_file_name .= ".flv";
	*/


						try
						{
self::log ( __METHOD__ , "before findBytesFromTimestamps" , $flv_file_name );

							//$use_multi_flavor = myFlvStaticHandler::isMultiFlavor ( $flv_file_name  ) && $timeline == "video";

							$calculated_real_seek_time = $lastTimestamp;
							$start_time_play = null;

							if ( $use_multi_flavor )
							{
								$start_time_play = $start_time;
								// play
								// $start_time_play - will be modified according to the first keyframe's time stamp AFTER (not before) the requested timestamp
								$result = myFlvStaticHandler::findBytesFromTimestamps( $flv_file_name , $start_time_play , $end_time ,
									$calculated_start_byte_play , $calculated_end_byte_play , $calculated_total_bytes_play, $lastTimestamp, $timeline != "video" , 1 );

								KalturaLog::log(__METHOD__.' '.__LINE__." play $result = findBytesFromTimestamps($flv_file_name , $start_time_play , $end_time , $calculated_start_byte_play , $calculated_end_byte_play , $calculated_total_bytes_play, $lastTimestamp, $timeline)");
									
								if( $result )
								{
									if ( $start_time_play != $start_time )
									{
										// we need to fill the gap between the user requested keyframe and the one actually found in the play (low res) flavor
										// 	edit - more keyfrmaes !
										$result = myFlvStaticHandler::findBytesFromTimestamps ( $flv_file_name_edit , $start_time , $start_time_play ,
											$calculated_start_byte , $calculated_end_byte, $calculated_total_bytes, $lastTimestamp, $timeline != "video" , 2 );
										KalturaLog::log(__METHOD__.' '.__LINE__." edit $result = findBytesFromTimestamps($flv_file_name , $start_time_play , $end_time , $calculated_start_byte_play , $calculated_end_byte_play , $calculated_total_bytes_play, $lastTimestamp, $timeline)");
									}
								}
							}
							else
							{
								// no reason to have multi-flavor files
								// either because NOT video or because the edit flavor does not exist
								$result = myFlvStaticHandler::findBytesFromTimestamps ( $flv_file_name , $start_time , $end_time ,
									$calculated_start_byte , $calculated_end_byte, $calculated_total_bytes, $lastTimestamp, $timeline != "video" , 0 );
								KalturaLog::log(__METHOD__.' '.__LINE__." only play $result = findBytesFromTimestamps($flv_file_name , $start_time_play , $end_time , $calculated_start_byte_play , $calculated_end_byte_play , $calculated_total_bytes_play, $lastTimestamp, $timeline)");
							}
self::log ( __METHOD__ , "after findBytesFromTimestamps" , $flv_file_name );							
						}
						catch ( Exception $ex1 )
						{
							debugUtils::log( "Error while converting time2bytes in file [$file_name]\n$ex1" );
							echo "Error while converting time2bytes in file [$file_name]\n$ex1";
						}

						$calculated_total_bytes += $calculated_total_bytes_play;

						if ( $result )
						{
							if (1|| $start_byte == self::MISSING_VALUE )
							{
								$stream_info->setAttribute ( "start_byte" , $calculated_start_byte );
								$stream_info->setAttribute ( "start_byte_play" ,  $calculated_start_byte_play );
							}
							if (1|| $end_byte == self::MISSING_VALUE )
							{
								$stream_info->setAttribute ( "end_byte" , $calculated_end_byte );
								$stream_info->setAttribute ( "end_byte_play" ,  $calculated_end_byte_play ) ;
							}
							if (1||$calculated_total_bytes == self::MISSING_VALUE)
							{
								$stream_info->setAttribute ( "total_bytes" , $calculated_total_bytes  );
								$stream_info->setAttribute ( "real_start_byte" , $real_start_byte );
								$stream_info->setAttribute ( "real_end_byte" , $real_start_byte + $calculated_total_bytes );
							}
							if (1||$calculated_real_seek_time == self::MISSING_VALUE)
							{
								// retrun the calculated_real_seek_time in seconds with 2 decimal points
								$stream_info->setAttribute (  "real_seek_time"  , number_format ( ( $calculated_real_seek_time / 1000 ) , 3 , '.', ''));
							}
							if ( $asset->hasAttribute ( "fix_status") )
							{
								$asset->removeAttribute ( "fix_status");
							}
						}
						elseif ( !$result )
						{
							// set an error on the asset element
							$asset->setAttribute ( "fix_status" , "Missing file or invalid FLV structure" );
						}
						
						$was_modified = true;
		  			}
  				}
  				catch ( Exception $ex2 )
  				{
  					echo "Error parsing file [$file_name]\n$ex2";
  				}

	  		}
  		}
  		return $xml_doc;
/*
  		if ( $was_modified )
  		{
  			return $xml_doc->saveXML();
  		}
  		else
  		{
  			// nothing was modified - use the original string
  			return $content;
  		}
*/

	}

	private static function getMetadataFromCache ( $filePath , $streamInfo , $addPadding )
	{
		$file_name = $filePath . ".". $streamInfo[1] . $streamInfo[2] . ".metadata" . ($addPadding ? "-padding" : "");
		if ( file_exists ( $file_name ) ) return file_get_contents( $file_name );
		return null;
	}

	private static  function setMetadataInCache ( $filePath , $streamInfo , $addPadding, $metadata_content )
	{
		$file_name = $filePath . ".". $streamInfo[1] . $streamInfo[2] . ".metadata" . ($addPadding ? "-padding" : "") ;
		file_put_contents( $file_name , $metadata_content ); // sync - OK
	}
	
	private static function log ( $method )
	{
		$s="";
		$numargs = func_num_args();
		$arg_list = func_get_args();
		for ($i = 0; $i < $numargs; $i++) 
		{
			$arg=$arg_list[$i];
			if ( is_array ( $arg ) || is_object ( $arg ) )
				$s .= print_r ( $arg , true );
			else
				$s .= $arg;
			$s .= " " ;
		}
		
		$time = ( microtime(true) );
		$milliseconds = (int)(($time - (int)$time) * 1000);  
		if ( function_exists('memory_get_usage') )
			$mem_usage = "{". memory_get_usage(true) . "}";
		else
			$mem_usage = ""; 
		$s = strftime( "%d/%m %H:%M:%S." , time() ) . $milliseconds . " " . $mem_usage . " " . $s ;	
		KalturaLog::log ( $s ); 
	}
}
?>
