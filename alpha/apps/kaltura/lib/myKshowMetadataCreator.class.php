<?php
require_once ( "requestUtils.class.php");


class extendedEntryMediaType
{
	public $asset_type = NULL;
	public $type = "";
}

class urlName
{
	public $url = "";
	public $name = "";
}


class myKshowMetadataCreator
{
	
	private static $IMAGE_EXT = array ( "jpg" , "gif" , "bmp" , "png" );
	private static $VIDEO_EXT = array ( "flv" );
	private static $SWF_EXT = array ( "swf" );
	private static $AUDIO_EXT = array ( "mp3" );
	private static $RTMP_PREFIX = array ( "rtmp" , "rtmpt" );
	
	
	const TYPE_RTMP = "RTMP";
	const TYPE_PD = "VIDEO";
	const TYPE_SWF = "SWF";
	const TYPE_FVSS = "FVSS";
	const TYPE_IMG = "IMG";
	const TYPE_EMPTY = "EMPTY";
	const TYPE_UNKNOWN = "?";
		
	const VIDEO_ASSET = 1;
	const VOICE_ASSET = 2;
	const AUDIO_ASSET = 3;
	const OBJECT_ASSET = 4;
	
	private $m_video_assets = NULL;
	private $m_audio_assets = NULL;
	private $m_voice_assets = NULL;
	private $m_object_assets = NULL;

	private $m_seq_play_time = 0;
	private $m_video_seq_play_time = 0;
	private $m_audio_seq_play_time = 0;
	private $m_voice_seq_play_time = 0;
	private $m_object_seq_play_time = 0;
	
	public function createMetadata ( $kshow_id )
	{
//		echo ( "createMetadata for [$kshow_id]\n" );
		$kshow = kshowPeer::retrieveByPK( $kshow_id );
		$show_entry_id = $kshow->getShowEntryId();
		$intro_id = $kshow->getIntroId();
		// fetch all entries for a kshow without the kshow entry or the intro
		// the order is ascending by creation date of the entry

		// if ordering by ascending ID - the intro will always be first
		$c = new Criteria ();
		$c->add ( entryPeer::KSHOW_ID , $kshow_id );
		$c->add ( entryPeer::ID , array ( $show_entry_id , $intro_id ) , Criteria::NOT_IN );
//		$c->addDescendingOrderByColumn('(' . entryPeer::ID . '=' . $intro_id . ')');
		$c->addAscendingOrderByColumn( entryPeer::ID );

//		$c->add ( entryPeer::STATUS , entry::ENTRY_STATUS_READY );
//		$c->addAscendingOrderByColumn( entryPeer::CREATED_AT );
		$entry_list = entryPeer::doSelect( $c );
		
//		echo ( "kshow [$kshow_id] has " . count ( $entry_list ) . " entries\n" );
		
		return $this->createMetadataForList ( $entry_list );
	}
	
	// creates the XML for all the entries in the list
	private function createMetadataForList ( $entry_list )
	{
		if ( $entry_list == NULL )
		{
			return "<xml></xml>";
		}
		
		$xml = "<xml>";
		
		$xml .= $this->beginAssetLists ();
		
		foreach ( $entry_list as $entry )
		{
			
			$extended_type = self::getEntryType ( $entry );
			$type = $extended_type->asset_type;
			
			if ( self::VIDEO_ASSET	== $type )
			{
				$this->addVideoAsset ( $entry , $extended_type);
			}
			elseif (self::AUDIO_ASSET == $type )
			{
				$this->addAudioAsset ( $entry , $extended_type);
			}
			elseif ( self::OBJECT_ASSET == $type )
			{
				$this->addObjectAsset ( $entry , $extended_type);
			}
			else
			{
				$this->addObjectAsset ( $entry , $extended_type);
			}
		}
		
		$this->finalizeAssetLists ();
		
		$xml .= $this->m_video_assets;
		$xml .= $this->m_audio_assets;
		$xml .= $this->m_voice_assets;
		$xml .= $this->m_object_assets;
		
		
		$xml .= "</xml>";
		
		return $xml;
	}
	
	private function addVideoAsset ( entry $entry , $extended_type)
	{
		
/*
 * OLD:		
		<vidAsset seqPlayTime="0" type="RTMP" name="dugag" url="rtmp://8.6.95.164:1935/kplayer/_definst_">
		<StreamInfo file_name="dugag" posX="0" posY="0" start_byte="0" end_byte="0" start_time="3.5" len_time="3.5" volume="0" pan="0" />
		<EndTransition property="alpha" type="smoothEaseOut" length="1" />
		<!--<Effect type="" length="" specialElements="" />-->
		</vidAsset>


    <vidAsset k_id="3" type="IMAGE" name="Deanna Wilkinson entry" url="http://localhost/images/templates/entry/data/13.jpg">
      <StreamInfo file_name="http://localhost/images/templates/entry/data/13.jpg" posX="0" posY="0" start_byte="-1" end_byte="-1" start_time="0" len_time="1" volume="0" pan="0" isSingleFrame="0"/>
      <EndTransition type="None" StartTime="1" length="0"/>
    </vidAsset>
    <vidAsset k_id="1402" type="VIDEO" name="Gina Mattila entry" url="http://localhost/images/templates/entry/data/9.flv" fix_status="Missing file or invalid FLV structure">
      <StreamInfo file_name="http://localhost/images/templates/entry/data/9.flv" posX="0" posY="0" start_byte="-1" end_byte="-1" start_time="103.24" len_time="45.97000000000001" volume="1" pan="0" isSingleFrame="0"/>
      <EndTransition type="None" StartTime="45.97000000000001" length="0"/>
    </vidAsset>

*/

		// create EMPTY filler (if needed)
		$asset = $this->createEmptyAsset ( "vidAsset" , $this->m_video_seq_play_time );

		$url_name = self::getUrlAndName ( $entry );
		$url = $url_name->url;
		$name = str_replace ( '"' , "&quot;" ,$url_name->name ) ;

// stub
		$len_time = ((int)($entry->getLengthInMsecs()/100)) /10;
		$start_time = 0;
		$volume = 0.5;
		
		$start_time = max ( 0 , $len_time - 1 );
		$length = $len_time - $start_time;
		$asset .= self::createEntryComment( $entry );
		
		$is_ready = $entry->getStatus() == entry::ENTRY_STATUS_READY;
		// if the entry is not ready - comment out the whole element and DON"T increment the $this->m_seq_play_time !!
		if ( ! $is_ready )
		{
			$asset .= "<!-- faild to convert. see conversion log for entry_id " . $entry->getId() . "\n" ;
		}
		$asset  .= '<vidAsset k_id="' . $entry->getId()  . '" seqPlayTime="' . $this->m_seq_play_time . '" type="' . $extended_type->type . '" name="' . $url_name->name . '" url="' . $url_name->url . '">' . "\n" .
		'<StreamInfo file_name="' . $name . '" posX="0" posY="0" start_byte="-1" end_byte="-1" start_time="' .$start_time . '" len_time="'. $len_time. '" volume="'. $volume .'" pan="0" />' . "\n" .
		'<EndTransition type="none" StartTime="' . $start_time . '" length="' . $length . '" />' . "\n" .
		'</vidAsset>' . "\n";
		
		if ( ! $is_ready )
		{
			$asset .= "-->" . "\n";
		}
		else
		{
			// increment the counter only if ready 
			$this->m_seq_play_time += 	$len_time;
		}
		
		$this->m_video_assets .= $asset;
		$this->m_video_seq_play_time = $this->m_seq_play_time;
	}

/*	
	private function addVideoEmpty ( $entry , $len_time )
	{
		$asset = self::createEntryComment( $entry );
		$asset .= self::createEmptyAsset( "vidAsset" , $this->m_seq_play_time  ,  $len_time );
		$this->m_video_assets .= $asset;
	}
	*/

	private function addAudioAsset ( $entry , $extended_type)
	{
		
		/*
		 <AudAsset seqPlayTime="0" type="FLV" name="savage" url="rtmp://8.6.95.164:1935/kplayer/_definst_">
			<StreamInfo file_name="savage" start_time="5" len_time="4" volume="0.3" pan="0" />
		</AudAsset>

		 */

		// create EMPTY filler (if needed)
		$asset = $this->createEmptyAsset ( "AudAsset" , $this->m_audio_seq_play_time );
		
		$url_name = self::getUrlAndName ( $entry );
		// stub
		$len_time = 8;
		$start_time = 2;
		$volume = 0.5;

		$asset .= self::createEntryComment( $entry );
		$asset .= '<AudAsset seqPlayTime="' . $this->m_seq_play_time . '" type="' . $extended_type->type . '" name="' . $url_name->name . '" url="' . $url_name->url . '">' . "\n" .
		'<StreamInfo file_name="' . $name . '"  start_time="' .$start_time . '" len_time="'. $len_time. '" volume="'. $volume .'" pan="0" />' . "\n" .
		'</AudAsset>' . "\n" ;

		$this->m_audio_assets .= $asset;

		// make sure there is an EMPTY in the video asset list
//		$this->addVideoEmpty( $entry, $len_time);
		
		
		$this->m_seq_play_time += 	$len_time;
		$this->m_audio_seq_play_time = $this->m_seq_play_time;
	}

	
	private function addObjectAsset ( $entry , $extended_type )
	{
		/*
		 <ldrAsset seqPlayTime="0" type="SWF" name="levLevitated" url="swf_n_img">
			<StreamInfo file_name="levLevitated.swf" posX="30" posY="50" len_time="2.5" sizeW="200" sizeH="120" />
			<!--<EndTransition property="alpha" type="smoothEaseOut" length="1" />
			<Effect type="" length="" specialElements="" />-->
			</ldrAsset>

		 */

		// create EMPTY filler )if needed)
		$asset = "" ; //$this->createEmptyAsset ( "ldrAsset" , $this->m_object_seq_play_time );
		
		$url_name = self::getUrlAndName ( $entry );

		// stub
		$len_time = 4;
		$start_time = 0;
		$volume = 0.5;

		$asset .= self::createEntryComment( $entry );
		
		if ( $extended_type->type == self::TYPE_UNKNOWN )		$asset .= "<!-- " . "\n" ;
		$asset .= '<ldrAsset seqPlayTime="' . $this->m_seq_play_time . '" type="' . $extended_type->type . '" name="' . $url_name->name . '" url="' . $url_name->url . '">' . "\n" .
		'<StreamInfo file_name="' . $url_name->name . '"  len_time="'. $len_time . '" posX="30" posY="50"  sizeW="200" sizeH="120" />' . "\n" .
		'</ldrAsset>' . "\n" ;
		if ( $extended_type->type == self::TYPE_UNKNOWN )		$asset .= " --> ". "\n";
		
		$this->m_object_assets .= $asset;

		// make sure there is an EMPTY in the video asset list
//		$this->addVideoEmpty( $entry,$len_time);


		$this->m_seq_play_time += 	$len_time;
		$this->m_object_seq_play_time = $this->m_seq_play_time;
	}
	


	private function createEmptyAsset( $asset_tag , &$seq_play_time)
	{
		$str = "";
		$len_time =  $this->m_seq_play_time - $seq_play_time ;
		if ( $len_time > 0 )
		{
			// have to fill the gap
			$str = self::createEmptyAssetImpl ( $asset_tag , $seq_play_time , $len_time );
			$str .= "\n";
			$seq_play_time = $this->m_seq_play_time ;
		}

		return $str;
	}

	private static function createEmptyAssetImpl ( $asset_tag , $seq_play_time , $len_time )
	{

/*
 * 
  		<vidAsset seqPlayTime="10.7" type="EMPTY" >
 			<StreamInfo len_time="2.3" />
 		</vidAsset>
 *
 */	
			
	$str = "<$asset_tag seqPlayTime=\"$seq_play_time\" type=\"EMPTY\">". "\n" . 
		"<StreamInfo len_time=\"$len_time\" />" . "\n" .	
		"</$asset_tag>" . "\n";
			
		return $str;
	}
	
	private static function createEntryComment ( $entry )
	{
		return "<!-- id:" . $entry->getId() . "-->\n";
	}
	
	// check the entry type - but more importatn - check the extension of the data
	private static function getEntryType ( $entry )
	{
		$extended_entry_type = new extendedEntryMediaType();
		$entry_type = $entry->getMediaType(); // this is assumed to be correct 

		$data = kFileSyncUtils::getReadyLocalFilePathForKey($entry->getSyncKey(entry::FILE_SYNC_ENTRY_SUB_TYPE_DATA)); // replaced__getDataPath
		
		$ext = pathinfo($data, PATHINFO_EXTENSION );

		
		if ( $ext == NULL || $ext == "" )
		{

			// if tehre is no extensio - ASSUME : entry::ENTRY_MEDIA_TYPE_VIDEO
			$extended_entry_type->asset_type = self::VIDEO_ASSET;
			if ( kString::beginsWith( $data , self::$RTMP_PREFIX ) )
			{
				$extended_entry_type->type = self::TYPE_RTMP;
			}
			else
			{
				$extended_entry_type->type = self::TYPE_PD ;// assume progressive download
			} 
		}
		else
		{
			// there is an extension -
			if ( in_array ( $ext , self::$IMAGE_EXT ))
			{
				$extended_entry_type->asset_type = self::VIDEO_ASSET;
				$extended_entry_type->type = self::TYPE_IMG;
			}
			elseif ( in_array( $ext , self::$VIDEO_EXT ))
			{
				$extended_entry_type->asset_type = self::VIDEO_ASSET;
				$extended_entry_type->type = self::TYPE_PD;

				// for now - use RTMP as the download method for video
				//$extended_entry_type->type = self::TYPE_RTMP;
				
			}
			elseif ( in_array( $ext , self::$SWF_EXT ))
			{
				$extended_entry_type->asset_type = self::OBJECT_ASSET;
				$extended_entry_type->type = self::TYPE_SWF;
			}
			elseif ( in_array( $ext , self::$AUDIO_EXT ))
			{
				$extended_entry_type->asset_type = self::AUDIO_ASSET;
				$extended_entry_type->type = self::TYPE_PD;
			}
			else
			{
				// choose some default for unknown extensions
				$extended_entry_type->asset_type = self::OBJECT_ASSET;
				$extended_entry_type->type = self::TYPE_UNKNOWN;
			}
				
		}
	
		return $extended_entry_type;
	}
	
	private static function getUrlAndName ( $entry )
	{
		$data = kFileSyncUtils::getReadyLocalFilePathForKey($entry->getSyncKey(entry::FILE_SYNC_ENTRY_SUB_TYPE_DATA)); // replaced__getDataPath
		$entry_type = self::getEntryType ( $entry );
		if ( $entry_type->type == self::TYPE_RTMP )
		{
			// the url should be where the RTMP service is 
			$url = self::getRTMPPath ();
			// the name should start with the content directory
			// and should not include the file extension !
			$name = pathinfo($data, PATHINFO_DIRNAME) . "/" . kFile::getFileNameNoExtension( $data );
		}
		else
		{
			$url = requestUtils::getHost() . $data ;
			$name = pathinfo($data, PATHINFO_BASENAME);
		}
		
		$url_name = new urlName ();
		$url_name->url = $url;
		$url_name->name = $name;
		
		return $url_name;
	}
	
	private function beginAssetLists ()
	{
		if ( $this->m_video_assets == NULL ) $this->m_video_assets .= "<VideoAssets>" . "\n";
		if ( $this->m_audio_assets == NULL ) $this->m_audio_assets .= "<AudioAssets>" . "\n" ;
		if ( $this->m_voice_assets == NULL ) $this->m_voice_assets .= "<VoiceAssets>" . "\n" ;
		if ( $this->m_object_assets == NULL ) $this->m_object_assets .= "<LoaderObjectAssets>" . "\n";
	}

	private function finalizeAssetLists ()
	{
		if ( $this->m_video_assets != NULL ) $this->m_video_assets .= "</VideoAssets>" . "\n" ;
		if ( $this->m_audio_assets != NULL ) $this->m_audio_assets .= "</AudioAssets>" . "\n" ;
		if ( $this->m_voice_assets != NULL ) $this->m_voice_assets .= "</VoiceAssets>" . "\n" ;
		if ( $this->m_object_assets != NULL ) $this->m_object_assets .= "</LoaderObjectAssets>" . "\n";
	}

	private static function getContentPath ()
	{
		return requestUtils::getHost () ;
	}
	
	private static function getRTMPPath ()
	{
		$url = "rtmp://";
		$url .= $_SERVER['HTTP_HOST'];

		// add herd-coded the kplayer service
		$url .= ":1935/kplayer/_definst_";
		
		return $url;
	}

}


?>