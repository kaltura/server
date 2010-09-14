<?php
class myFlvWrapper
{
	/*FLV File Format
	 Starting with version 6, Flash Player can exchange audio, video, and data over RTMP connections
	 with the Macromedia Flash Communication Server. One way to feed data to Flash
	 Communication Server (and thus on to Flash Player clients) is from files of a new Macromedia
	 open format called FLV. Starting with version 7, Flash Player can also directly play FLV files.
	 An FLV file encodes synchronized audio and video streams. The audio and video data within
	 FLV files are encoded in the same way as audio and video within SWF files.
	 This document describes FLV version 1.
	 Each tag type in an FLV file constitutes a single stream. There can be, at most, one audio and one
	 video stream, synchronized together, in an FLV file. It is not possible to define multiple
	 independent streams of a single type.
	 Note: FLV files, unlike SWF files, store multi-byte integers in big-endian byte order. This means that,
	 for example, the number 300 (0x12C) as a UI16 in SWF file format is represented by the byte
	 sequence 0x2C 0x01, while as a UI16 in FLV file format, it is represented by the byte sequence 0x01
	 0x2C. Also note that FLV uses a 3-byte integer type, UI24, that is not used in SWF.


	 The FLV Header
	 All FLV files begin with the following header:
	 The DataOffset field always has a value of 9 for FLV version 1. This field is present in order to
	 accommodate larger headers in future versions.
	 FLV File Header
	 Field Type Comment
	 Signature UI8 Signature byte always ‘F’	 (0x46)
	 Signature UI8 Signature byte always ‘L’	 (0x4C)
	 Signature UI8 Signature byte always ‘V’	 (0x56)
	 Version UI8 File version (for example, 0x01	 for FLV version 1)
	 TypeFlagsReserved UB[5] Must be 0
	 TypeFlagsAudio UB[1] Audio tags are present
	 TypeFlagsReserved UB[1] Must be 0
	 TypeFlagsVideo UB[1] Video tags are present
	 DataOffset UI32 Offset in bytes from start of file	 to start of body (that is, size of	 header)

	 The FLV File Body
	 After the FLV header, the remainder of an FLV file consists of an alternation of back-pointers and
	 tags. They interleave like this:
	 FLV Tags
	 FLV tags have the following format:
	 In playback, the time sequencing of FLV tags depends on the FLV timestamps only. Any timing
	 mechanisms built into the payload data format are ignored.
	 FLV File Body
	 Field Type Comment
	 PreviousTagSize0 UI32 Always 0
	 Tag1 FLVTAG First tag
	 PreviousTagSize1 UI32 Size of previous tag, including its header.
	 For FLV version 1, this is the previous tag’s DataSize	 plus 11.
	 Tag2 FLVTAG Second tag
	 ...
	 PreviousTagSizeN-1 UI32 Size of second-to-last tag
	 TagN FLVTAG Last tag
	 PreviousTagSizeN UI32 Size of last tag

	 FLVTAG
	 Field Type Comment
	 TagType UI8 Type of this tag. Values are:
	 8: audio
	 9: video
	 all others: reserved
	 DataSize UI24 Length of the data in the Data field
	 Timestamp UI24 Time in milliseconds at which the data in this tag
	 applies. This is relative to the first tag in the FLV file,
	 which always has a timestamp of 0.
	 Reserved UI32 Always 0
	 Data If TagType = 8
	 AUDIODATA
	 If TagType = 9
	 VIDEODATA
	 Body of the tag


	 *
	 *
	 */
	const STATUS_OK = 0;
	const STATUS_INIT_FAILED = -1;

	const TYPE_VIDEO = 9;
	const TYPE_AUDIO = 8;
	const TYPE_METADATA = 18;

	private $init;

	private $header = null;
	private $prev_tag_size_0 = 0;

	private $tag_count = 0;

	private $fh = null;

	private $optimize_when_seek = false;

	private $flv_info = null;
	private $flv_info_audio = null;
	private $flv_metadata = null;
	private $flv_metadata_audio = null;

	private $file_name = null;
	public static $use_info_optimization = true;
	
	private static $create_helpers = 1; // 1 = only video, 2 = only audio , 3 = both
	/**
	 * The result MUST start from a keyframe but does not have to end in one !
	 * $from_byte =  the firts byte of the video-keyframe tag that has a timestamp equal to $from_millisecond. If there is no such a keyframe tag,
	 * 	$from_byte =  the first byte of the closest and SMALLER.
	 * $to_byte =  the last byte of the video-keyframe tag that has a timestamp equal to $to_millisecond. If there is no such a keyframe tag,
	 * 	$to_byte =  the last byte of the closest and BIGGER.
	 */

	public static function getFlvHeaderSize ( )
	{
	 return strlen ( self::getFlvHeader() );
	}


	public static function isMultiFlavor ( $file_name )
	{
		$edit_file_name = myContentStorage::getFileNameEdit( $file_name );
		return ( file_exists ( $edit_file_name ) && filesize ( $edit_file_name ) > 0 ) ;
	}


	/**
		will return the edit file name if exists, else the original one
		*/
	public static function getBestFileFlavor ( $file_name  )
	{
		$edit_file_name = myContentStorage::getFileNameEdit( $file_name );
		if ( file_exists ( $edit_file_name ) && filesize ( $edit_file_name ) > 0 )
		{
			return $edit_file_name;
		}
		return $file_name;
	}

	public static function getFlvHeader ( $add_last_char = true )
	{
		// FLV 1 5 9
		$res = "FLV" . pack('C', 1 ) .pack('C', 5 ) . pack('N', 9 ) ;
		if (  $add_last_char ) $res .= pack('N', 0 );
		return $res;
	}

	// optimize should generally be always true. if we decide to switch it off - it will affect the way we create the myFlvWrapper
	// and the way we add the size of the tags
	public static function findBytesFromTimestamps( $flv_file_name , &$from_millisecond , $to_millisecond ,
	&$from_byte , &$to_byte, &$total_bytes, &$lastTimeStamp, $onlyAudio = false , $get_next_keyframe = false , $optimize = true )
	{
		if ( $onlyAudio )
			self::$create_helpers = 2; // create only audio helpers if not already exists
		else
			self::$create_helpers = 1; // create only video helpers if not already exists
		$count=1;
		$set_from = true;
		
		
		$flv_wrapper = new myFlvWrapper ( $flv_file_name , 0 , $optimize );  // don't use the optimization for now !!
//		$flv_wrapper = new myFlvWrapper ( $flv_file_name , 0 , true );  // don't use the optimization for now !!
		if ( !$flv_wrapper->getStatus () )
		{
			sfLogger::getInstance()->err ( __METHOD__ . " error in file [$flv_file_name]" );
			return self::STATUS_INIT_FAILED;
		}

		$flv_tag_prev = $flv_wrapper->getNextTag();
		$flv_tag_prev_2 = $flv_tag_prev; // will be the previos tag (one before $flv_tag_prev)
		
		// set the dummy first keyframe to be the first - this is usually the case and if not, the FLV is bad anyway
		$flv_tag_prev_keyframe = 	$flv_tag_prev ;

		$from_byte = 0;
		$to_byte = filesize($flv_file_name);
		$lastTagTimeStamp = 0;

		while ( $flv_tag_prev != NULL  )
		{
			$flv_tag_current = $flv_wrapper->getNextTag();

			// dont use the last tag as we dont know its duration and
			// we wont be able to give the next chunk a percise timestamp
			if ( $flv_tag_current == NULL )	break;
			if ($flv_tag_prev->tag_type != self::TYPE_METADATA)
			{
				$lastTagTimeStamp = $flv_tag_prev->timestamp;
				// when processing the audio timeline every audio chunk can be used as a start point
				$keyframe = $flv_tag_prev->keyframe || ($onlyAudio && $flv_tag_prev->tag_type == self::TYPE_AUDIO);
				if ( $set_from )
				{
					// the from must relate to a keyframe
					if ( $keyframe )
					{
						if ( $get_next_keyframe )
						{
							if ( $flv_tag_prev->timestamp >= $from_millisecond )
							{
								$from_millisecond = $flv_tag_prev->timestamp; // we'll set the in-out param to be the actual start time we chose
								// passed to right tag - use the prev: 'closest and SMALLER'
								$from_byte = $flv_tag_prev->first_byte;

								$lastTimeStamp -= $flv_tag_prev->timestamp;
								$set_from = false;

								// total bytes will always start here - nothing before is relevant.
								$total_bytes = 0;
							}
						}
						else
						{
							if ( $flv_tag_prev->timestamp > $from_millisecond )
							{
								// passed to right tag - use the prev: 'closest and SMALLER'
								$from_byte = $flv_tag_prev_keyframe->first_byte;

								$lastTimeStamp -= $flv_tag_prev_keyframe->timestamp;
								$set_from = false;
							}
							// if video timeline get size of whatever chunk, if audio/voice timeline we dont count the current chunk
							else if (!$onlyAudio || $flv_tag_prev->tag_type == self::TYPE_AUDIO)
							{
								$total_bytes = $flv_tag_prev->getSize();
							}
							else
								$total_bytes = 0;
						}
						$flv_tag_prev_keyframe = $flv_tag_prev;
					}
					else if (!$onlyAudio || $flv_tag_prev->tag_type == self::TYPE_AUDIO)
					{
						$total_bytes += $flv_tag_prev->getSize();
						if ( $optimize )
						{
							$delta = $flv_tag_prev->first_byte - $flv_tag_prev_2->first_byte - $flv_tag_prev_2->getSize();
							// will have to add all the bytes between the last tag and the current
							$total_bytes +=  ( $delta  ); 
						}
						
					}
				}
				if (!$set_from) // we dont use an else because we want to test the current frame again
				{
					// the edit flavor always end on a keyframe which is the first keyframe of the play flavor
					// this keyframe shoudnt be included as it is part of the play flavor stream
					if (!$get_next_keyframe)
					{
						if ( ($onlyAudio || $flv_tag_prev->tag_type == self::TYPE_VIDEO) &&  $flv_tag_prev->timestamp >= $to_millisecond )
						{
							$to_byte = $flv_tag_prev->first_byte - 1;
							$lastTagTimeStamp = $flv_tag_prev->timestamp;
							break;
						}
					}

					// add chunk size to total_bytes if this is the video timeline OR audio/voice and the current chunk is of type audio
					if (!$onlyAudio || $flv_tag_prev->tag_type == self::TYPE_AUDIO)
					{
						$total_bytes += $flv_tag_prev->getSize();
					}

					$to_byte = $flv_tag_prev->first_byte - 1 + $flv_tag_prev->getSize();

					// in case of video timeline - the to - can relate to any video frame
					// in case of audio / voice timeline the to - can realte to any frame (audio / video)
					if ($get_next_keyframe && ($onlyAudio || $flv_tag_prev->tag_type == self::TYPE_VIDEO) &&  $flv_tag_prev->timestamp >= $to_millisecond )
					{
						$lastTagTimeStamp = $flv_tag_current->timestamp;
						break;
					}
				}
			}
			
			$flv_tag_prev_2 = $flv_tag_prev;
			$flv_tag_prev = $flv_tag_current;
	//		if ( $count > 25 ) die();
			$count++;
		}

		if ($set_from) // if we didnt find a starting keyframe
		{
			$from_millisecond = $to_millisecond; // change the start position so we'll try getting at the _edit version
			$to_byte = 0; // dont return anything (the default was set to filesize)
			$total_bytes = 0;
		}

		if ( $optimize && !$onlyAudio )
		{
			$total_bytes = ($to_byte - $from_byte) + 1;
		}
		
		$lastTimeStamp += $lastTagTimeStamp;
		return self::STATUS_OK;
	}

	
	public static function clipAudioFlv ( $flv_file_name , $clip_from_milliseconds = 0 , $clip_to_milliseconds = 2147483647  )
	{
		
		return self::clipVideoFlv( $flv_file_name , $clip_from_milliseconds , $clip_to_milliseconds , false );
	}
	
	// will return a list of the followiung:
	// 1. the index of the from_tag - last video tag with timestamp smaller or equal    $clip_from_milliseconds
	// 2. the index of the to_tag - first video tag with timestamp greater of equal 	$clip_to_milliseconds -
	// 3. the number of bytes of all the tags between from_tag and to_tag INCLUSIVE
	public static function clipVideoFlv ( $flv_file_name , $clip_from_milliseconds = 0 , $clip_to_milliseconds = 2147483647 , $is_video = true )
	{
		if ( $is_video ) self::$create_helpers = 1; // create only video helpers if not already exists
		else self::$create_helpers = 2; // create only audio helpers if not already exists
		if ( $clip_from_milliseconds >= $clip_to_milliseconds )
		{
			// error = clip will contain no data
			return array ( -1 , -1 , -1, 0, 0, 0 );
		}

		$flv_wrapper = new myFlvWrapper ( $flv_file_name , 0 , true );
//echo ( "\n" . __METHOD__ . " [$flv_file_name] [" . self::$use_info_optimization . "] [{$is_video}]\n"  );
 
		if ( self::$use_info_optimization )
		{
			if ( $is_video )
			{
				$flv_info = $flv_wrapper->getFlvInfo();
				if ( $flv_info != null && !$flv_info->creating() )
				{
					return $flv_info->clipVideoFlv ( $clip_from_milliseconds  , $clip_to_milliseconds );
				}
			}
			else
			{
				$flv_info_audio = $flv_wrapper->getFlvInfoAudio();
				if ( $flv_info_audio != null && !$flv_info_audio->creating() )
				{
					return $flv_info_audio->clipAudioFlv ( $clip_from_milliseconds  , $clip_to_milliseconds );
				}
			}
		}
			
		if ( !$flv_wrapper->getStatus () )
		{
			// cannot parse the file
			return array ( -1 , -1 , -1, 0, 0, 0 );
		}

		$accumulated_bytes = 0;

		$clip_from = $clip_from_milliseconds; // / 1000;
		$clip_to = $clip_to_milliseconds ; // / 1000;

		$from_index = -1;
		$to_index = -1;
		$from_byte = -1;
		$to_byte = -1;

		$first_tag = null;

		$last_used_tag = null;
		while ( ( $tag = $flv_wrapper->getNextTag() ) != null )
		{
			$last_used_tag = $tag;
			if ( $is_video )
			{
				if ( $tag->tag_type != self::TYPE_VIDEO ) continue;
				$keyframe =  $tag->keyframe;
			}
			else
			{
				if ( $tag->tag_type != self::TYPE_AUDIO ) continue;
				$keyframe = true;
			}

			if ( $keyframe && ($tag->timestamp <= $clip_from || !$first_tag && $tag->timestamp > $clip_from ) )
			{
				{
					// for the first time - start the indexes
					$from_index = $tag->index;
					$from_byte = $tag->first_byte;

					$first_tag = $tag;
				}
			}

			$timestamp = $tag->timestamp;
			if ( $timestamp >= $clip_from && $timestamp <= $clip_to )
			{
				$accumulated_bytes += $tag->getSize();;
//				echo "$tag_type $timestamp $size\n";				
			}
			
			if ( $tag->timestamp > $clip_to )
			{
				$to_index = $tag->index;
				$to_byte = $tag->first_byte + $tag->getSize();
				break;
			}
		}

		if ( $to_byte < 0 )
		{
			// didn't find a tag that can close the clipping - use the lat one
			$to_index = $last_used_tag->index;
			$to_byte = $last_used_tag->first_byte + $last_used_tag->getSize();
		}

		// in case the first tag was not set
		if ( $first_tag == null ) $first_tag = 	$duration = 0;
		else
		{
			$duration = $last_used_tag->timestamp - $first_tag->timestamp;
		}

		$total_bytes = !$is_video ? $accumulated_bytes : (int)( $to_byte - $from_byte );

		return array ( $total_bytes , $from_index , $to_index , $duration , $from_byte , $to_byte ); // added the duration as 4rt parameter

	}

	public static function fixRed5WebcamFlv ( $flv_file_name , $new_file )
	{
		$flv_wrapper = new myFlvWrapper ( $flv_file_name );
		
		// sort timestamps because of a bug in red5 webcam recording
		$sorted_tags = array();
		$index = "";
		while ( ( $tag = $flv_wrapper->getNextTag() ) != null )
		{
			$index = sprintf("%010d%06d", $tag->timestamp, $tag->index);
			$sorted_tags[$index] = $tag->first_byte;
		}
		
		ksort($sorted_tags, SORT_NUMERIC);
		
		$fh = fopen ( $new_file , "w" );
		fwrite ( $fh , myflvWrapper::getFlvHeader() ) ;
		
		foreach($sorted_tags as $timestamp => $pos)
		{
			$flv_wrapper->seek($pos);
			$tag = $flv_wrapper->getNextTag();
			
			fwrite ( $fh , $tag->dump() );
		}

		fclose ( $fh );
	}
	
	public static function clipVideoFlvToNewFile ( $flv_file_name , $new_file , $clip_from_milliseconds = 0 , $clip_to_milliseconds = 2147483647 , $add_header = true   ) // max-int
	{
		// TODO - make the optimized lines below work !!
		//return  self::clipVideoFlv( $flv_file_name , $clip_from_milliseconds , $clip_to_milliseconds , $new_file , $add_header );

		if (!$clip_to_milliseconds)
		$clip_to_milliseconds = 2147483647;
			
		$duration = 0;
		list ( $bytes , $from_index , $to_index , $duration, $from_bytes, $to_bytes  ) = self::clipVideoFlv( $flv_file_name , $clip_from_milliseconds , $clip_to_milliseconds );
		$fh = fopen ( $new_file , "w" );

		if ( $add_header ) fwrite ( $fh , myflvWrapper::getFlvHeader() ) ;

		$flv_wrapper = new myFlvWrapper ( $flv_file_name, $from_bytes );
		$to_index -= $from_index;

		// fix timestamp !
		$timestamp_offset = -1;
			
		// TODO - remove !!
		// this code bellow is not relevant any more now that we serve EXTERNAL metadata for every FLV
		$amfSerializer = new FLV_Util_AMFSerialize();

		$metadata = array();
		$metadata_data = array();
		$metadata_data["duration"] = (int)($duration/1000) ;

		$res = $amfSerializer->serialize( 'onMetaData') . $amfSerializer->serialize( $metadata_data );

		// create a metadata tag
		$metatag = new FlvTag();
		$metatag->setPrevTagSize ( 0 );
		$metatag->setTagType ( 0x12 ); // metadata type
		$metatag->setTimestamp ( 0 );

		$metatag->setData (  $res );

		fwrite ( $fh , $metatag->dump() );

		while ( ( $tag = $flv_wrapper->getNextTag() ) != null )
		{
			$index = $tag->index;
			
			// first time - set the timestamp
			if ( $timestamp_offset == -1 )
			{
				$timestamp_offset = $tag->timestamp;
			}

			$tag->setTimestamp ( $tag->timestamp - $timestamp_offset );
			fwrite ( $fh , $tag->dump() );

			if ( $index > $to_index ) break; // this tag and onwards are irrelevant
		}

		fclose ( $fh );
		
		return $timestamp_offset;
	}


	public function getFirstVideoTimestampImpl ( )
	{
		if ( !$this->getStatus () )
		{
			// cannot parse the file
			return -1;
		}

		$timestamp = -2;
		while ( ( $tag = $this->getNextTag() ) != NULL )
		{
			if ( $tag->tag_type == self::TYPE_VIDEO )
			{
				$timestamp = $tag->timestamp;
				break;
			}
		}

		return $timestamp;		
	}
	
	// TODO - use the member function getFirstVideoTimestampImpl
	// i copied the code due to lack of testing time (liron 2008-08-07)
	public static function getFirstVideoTimestamp ( $flv_file_name )
	{
		$flv_wrapper = new myFlvWrapper ( $flv_file_name , 0 ,true );
		if ( !$flv_wrapper->getStatus () )
		{
			// cannot parse the file
			return -1;
		}

		$timestamp = -2;
		while ( ( $tag = $flv_wrapper->getNextTag() ) != NULL )
		{
			if ( $tag->tag_type == self::TYPE_VIDEO )
			{
				$timestamp = $tag->timestamp;
				break;
			}
		}

		return $timestamp;
	}


	private function getLastTimestampImpl ( )
	{
		if ( !$this->getStatus () )
		{
			// cannot parse the file
			return -1;
		}

		$this->gotoLastTag( false );
		$tag = $this->getNextTag( false );
		$timestamp = $tag->timestamp;
		return $timestamp;
	}

	// search the end of the file -
	// each tag ends with 4 bytes indicating its size - read them and go back to the beginning of the tag
	public static function getLastTimestamp ( $flv_file_name  )
	{
		$flv_wrapper = new myFlvWrapper ( $flv_file_name , 0 ,false );
		$timestamp = $flv_wrapper->getLastTimestampImpl();
		if ( $timestamp  < 1 ) 
		{
			// in this case the end of the file may be damaged
			while ( ( $tag = $flv_wrapper->getNextTag() ) != NULL )
			{
				if ( true /*$tag->tag_type == self::TYPE_VIDEO */ )
				{
					// as long as the timestamp of rh tag is greater than the what we have in hand...
					if ( $tag->timestamp > $timestamp ) 
						$timestamp = $tag->timestamp;
				}
			}
		}
		
		return $timestamp ;
	}

	
	public static function fileHasAudio ( $flv_file_name  )
	{
		$found_audio = false;
		$flv_wrapper = new myFlvWrapper ( $flv_file_name , 0 ,false );
		$header = $flv_wrapper->getHeader();
		// inspect only FLV files
		if ( substr ( $header , 0 , 3 ) != "FLV" )  
		{
			return true; 
		}
		$tag_count = 0;
		while ( ( $tag =  $flv_wrapper->getNextTag( ) ) != null )
		{
			$tag_count++;
			if ( $tag->tag_type == myFlvWrapper::TYPE_AUDIO )
			{
				$found_audio=true;
				 break;
			}
			if ( $tag_count > 3000 ) break;
		}
		
		return $found_audio;
	}
	
	// TODO - can optimize ?? - can create this data in a cached file ?
	public static function fileHasVideoAndAudio ( $flv_file_name  )
	{
		$found_audio = false;
		$found_video = false;
		$flv_wrapper = new myFlvWrapper ( $flv_file_name , 0 ,false );
		$header = $flv_wrapper->getHeader();
		// inspect only FLV files
		if ( substr ( $header , 0 , 3 ) != "FLV" )  
		{
			return true; 
		}
		$tag_count = 0;
		while ( ( $tag =  $flv_wrapper->getNextTag( ) ) != null )
		{
			$tag_count++;
			if ( $tag->tag_type == myFlvWrapper::TYPE_AUDIO )
			{
				$found_audio=true;
			}
			if ( $tag->tag_type == myFlvWrapper::TYPE_VIDEO )
			{
				$found_video=true;
			}
			
			if ( $found_audio && $found_video ) break;
			
			if ( $tag_count > 3000 ) break;
		}
		
		return array ( $found_video , $found_audio );
	}	

	
	public static function isFlv ( $flv_file_name  )
	{
		$flv_wrapper = new myFlvWrapper ( $flv_file_name , 0 ,false );
		$header = $flv_wrapper->getHeader();
		// inspect only FLV files
		return  ( substr ( $header , 0 , 3 ) == "FLV" );  
	}
	
	// myFlvWrapper ctor 
	public function myFlvWrapper( $flv_file_name , $seek_pos = 0 , $optimize_when_seek = false, $called_from_helpers = false )
	{
		try
		{
			if ( !file_exists ( $flv_file_name) )
			{
				$this->init = false;
				sfLogger::getInstance()->err ( __METHOD__ . " [$flv_file_name] file not found" );
				return;
			}

			if ( $seek_pos < 0 )
			$seek_pos = 0;

			$this->fh = fopen($flv_file_name, "rb");
			$this->file_name = $flv_file_name;

			if ($seek_pos)
			{
				fseek($this->fh, $seek_pos, SEEK_SET);
			}
			else
			{
				// first 9 bytes are fixed - read them as the header (last 4 might change in future versions)
				$this->header = fread ( $this->fh, 9 ) ;

				// first find the PreviousTagSize0 - it does not belong to the FLV tag itself !
				// PreviousTagSize0 UI32
				$prev_tag_size_0_raw =  fread ( $this->fh, 4 );
				if ( ! $this->optimize_when_seek )
				{
					// create this small object only when not optimizing
					$this->prev_tag_size_0 = new PrevTagSize ( $prev_tag_size_0_raw );
				}
			}

			$this->optimize_when_seek = $optimize_when_seek ;
			$this->init = true;

//echo __METHOD__ . " [$optimize_when_seek] [$called_from_helpers]\n";			
			// create the info
			if ( self::$use_info_optimization && $optimize_when_seek && !$called_from_helpers)
			{
				$this->createHelperObjects( $flv_file_name );
			}
		}
		catch ( Exception $ex )
		{
			$this->init = false;
			sfLogger::getInstance()->err ( __METHOD__ . " [$flv_file_name] " . $ex->getMessage() );
		}
	}

	public function getFileName ()
	{
		return $this->file_name;
	}

	private function createHelperObjects ( $flv_file_name )
	{
		// use dummy wrapper to create the helpers to the state of the current object will not be damaged
		$wrapper = new myFlvWrapper($flv_file_name, 0, 0, true);

		$create_info = 	$create_metadata = $create_info_audio = $create_metadata_audio = false;
		
		if ( self::$create_helpers & 1 )
		{
			$wrapper->flv_info = new FlvInfo ( $flv_file_name );
			$wrapper->flv_metadata = new FlvMetaData( $flv_file_name );
			$create_info = $wrapper->flv_info->creating();
			$create_metadata = $wrapper->flv_metadata->creating();
		}
		if ( self::$create_helpers & 2 )
		{
			$wrapper->flv_info_audio = new FlvInfoAudio( $flv_file_name );
			$wrapper->flv_metadata_audio = new FlvMetaDataAudio( $flv_file_name );
			$create_info_audio = $wrapper->flv_info_audio->creating();
			$create_metadata_audio = $wrapper->flv_metadata_audio->creating();
		}

//echo __METHOD__ . " ( $create_info || $create_metadata || $create_info_audio || $create_metadata_audio )\n";
	
		if ( $create_info || $create_metadata || $create_info_audio || $create_metadata_audio )
		{
			while ( ( $tag = $wrapper->getNextTag() ) != null )
			{
				if ( self::$create_helpers & 1 )
				{
					if ( $create_info ) $wrapper->flv_info->addTag ( $tag );
					if ( $create_metadata ) $wrapper->flv_metadata->addTag ( $tag );
				}

				if ( self::$create_helpers & 2 )
				{
					if ( $create_info_audio ) $wrapper->flv_info_audio->addTag ( $tag );
					if ( $create_metadata_audio ) $wrapper->flv_metadata_audio->addTag ( $tag );
				}
				
				$tag->data = null; // we dont need the data anymore, just release it
				$tag = null;
			}

			if ( $create_info )  $wrapper->flv_info->close();
			if ( $create_metadata ) $wrapper->flv_metadata->close();
			if ( $create_info_audio ) $wrapper->flv_info_audio->close ();
			if ( $create_metadata_audio ) $wrapper->flv_metadata_audio->close ();

		}

		$this->flv_info = $wrapper->flv_info;
		$this->flv_metadata = $wrapper->flv_metadata;
		$this->flv_info_audio = $wrapper->flv_info_audio;
		$this->flv_metadata_audio = $wrapper->flv_metadata_audio;

	}

	public function getMetadata ( $duration )
	{
		// incase the metadata was never created
		if ( ! $this->flv_metadata )
		{
			$this->createHelperObjects ( $this->file_name );
		}

		return $this->flv_metadata->getMetaData( $duration , 0 , 0 );
	}

	public function getMetadataAudio ( $duration )
	{
		// incase the metadata was never created
		if ( ! $this->flv_metadata_audio )
		{
			$this->createHelperObjects ( $this->file_name );
		}

		return $this->flv_metadata_audio->getMetaData( $duration , 0 , 0 );
	}
	
	
	public function getFlvInfo ()
	{
		return $this->flv_info;
	}

	public function getFlvInfoAudio ()
	{
		return $this->flv_info_audio;
	}
	
	public function getStatus()
	{
		return $this->init;
	}

	public function getHeader ()
	{
		if ( !$this->init ) return NULL;
		return $this->header;
	}

	public function getPrevTagSize0Raw ()
	{
		if ( !$this->init ) return NULL;
		return $this->prev_tag_size_0->getSizeRaw();
	}


	public function rewind ()
	{
		rewind ( $this->fh );
	}

	public function getNextTag ( $optimize = true )
	{
		if ( !$this->init ) return NULL;

		if (  $optimize &&  self::$use_info_optimization && $this->optimize_when_seek && $this->flv_info && ( ! $this->flv_info->creating() ) )
		{
			return $this->flv_info->getNextTag();
		}

		// repeat for every tag...
		if (!feof($this->fh))
		{
			$flv_tag = new FlvTag();
			$flv_tag->index = $this->tag_count;
			$flv_tag->first_byte = ftell ( $this->fh );

			$temp_data = fread ( $this->fh, 11 );
			//$tag_type_raw = fread ( $this->fh, 1 );
			$tag_type_raw = substr ( $temp_data , 0 , 1 );

			// reached the end
			if ( feof($this->fh))			return NULL;

			$flv_tag->setTagTypeRaw( $tag_type_raw );

			//$data_size_raw = fread ( $this->fh , 3 );
			$data_size_raw = substr ( $temp_data , 1 , 3 );
			$flv_tag->setDataSizeRaw( $data_size_raw );

			//$timestamp_raw = fread ( $this->fh , 3 );
			$timestamp_raw = substr ( $temp_data , 4 , 3 );
			$flv_tag->setTimestampRaw( $timestamp_raw );

			//$flv_tag->reserved = fread ( $this->fh , 4 );
			$flv_tag->reserved = substr ( $temp_data , 7 , 4 );
			// because this raw part is not relevant after extracting the real numbers - store them as one big chunk
			//$flv_tag->raw_content = $tag_type_raw . $data_size_raw . $timestamp_raw . $reserved;

			if ( $flv_tag->data_size > 0 )
			{
				// I don't know why this should happen, but it did (once) for upload from webcam
				$flv_tag->data = fread ( $this->fh , $flv_tag->data_size );
			}
			else
			{
				$flv_tag->data = "";
			}

			$flv_tag->keyframe = ( $flv_tag->tag_type  == self::TYPE_VIDEO && self::isKeyframe ( $flv_tag->data ) );

			// PreviousTagSize UI32
			$prev_tag_size_raw =  fread ( $this->fh, 4 );
			$flv_tag->setPrevTagSize ( new PrevTagSize ( $prev_tag_size_raw ) );

			// 			$this->tag_arr[] = $flv_tag;

			// instead of doing this in the here - it's done for the first time in createHelperObjects function
			/*
			 if ( $this->flv_info ) $this->flv_info->addTag ( $flv_tag );
			 if ( $this->flv_metadata )	$this->flv_metadata->addTag ( $flv_tag );
			 */
			$this->tag_count ++;

			return $flv_tag;
		}
		else
		{
			return NULL;
		}
	}


	public function gotoLastTag( $optimize = true )
	{
		if ( $optimize && $this->flv_info ) $this->flv_info->gotoLastTag();
			
		$stat = fstat ( $this->fh );
		$file_size = $stat["size"];
		fseek ($this->fh, $file_size-4 , SEEK_SET); // go back 4 bytes
		$prev_tag_size_raw =  fread ( $this->fh, 4 ); // now read the last 4 bytes
		$tag_size = myFlvWrapper::toInt ( $prev_tag_size_raw , 'N' );
		fseek ($this->fh, $file_size-4-$tag_size , SEEK_SET); // go back 4 bytes AND the size of the tag
	}

	public function seek ($pos)
	{
		if ( $this->fh != null )		fseek( $this->fh, $pos, SEEK_SET );
	}
	
	public function close ()
	{
		if ( $this->fh != null )		fclose( $this->fh );
		if ( $this->flv_info ) $this->flv_info->close();
		if ( $this->flv_metadata ) $this->flv_metadata->close();
	}

	public function __destruct()
	{
		$this->close();
	}

	public static  function toInt ( $bytes , $unpack_type)
	{
		if ( strlen( $bytes ) == 3 )
		{
			if ( $unpack_type == 'N'  )
			{
				$bytes = pack ( 'C' , 0 ) . $bytes ; // big endian
			}
			elseif ( $unpack_type == 'V'  )
			{
				$bytes = $bytes . pack ( 'C' , 0 ) ; // little endian
			}

			//	echo "read [" . $read . "]" ;
		}
		$a = @unpack ( $unpack_type , $bytes );
		$res = $a[1];

		return $res;
	}

	private static function isKeyframe ( $data )
	{
		$first_byte_arr = unpack ( 'C' , $data[0] );
		$first_byte = $first_byte_arr[1];
		$upper_part_first_byte = (($first_byte & 0xF0 ) >> 4 );
		return  ( $upper_part_first_byte == 1 );
	}
}

class FlvTag
{
	public $index = 0;
	public $first_byte;

	//	public $raw_content;
	public $prev_tag_size ;
	//public $prev_tag_size_raw;
	public $tag_type ;
	public $tag_type_raw ;

	public $data_size ;
	public $data_size_raw ;

	public $timestamp ;
	public $timestamp_raw ;

	public $reserved ;
	public $data ;
	public $keyframe;

	public function __construct( )
	{
		$this->prev_tag_size = NULL;
		$this->reserved = pack ( 'N' , 0 );
	}

	public function setPrevTagSize ( $prev_tag_size )
	{
		$this->prev_tag_size = $prev_tag_size;
	}


 //$tag_type_raw . $data_size_raw . $timestamp_raw . $reserved;

 public function setTagTypeRaw ( $raw )
 {
 	$this->tag_type_raw = $raw;
 	$this->tag_type = myFlvWrapper::toInt ( $raw , 'C' );
 }

 public function setTagType ( $value )
 {
 	$this->tag_type = $value;
 	$this->tag_type_raw = pack ( 'C' , $value );
 }

 public function setData ( $data )
 {
 	$this->data = $data;
 	$this->setDataSize ( strlen ( $data ) );
 }

 public function setDataSizeRaw ( $raw )
 {
 	$this->data_size_raw = $raw;
 	$this->data_size = myFlvWrapper::toInt ( $raw , 'N' );
 		
 	//echo ( "setDataSizeRaw [" . $raw . "] of lenght [" . strlen($raw) . "] -> size=[" . $this->data_size ")
 }

 public function setDataSize ( $value )
 {
 	$this->data_size = $value;
 	$raw = pack ( 'N' , $value );
 	$this->data_size_raw = substr ( $raw , 1 );
 }

 public function setTimestampRaw ( $raw )
 {
 	$this->timestamp_raw = $raw;
 	$this->timestamp = myFlvWrapper::toInt ( $raw , 'N' );
 }

 public function setTimestamp ( $value )
 {
 	$this->timestamp = $value;
 	$raw = pack ( 'N' , $value );
 	$this->timestamp_raw = substr ( $raw , 1 );
 }


 public function getSize ( )
 {
 	if ( $this->data == null ) 	return 15 + $this->data_size;
 	return 15 + strlen($this->data);
 }

	public function dump ( $include_prev_tag_size_raw = true )
	{
		//$res = $this->raw_content . $this->data;
		$res = $this->tag_type_raw . $this->data_size_raw . $this->timestamp_raw . $this->reserved . $this->data;
		if ( $include_prev_tag_size_raw )
		{
			if ( $this->prev_tag_size == NULL )
			{
				$this->prev_tag_size = new PrevTagSize( $this->getSize() , true );
			}

			$res .= $this->prev_tag_size->getSizeRaw();
		}

		return $res;
	}

	public function toAsciiString()
	{
		// no need to store the data !
		// the keyframe is originally part of the data, we'll store it aside for further use
		// omit the reserved !
		//$fields = array ( $this->index , $this->first_byte ,$this->tag_type  , $this->getSize() , $this->timestamp , $this->keyframe );
		//$res = implode ( "," , $fields );
		$res = pack ( "N6" , $this->index , $this->first_byte ,$this->tag_type  , $this->getSize() , $this->timestamp , $this->keyframe );
		return $res;
	}

	public static function fromAsciiPack( $str , &$starting_index )
	{
		//		return self::fromAsciiArray ( unpack ( "NNNNNN" , substr ( $str , $starting_index , 24 ) ) , $starting_index );

		if ( empty ( $str ) ) return null;
		if ( strlen($str) <= $starting_index ) return null;

		$flv_tag = new FlvTag();

		$sub_str = substr ( $str , $starting_index , 24 );
		//		echo "fff: ($starting_index) \n" ; //$sub_str\n";

		$arr = unpack ( "N6" , $sub_str );

		//		print_r ( $arr );


		/*
		 list($this->index , $this->first_byte ,$this->tag_type  , $this->getSize() , $this->timestamp , $this->keyframe  ) =
			unpack ( "N6" , substr ( $str , $starting_index , 24 ) );
			*/

		$flv_tag->index = $arr[1] ;
		$flv_tag->first_byte  = $arr[2] ;
		$flv_tag->tag_type	=$arr[3] ;
		$flv_tag->data_size	=  $arr[4] ;
		$flv_tag->timestamp	= $arr[5] ;
		$flv_tag->keyframe = $arr[6] ;

		$starting_index+= 24;

		return $flv_tag;
	}

	public static function fromAsciiString( $str )
	{
		if ( empty ( $str ) ) return null;
		$flv_tag = new FlvTag();

		list ( $flv_tag->index , $flv_tag->first_byte , $tag_type  , $data_size , $timestamp , $keyframe ) =	explode ( ",", $str );
		$flv_tag->setTagType( $tag_type );
		$flv_tag->setDataSize( $data_size );
		$flv_tag->setTimestamp( $timestamp );
		$flv_tag->keyframe = $keyframe;

		return $flv_tag;
	}

	public static function fromAsciiArray( $arr , &$starting_index )
	{
		if ( empty ( $arr ) ) return null;

		if ( count($arr) <= $starting_index ) return null;
		$flv_tag = new FlvTag();

		$flv_tag->index = $arr[$starting_index++];
		$flv_tag->first_byte  = $arr[$starting_index++];
		$flv_tag->tag_type	= $arr[$starting_index++] ;
		$flv_tag->data_size	=  $arr[$starting_index++];
		$flv_tag->timestamp	=$arr[$starting_index++] ;
		$flv_tag->keyframe =  $arr[$starting_index++];

		return $flv_tag;
	}

}

class PrevTagSize
{
	public $size_raw = null;
	public $size = null;

	public function PrevTagSize( $_size_raw , $real_size = false )
	{
		if ( $real_size )
		{
			$this->setSize ( $_size_raw );
		}
		else
		{
			$this->size_raw = $_size_raw;
			$this->size = myFlvWrapper::toInt ( $_size_raw , 'N' );
		}
	}

	private function setSize ( $value )
	{
		$this->size = $value;
		$raw = pack ( 'N' , $value );
		$this->size_raw = $raw; // substr ( $raw , 1 );
	}

	public function getSizeRaw()
	{
		return $this->size_raw;
	}
}


class FlvInfo
{
	const NUMBER_OF_FIELDS = 6;

	protected $FILE_SUFFIX = ".info";

	protected $init = false;
	protected $file_name;
	protected $creating = false;

	protected $very_last_tag = null;
	protected $very_last_video_tag = null;
	protected  $tag_list;
	protected $current_tag_index=0;

	protected $general_info = null;


	public function __construct( $original_flv_file_name , $full_init = false )
	{
		$full_init = true;
		$init = false;
		$original_flv_file_name = kFile::fixPath( $original_flv_file_name );
		$this->file_name = $original_flv_file_name . $this->FILE_SUFFIX;

//echo __METHOD__ . " [$original_flv_file_name] [{$this->file_name}] \n";
		
		// check if the info file exists and is newer than the original
		if ( file_exists ( $this->file_name ) )
		{
//echo __METHOD__ . " [$original_flv_file_name] [{$this->file_name}] \n";			
			$orig_mtime = filemtime  ( $original_flv_file_name ) ;
			$info_mtime = filemtime  ( $this->file_name );

			$this->tag_list  = array();
			if ( $info_mtime < $orig_mtime )
			{
				$this->creating = true;
			}
			else
			{
				if ( $full_init )
				{
					$this->initMe();
				}
			}
		}
		else
		{
			$this->creating = true;
		}
	}

	private function initMe ( )
	{

		if (  $this->init ) return;
		$start_time_1 = microtime( true );

		// construct from info file - copy only the relevant fields from the unserialized object
		$temp_content =  file_get_contents( $this->file_name ) ;

		$end_time_1 = microtime( true );

		// when in creation mode - $this->tag_list[1] is the first tag
		// $this->tag_list[0] points to the general_data
		$all_data = explode ( "\n" , $temp_content , 2 );

		$end_time_2 = microtime( true );

		if ( count ( $all_data ) < 1 )
		{
			// some error - ignore the current file
			$this->creating = true;
			return;
		}
		$this->general_info = $all_data[0];

		$index =1;

		$start_time_3 = microtime( true );
		$tag_data = $all_data[1];
		$number_of_tags = strlen ( $all_data[1] ) / 24  ;// 6 integers
		$number_of_ints = $number_of_tags * 6;
		$unpack_pattern = "N$number_of_ints";

		$tag_array = unpack ( $unpack_pattern , $tag_data );

		$end_time_3 = microtime( true );

		for ( $i=0; $i <= $number_of_tags  ; $i++  )
		{
			$this->tag_list[] = FlvTag::fromAsciiArray( $tag_array , $index ) ;
		}

		$this->current_tag_index=0; // when in creation mode - $this->tag_list[1] is the first tag

		$end_time_4 = microtime( true );

		$time_1 = $end_time_1 - $start_time_1;
		$time_2 = $end_time_2 - $start_time_1;
		$time_3 = $end_time_3 - $start_time_3;
		$time_4 = $end_time_4 - $start_time_1;

		$this->init = true;
	}

	// leave out the FlvTag object creation and do the calculation lower level
	// the code was take from myFlvWrapper::clipVideoFlv and was customized to work on integers rather than objects
	public function clipVideoFlv ( $clip_from_milliseconds = 0 , $clip_to_milliseconds = 2147483647 ) // , $dump_to_file_name = null , $dump_header = false ) // max-int
	{
		$dump_to_file_name = null;

		if ( $clip_from_milliseconds >= $clip_to_milliseconds )
		{
			// error = clip will contain no data
			return array ( -1 , -1 , -1 );
		}

		$accumulated_bytes = 0;

		$start_time_1 = microtime( true );

		// construct from info file - copy only the relevant fields from the unserialized object
		$temp_content =  file_get_contents( $this->file_name ) ;

		$end_time_1 = microtime( true );

		// when in creation mode - $this->tag_list[1] is the first tag
		// $this->tag_list[0] points to the general_data
		$all_data = explode ( "\n" , $temp_content , 2 );

		$end_time_2 = microtime( true );

		if ( count ( $all_data ) < 1 )
		{
			// some error - ignore the current file
			$this->creating = true;
			return;
		}
		$this->general_info = $all_data[0];

		$index =1;

		$start_time_3 = microtime( true );
		$tag_data = $all_data[1];
		$number_of_tags = strlen ( $all_data[1] ) / (4*self::NUMBER_OF_FIELDS  );// 6 integers
		$number_of_ints = $number_of_tags * self::NUMBER_OF_FIELDS;
		$unpack_pattern = "N$number_of_ints";
		//$unpack_pattern = "N";

		//		echo "($number_of_tags) ($number_of_ints) ($unpack_pattern)";

		$tag_array = unpack ( $unpack_pattern , $tag_data );

		$end_time_3 = microtime( true );

		$this->current_tag_index=0; // when in creation mode - $this->tag_list[1] is the first tag

		$end_time_4 = microtime( true );

		$time_1 = $end_time_1 - $start_time_1;
		$time_2 = $end_time_2 - $start_time_1;
		$time_3 = $end_time_3 - $start_time_3;
		$time_4 = $end_time_4 - $start_time_1;

		//sfLogger::getInstance()->warning ( "FlvInfo: Creation time: ($time_1) ($time_2) ($time_3) " );

		//		echo "Creation time: ($time_1) ($time_2) ($time_3) ($time_4)\n";
			
		$clip_from = $clip_from_milliseconds; // / 1000;
		$clip_to = $clip_to_milliseconds ; // / 1000;

		$from_index = -1;
		$to_index = -1;
		$from_byte = -1;
		$to_byte = -1;

		$first_tag = false;

		// $this->index , $this->first_byte ,$this->tag_type  , $this->getSize() , $this->timestamp , $this->keyframe
		for ( $i=1 ; $i < $number_of_ints ; $i+=self::NUMBER_OF_FIELDS  )
		{
			$index = 		$tag_array[$i];
			$first_byte = 	$tag_array[$i+1];
			$tag_type = 	$tag_array[$i+2];
			$size = 		$tag_array[$i+3] + 15;
			$timestamp =   	$tag_array[$i+4];
			$keyframe =   	$tag_array[$i+5];

			//			$last_used_tag = $tag;

			// we won't decide according to tags that are not relevant
			if ( ! $this->ifOfRelevantType ( $tag_type )) continue;

			// see if its time to set the first tag
			if ( $this->isKeyframe ( $keyframe ) && ($timestamp <= $clip_from || !$first_tag && $timestamp > $clip_from) )
			{
				// for the first time - start the indexes
				$from_index =  $index; // index
				$from_byte = $first_byte; // first_byte

				$first_tag_timestamp = $timestamp;
				$first_tag = true;
			}

			if ( $timestamp >= $clip_from && $timestamp <= $clip_to )
			{
				$accumulated_bytes += $size;
//				echo "$tag_type $timestamp $size\n";				
			}
			

			// see if its time to wrap things up...
			if ( $timestamp > $clip_to )
			{
				$to_index = $index; // index
				$to_byte = $first_byte + $size; // first_byte + size
				$accumulated_bytes += $size;
				break;
			}
		}

		if ( $to_byte < 0 )
		{
			// didn't find a tag that can close the clipping - use the last one
			$to_index = $index;
			$to_byte = $first_byte + $size;
		}

			
		// in case the first tag was not set
		if ( ! $first_tag )
		{
			$duration = 0;
			//$from_byte = 0;
		}
		else
		{
			$duration = $timestamp - $first_tag_timestamp;
		}

		$total_bytes = $this->useAccumulatedBytes() ? $accumulated_bytes : (int)( $to_byte - $from_byte );
		//$total_bytes = $accumulated_bytes ;

		// added the duration as 4rt parameter & from_byte & to_byte as 5 and 6
		return array ( $total_bytes , $from_index , $to_index , $duration , $from_byte , $to_byte );

	}

	protected function ifOfRelevantType ( $tag_type )
	{
		return ( $tag_type == myFlvWrapper::TYPE_VIDEO ) ;
	}

	protected function isKeyframe ( $tag_keyframe )
	{
		return $tag_keyframe;
	}

	// in general - for video using accumulated bytes should be the same as using end-start bytes calculation.
	// to make sure we don't have backward compatibility problems, I'm leaving the calculation as it was.
	protected function useAccumulatedBytes()
	{
		return false;
	}

	public function addTag ( FlvTag $tag )
	{
		if ( ! $this->creating ) return;

		$this->very_last_tag = $tag ;
		if ( $tag->tag_type == myFlvWrapper::TYPE_VIDEO )
		{
			$this->very_last_video_tag = $tag;
		}
		
		// add only keyframes and the very last tag
		if ( $tag->keyframe )
		{
			$this->tag_list[] = $tag;
		}
			
	}

	public function getNextTag ()
	{
		if ( !$this->init )
		{
			$this->initMe();
		}

		if ( $this->current_tag_index >= count ( $this->tag_list ) ) return null;

		$res = $this->tag_list[$this->current_tag_index];
		$this->current_tag_index++;

		return $res;
		//return unpack ( "NNNNNN" , $res );
	}

	public function gotoLastTag ()
	{
		$this->current_tag_index = count ( $this->tag_list) -1;
	}

	public function creating()
	{
		return $this->creating;
	}

	public function close ()
	{
		if ( ! $this->creating ) return;
		// save to disk
		// when in creation mode - $this->tag_list[0] is the first tag
		$str = $this->general_info . "\n";
		//		$str .= implode ( "," ,$this->tag_list  );

		$temp_tag = null;
		if ( is_array ( $this->tag_list ))
		{
			foreach ( $this->tag_list as $tag )
			{
				$res = pack ( "N6" , $tag->index , $tag->first_byte ,$tag->tag_type  , $tag->getSize() - 15, $tag->timestamp , $tag->keyframe );
				$str .= $res; //$tag->toAsciiString();
				$temp_tag = $tag;
			}
		}

		// add the very last tag to end the clip (if it wasn't a keyframe and was not added already)
		if ( $this->very_last_video_tag != $temp_tag )
		{
			
			$tag = $this->very_last_video_tag;
			if ( $tag )
			{
				$res = pack ( "N6" , $tag->index , $tag->first_byte ,$tag->tag_type  , $tag->getSize() - 15, $tag->timestamp , $tag->keyframe );
				$str .= $res; //$tag->toAsciiString();
			}
		}
		
		if ( $this->very_last_tag != $this->very_last_video_tag )
		{
			
			$tag = $this->very_last_tag;
			$res = pack ( "N6" , $tag->index , $tag->first_byte ,$tag->tag_type  , $tag->getSize() - 15, $tag->timestamp , $tag->keyframe );
			
			$str .= $res; //$tag->toAsciiString();
		
		}
		file_put_contents( $this->file_name , $str );
	}
}


class FlvInfoAudio extends FlvInfo
{
	protected $FILE_SUFFIX = ".info-audio";

	public function clipAudioFlv ( $clip_from_milliseconds = 0 , $clip_to_milliseconds = 0 )
	{
		return parent::clipVideoFlv( $clip_from_milliseconds , $clip_to_milliseconds );
	}
	 
	public function addTag ( FlvTag $tag )
	{
		if ( ! $this->creating ) return;

		$this->very_last_tag = $tag ;

		// add only keyframes and the very last tag
		if ( $tag->tag_type == myFlvWrapper::TYPE_AUDIO )
		{
			$this->tag_list[] = $tag;
		}
	}

	protected function ifOfRelevantType ( $tag_type )
	{
		return ( $tag_type == myFlvWrapper::TYPE_AUDIO ) ;
	}

	// every audio tag is considered a keyframe
	protected function isKeyframe ( $tag_keyframe )
	{
		return true;
	}

	// because the audio is NOT served in a row, but rather as separated tags,
	// we should use the accumulates bytes rather than a end-start calculation
	protected function useAccumulatedBytes()
	{
		return true;
	}

}

// this class helps create the metadata for an flv file
// using a cached version of the serialized FLV_Util_AMFSerialize as a basic template
class FlvMetaData
{
	protected $FILE_SUFFIX = ".metadata";

	private $file_name;
	private $creating = false;

	private $tag_list;
	private $sizeList;
	private $current_tag_index=0;

	private $total_bytes = 0;
	private $sizeListTime = 0;
	private $duration = 0;

	private $keyframeTimes;
	private $keyframeBytes;

	private $tag_count = 0;

	public function __construct( $original_flv_file_name )
	{
		$original_flv_file_name = kFile::fixPath( $original_flv_file_name );
		$this->file_name = $original_flv_file_name . $this->FILE_SUFFIX;

		$this->creating = false;

		// check if the info file exists and is newer than the original
		if ( file_exists ( $this->file_name ) )
		{
			$orig_mtime = filemtime  ( $original_flv_file_name ) ;
			$info_mtime = filemtime  ( $this->file_name );

			if ( $info_mtime < $orig_mtime )
			$this->creating = true;
			else
			{
				// load from disk - replace the dynamic variables
			}
		}
		else
		{
			$this->creating = true;
			// TODO - remove !!
			$this->sizeList = array ();
			$this->keyframeTimes = array();
			$this->keyframeBytes = array();
		}
	}


	public function getMetadata ( $duration , $timeOffset , $bytesOffset )
	{
	 // TODO - fetch from disk and replace dynamic values
		// lazy load from disk
		if ( ! file_exists( $this->file_name ))
		{
			kLog::log( __METHOD__ . ": file [" . $this->file_name . "] does not exist" );
			return null;	
		}
		
		$content = file_get_contents( $this->file_name );

		//		sfLogger::getInstance()->warning ( "getMetadata: " . $this->file_name );
			
		return $content;
	}


	public function addTag ( FlvTag $tag )
	{
		if ( ! $this->creating ) return;

		$dump =  $tag->dump();

		$currentTimeStamp = $tag->timestamp;

		$this->total_bytes += strlen($dump);

		if ( $this->shouldAddToArray ( $tag ) )
		{
			$this->keyframeTimes [$this->tag_count] = $currentTimeStamp;
			$this->keyframeBytes [$this->tag_count] = $this->total_bytes;
			$this->tag_count++;
		}
		// update the duration every time
		if ( $currentTimeStamp > $this->duration )
		{
			$this->duration = $currentTimeStamp;
		}
	}

	protected function shouldAddToArray ( $tag )
	{
		return 	$tag->keyframe;
	}

	public function creating()
	{
		return $this->creating;
	}

	public function close ()
	{
		if ( ! $this->creating ) return;
		// save to disk
		$amfSerializer = new FLV_Util_AMFSerialize();

		$metadata = array();
		$metadata_data = array();

		// theses will be used as placeholders for dynamic data
		$metadata_data["duration"] = (int)($this->duration/1000) ;
		$metadata_data["timeOffset"] = 0 ;
		$metadata_data["bytesOffset"] = 0 ;

		// TODO - remove !!
		//$metadata_data["bufferSizes"] = $this->sizeList;
		$metadata_data["times"] = $this->keyframeTimes ;
		$metadata_data["filepositions"] = $this->keyframeBytes;

		$res = $amfSerializer->serialize( 'onMetaData') . $amfSerializer->serialize( $metadata_data );

		// first create a metadata tag with it's real size - this will be the offset of all the rest of the tags
		// create a metadata tag
		$metatag = new FlvTag();
		$metatag->setPrevTagSize ( 0 );
		$metatag->setTagType ( 0x12 );
		$metatag->setTimestamp ( 0 );
		$metatag->setData (  $res );

		$metatagSize = $metatag->getSize();;
		/*
		 // TODO - remove
		 // second - create the real metadata tag with the values of all the following tags with correct offsets
		 for ($i = 0 ; $i < count ( $this->sizeList ) ; ++$i )
		 {
			$this->sizeList[$i] += $metatagSize;
			}
			$metadata_data["bufferSizes"] = $this->sizeList;
			*/
		// fix the bytes' offsets
		for ($i = 0 ; $i < count ( $this->keyframeBytes ) ; ++$i )
		{
			$metadata_data["filepositions"][$i] += $metatagSize;
		}

		$res = $amfSerializer->serialize( 'onMetaData') . $amfSerializer->serialize( $metadata_data  );

		$metatag->setData (  $res );

		$str = $metatag->dump( true );

		$amfSerializer = null;
		$metatag = null;
		file_put_contents( $this->file_name , $str );
	}
}

class FlvMetaDataAudio extends FlvMetaData
{
	protected $FILE_SUFFIX = ".metadata-audio";

	protected function shouldAddToArray ( $tag )
	{
		return 	( $tag->tag_type == myFlvWrapper::TYPE_AUDIO );
	}

}
