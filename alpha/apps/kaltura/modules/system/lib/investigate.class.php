<?php
class investigate
{
	private static function printFieldNames ( $arr , $add_tr = true )
	{
		$str = $add_tr ? "<tr style='font-size:11px;'>" : "";
		foreach ( $arr as $name )
		{
			$prop = str_replace ("_" , " " , $name );
			$str .= "<td>$prop</td>";
		}
		$str .= $add_tr ?  "</tr>" : "";
		return $str;
	}
	
	private static function printFields ( $obj , $arr , $styles = null , $add_tr = true , $extra_fields = null )
	{
		if ( ! $obj ) return;
		$str = $add_tr ? "<tr>" : "";
		foreach ( $arr as $name )
		{
			$extra = "";
			$style = "";
			$is_link = false;
			$prop = str_replace ( "_" , "" , $name );
			$getter = "get{$prop}";
			$val = call_user_func ( array ( $obj , $getter ) );
			if ( $styles &&  @$styles[$name]  && $val ) $style=$styles[$name];
			if ( $extra_fields && @$extra_fields[$name] )
			{
				$extra = str_replace ( "{?}" , $val , $extra_fields[$name] ); //  replace the special string {?} with the values  of the parameter
				$is_link = true; // assume that when there is extra_fields - this should be a link. It is not a safe assumption, but good for IDs to stat with
			}
			$link_name = "td_" . get_class ( $obj ) . "_" . $obj->getId();
			$str .= "<td $extra style='{$style}'>" . ( $is_link ? "<a name='$link_name' href='javascript:void()'>" . $val . "</a>" : $val ) . "</td>";
		}
		$str .= $add_tr ?  "</tr>" : "";
		return $str;
	}
		
	
	private static $not_type_statuses = array(
		"1" => "PENDING",
		"2" => "SENT",
		"3" => "ERROR",
		"4" => "SHOULD_RESEND",
		"5" => "ERROR_RESENDING",
		"6" => "SENT_SYNCH");
	
	private static $not_type_texts = array(
		"1" => "ENTRY_ADD",
		"2" => "ENTRY_UPDATE_PERMISSIONS",
		"3" => "ENTRY_DELETE",
		"4" => "ENTRY_BLOCK",
		"5" => "ENTRY_UPDATE",
		"6" => "ENTRY_UPDATE_THUMBNAIL",
		"11" => "KSHOW_ADD",
		"12" => "KSHOW_UPDATE_INFO",
		"13" => "KSHOW_DELETE",
		"14" => "KSHOW_UPDATE_PERMISSIONS",
		"15" => "KSHOW_RANK",
		"16" => "KSHOW_BLOCK");
	
	private static $not_result_texts = array(
		"0" => "OK", 
		"-1" => "ERROR_RETRY", 
		"-2" => "ERRROR_NO_RETRY");
	
	public static function addIdIfNotNull ( $arr , $obj )
	{
		if ( $obj ) $arr[] = $obj;
	}
	
	public static function formatEntryType ( $val )
	{
		return $val;
		$str = "($val) ";
		$str .= ( $val== 0 ? "BACKGROUND" : ( $val == 1 ? "MEDIACLIP" : "SHOW" ) );
		return $str;
	}

	public static function printEntryTypeStr ( )
	{
		$NL = "\n";
			$str = "0=BACKGROUND" . $NL .
				"1=MEDIACLIP" . $NL .
				"2=SHOW"  ;
		return $str;
	}
	
	public static function formatEntryStatus ( $val )
	{
		return $val;
		
		$str = "($val) ";
		switch ( $val )
		{
			case entryStatus::ERROR_CONVERTING:
				$str .= "ERROR_CONVERTING"; break;
			case entryStatus::IMPORT:
				$str .= "import"; break;
			case entryStatus::PRECONVERT:
				$str .= "PRECONVERT"; break;
			case entry:: ENTRY_STATUS_READY:
				$str .= "READY"; break;
			case entryStatus::DELETED:
				$str .= "DELETED"; break;
//			case entryStatus::MODETATE:
//				$str .= "MODERATE"; break;
				
		}
		return $str;
	}

	public static function printEntryStatusStr ( )
	{
		$NL = "\n";
		$str = 
			entryStatus::ERROR_CONVERTING . "=ERROR_CONVERTING" . $NL .
			entryStatus::IMPORT . "=IMPORT" . $NL .
			entryStatus::PRECONVERT . "=PRECONVERT" . $NL .
			entryStatus::READY . "=READY" . $NL . 
			entryStatus::DELETED . "=DELETED" . $NL ;
//			entryStatus::MODETATE . "=MODERATE" . $NL ;
	
		return $str;
	}
	
	public static function entryStatusColor ( $val )
	{
		$str = "";
		switch ( $val )
		{
			case entryStatus::ERROR_CONVERTING:
				$str .= "red"; break;
			case entryStatus::IMPORT:
				$str .= "yellow"; break;
			case entryStatus::PRECONVERT:
				$str .= "#66CCFF"; break;
			case entryStatus::READY:
				$str .= "lime"; break;
			case entryStatus::DELETED:
				$str .= "#CCCCCC"; break;
			case entryStatus::MODERATE:
				$str .= "orange"; break;
				
		}
		return $str;
	}

	/*
	 constBatchJobType::CONVERT = 0;
	 constBatchJobType::IMPORT = 1;

	 const BATCHJOB_SUB_TYPE_YOUTUBE = 0;
	 const BATCHJOB_SUB_TYPE_MYSPACE = 1;
	 const BATCHJOB_SUB_TYPE_PHOTOBUCKET = 2;
	 const BATCHJOB_SUB_TYPE_JAMENDO = 3;
	 const BATCHJOB_SUB_TYPE_CCMIXTER = 4;

	 const BATCHJOB_STATUS_PENDING = 0;
	 const BATCHJOB_STATUS_QUEUED = 1;
	 const BATCHJOB_STATUS_PROCESSING = 2;
	 const BATCHJOB_STATUS_PROCESSED = 3;
	 const BATCHJOB_STATUS_MOVEFILE = 4;
	 const BATCHJOB_STATUS_FINISHED = 5;
	 const BATCHJOB_STATUS_FAILED = 6;
	 const BATCHJOB_STATUS_ABORTED = 7;
	 */

	private static $job_type_map = array (
		BatchJobType::CONVERT => "BATCHJOB_TYPE_CONVERT",
		BatchJobType::IMPORT => "BATCHJOB_TYPE_IMPORT",
		BatchJobType::DELETE => "BATCHJOB_TYPE_DELETE",
		BatchJobType::FLATTEN => "BATCHJOB_TYPE_FLATTEN",
		BatchJobType::BULKUPLOAD => "BATCHJOB_TYPE_BULKUPLOAD", 
		BatchJobType::DVDCREATOR => "BATCHJOB_TYPE_DVDCREATOR",
		BatchJobType::DOWNLOAD => "BATCHJOB_TYPE_DOWNLOAD",
		BatchJobType::OOCONVERT => "BATCHJOB_TYPE_OOCONVERT",
		BatchJobType::CONVERT_PROFILE => "BATCHJOB_TYPE_CONVERT_PROFILE",
		BatchJobType::POSTCONVERT => "BATCHJOB_TYPE_POSTCONVERT",
		BatchJobType::PULL => "BATCHJOB_TYPE_PULL",
		BatchJobType::REMOTE_CONVERT => "BATCHJOB_TYPE_REMOTE_CONVERT",
		BatchJobType::EXTRACT_MEDIA => "BATCHJOB_TYPE_EXTRACT_MEDIA",
		BatchJobType::MAIL => "BATCHJOB_TYPE_MAIL",
		BatchJobType::NOTIFICATION => "BATCHJOB_TYPE_NOTIFICATION",
		BatchJobType::CLEANUP => "BATCHJOB_TYPE_CLEANUP",
		BatchJobType::SCHEDULER_HELPER => "BATCHJOB_TYPE_SCHEDULER_HELPER",
		BatchJobType::BULKDOWNLOAD => "BATCHJOB_TYPE_BULKDOWNLOAD",
		BatchJobType::DB_CLEANUP => "BATCHJOB_TYPE_DB_CLEANUP",
	);
	public static function formatBatchJobType ( $type )
	{
		$str = @self::$job_type_map[$type];
		return ( $str ? $str : "[$type]" );
	}

	public static function printFileData ( $files )
	{
		$str = '<table border=1 cellspacing=0	style="font-family:verdana; font-size:12px">' .
		'<tr>' .
		'<td>Full Path</td>' .
		'<td>Name</td>' .
		'<td>Ext</td>' .
		'<td>Exists</td>' .
		'<td>Size</td>' .
		'<td>Date</td>' .
		'<td>&nbsp;</td>' .
		'</tr>' ;

		if ( ! is_array( $files ) ) { $files = array ( $files ); }
		foreach ( $files as $file )
		{
			$has_content = ! empty ( $file->content ) ;

			$id = preg_replace( "/[^a-zA-Z0-9_\-]/" , "_" , $file->full_path );

			$str .= '<tr>' .
			'<td>' . $file->full_path . '</td>' .
			'<td>' . $file->name . '</td>' .
			'<td>' . $file->ext . '</td>' .
			'<td>' . $file->exists . '</td>' .
			'<td>' . $file->size . '</td>' .
			'<td>' . $file->timestamp . '</td>' .
			( $has_content ? '<td onclick="show(this,event)">X<textarea style="display:none">' . htmlspecialchars( $file->content ) . '</textarea></td>' :	'<td>&nbsp;</td>' ) .
			'</tr>' ;
		}

		$str .= 		'</table>' ;
		return $str;
	}

	public static function printEntryHeader (  )
	{
		$str = "<tr><td></td>" .
			"<td>Id(pid)</td>" .
			"<td>Name</td>" . 
			"<td width='50px'>Tags</td>" . 
			"<td width='50px'>Admin tags</td>" .
			"<td title='plays / views'>P/V</td>".
			"<td width='50px'>Kshow Id</td>".
			"<td>Kuser Id</td>".
			"<td title='" . self::printEntryStatusStr() . "'>Status</td>".
			"<td title='" . self::printEntryTypeStr() . "'>Type</td>".
			"<td width='40px'>Media Type</td>".
			"<td>Source</td>".		
//			"<td>Appears In</td>".
			"<td>Thumbnail</td>".
			"<td>Data (size)</td>".
			"<td width='80px'>Custom<br/>Data</td>".
			"<td title='conversion quality'>C/Q<br>[conv prof id]</td>".
			"<td>Duration</td>".	
			"<td>Search Text</td>".
			"<td>Discrete Search Text</td>".
			"<td width='68px'>Media Date</td>".	
			"<td width='68px'>Created At</td>".
			"<td width='68px'>Modified At</td>".
			"<td width='68px'>Updated At</td>".
		"</tr>";
		
		return $str;
	}
	
	
	public static function printEntry ( $entry , $create_link = false , $kshow = null , $text = null )
	{
		if ( $entry === NULL )	{	return ""; 	}
		
		if ( $kshow != null )
		{
			if ( $entry instanceof entry )
			{
				$entry->setKshow ( $kshow );
			}
			else if ( $entry instanceof genericObjectWrapper ) 
			{
				$entry->getWrappedObj()->setKshow ( $kshow );
			}
			//$entry->setKshowId ( $kshow->getId() );
		}
		
		if ( $entry instanceof entry )
			$entry = new genericObjectWrapper ( $entry  , true  );
		
		$id_partner_id = $entry->id . "(" . $entry->partnerId . ")<br>" . $entry->intId;
			
		$kshow_name = (  $entry->kshow ? $entry->kshow->name : "" );
		
		$thumb_url = $entry->thumbnailUrl; 
		$str = "" .
			'<tr style="vertical-align: top">' .
			'<td>' . ( $text ? $text : "&nbsp;" ) . '</td>' . 
			'<td>' . ( $create_link ? "<a href='" . url_for ( "/system" ) . "/investigate?entry_id=" . $entry->id  . "'> $id_partner_id .  </a>" : $id_partner_id ) . '<br>' . $entry->id . '</td>' .
			'<td>' . $entry->Name . ' </td>'.
			'<td>' . htmlentities( $entry->Tags , ENT_COMPAT  , "UTF-8" ). ' </td>'.
			'<td>' . htmlentities( $entry->AdminTags , ENT_COMPAT  , "UTF-8" ). ' </td>'.
			'<td>' . "p:" . $entry->plays . "<br>v: " . $entry->views . ' </td>'.
			'<td >' . "<a href='" . url_for ( "/system" ) . "/investigate?kshow_id=" . $entry->KshowId  . "'>" . $entry->KshowId . " , " . $kshow_name . '</a></td>' .
			'<td>' . $entry->KuserId . ", " . ( $entry->kuser ? $entry->kuser->screenName : "" ). '</td>'.
			'<td style="background-color:' . self::entryStatusColor ( $entry->Status) . '">' . self::formatEntryStatus ( $entry->Status ) . '</td>'.
			'<td>' . self::formatEntryType(  $entry->type  ) . '</td>'.
			'<td>' . $entry->mediaType .'</td>'.
			'<td>' . $entry->source . '</td>' .		
//			'<td>' .$entry->AppearsIn .'</td>'.
			'<td><img title="'. $thumb_url . '" width="100px" height="80px"	src="' . $thumb_url . '"></td>'.
			'<td>' . ( $entry->data ? "<a href='$entry->dataPath'>" . $entry->data . "</a><br><br>" : "" ) .
				'(' . $entry->width . "x" . $entry->height . ')' . 
				'</td>'.
			'<td><div style="width:280px">' . str_replace ( ";" , "; " , $entry->customData ) . '</div></td>'.
			'<td ><span onclick="update ( \'' . $entry->conversionQuality . '\' , \'' . $entry->id . '\' , \'conversionQuality\' );">[?]</span> ' .
				'<a href="javascript:conversionProfileMgr ( \'' . $entry->conversionQuality . '\' )"; return false;>{' . $entry->conversionQuality . '}</a><br/>' .
				"[" . $entry->conversionProfileId .']</td>'.
			'<td>' . $entry->lengthInMsecs . '</td>'.
			'<td>' . $entry->searchText . '</td>'.
			'<td>' . $entry->discreteSearchText . '</td>'.
			'<td>' . $entry->mediaDate . '</td>'.
			'<td>' . $entry->createdAt . '</td>'.
			'<td>' . $entry->modifiedAt . '</td>'.
			'<td>' . $entry->updatedAt . '</td>'.
			'</tr>' ; 
		
		return $str;
	}

/*
 * 	idint(11)
partner_idint(11)
object_typetinyint(4)
object_idvarchar(20)
versionvarchar(20)
object_sub_typetinyint(4)
dcvarchar(2)
originaltinyint(4)
created_atdatetime
updated_atdatetime
ready_atdatetime
sync_timeint(11)
statustinyint(4)
file_typetinyint(4)
linked_idint(11)
link_countint(11)
file_rootvarchar(64)
file_pathvarchar(128)
file_sizeint(11)
 */	
	public static function printFileSyncHeader (  )
	{
		$str = "<tr><td></td>" .
			"<td>id</td>" .
			"<td>partner id</td>" . 
			"<td>object_type</td>" . 
			"<td>object_id</td>" .
			"<td>version</td>".
			"<td>object_sub_type</td>".
			"<td>dc</td>".
			"<td>original</td>".
			"<td>created_at</td>".
			"<td>updated_at</td>".
			"<td>ready_at</td>".
			"<td>sync_time</td>".
			"<td>status</td>".
			"<td>file_type</td>".
			"<td>linked_id</td>".	
			"<td>link_count</td>".
			"<td>file_root</td>".
			"<td>file_path</td>".
			"<td>file_size</td>".
			"<td>disk file size</td>".
			"<td></td>";
		"</tr>";
		
		return $str;
	}

	public static function printFileSync ( $fs )
	{
		$local_file = $fs->getDc() == kDataCenterMgr::getCurrentDcId() ;
		 
		$link_id = $fs->getLinkedId();
		$link_str = $link_id ? "style='cursor:pointer' title='click to view fileSync for link' onclick='toggleFileSyncLink($link_id,event)'" : "";
		 
		$link_name = "td_fileSync_{$fs->getId()}";
		$str = "<tr " . ($local_file ? "" : "style='color:#0066FF'" ). "><td></td>" .
			"<td>{$fs->getId()}</td>" .
			"<td>{$fs->getPartnerId()}</td>" . 
			"<td>{$fs->getObjectType()}</td>" . 
			"<td>{$fs->getObjectId()}</td>" .
			"<td>{$fs->getVersion()}</td>".
			"<td>{$fs->getObjectSubType()}</td>".
			"<td>{$fs->getDc()}</td>".
			"<td>{$fs->getOriginal()}</td>".
			"<td>{$fs->getCreatedAt()}</td>".
			"<td>{$fs->getUpdatedAt()}</td>".
			"<td>{$fs->getReadyAt()}</td>".
			"<td>{$fs->getSyncTime()}</td>".
			"<td>{$fs->getStatus()}</td>".
			"<td>{$fs->getFileType()}</td>".
			"<td $link_str><a name='$link_name' href='javascript:void()'>{$fs->getLinkedId()}</a></td>".	
			"<td>{$fs->getLinkCount()}</td>".
			"<td>{$fs->getFileRoot()}</td>".
			"<td>{$fs->getFilePath()}</td>".
			"<td>{$fs->getFileSize()}</td>";
		
			if ( $local_file )
			{
				$actual_file_size = @filesize( $fs->getFullPath() );
				if ( ! $actual_file_size  ) $actual_file_size =0;
				if ( $actual_file_size != $fs->getFileSize() )
					$str .= "<td style='color:red'>";
				else
					$str .= "<td style='color:green'>";
				$str .= $actual_file_size;
				$str .= "</td>";
			}
			else
			{
				$str .= "<td>&nbsp;</td>";
			}
			
			$content = null;
			if ( $local_file )
			{
				if ( $fs->getObjectType() == FileSync::FILE_SYNC_OBJECT_TYPE_FLAVOR_ASSET && 
					$fs->getObjectSubType() == flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_CONVERT_LOG )
				{
					$path = $fs->getFileRoot() . $fs->getFilePath();
					$content = @file_get_contents( $path );
				} 
			}
			
		$str .= $content ? '<td onclick="show(this,event)">X<textarea style="display:none">' . htmlspecialchars( $content ) . '</textarea></td>' :	'<td>&nbsp;</td>'  ;
		$str .= "</tr>";	

		return $str;
	}
	
	public static $flavorAssetParams =  array ( 
	   "id",    "int_id",    "partner_id",    "tags",    "created_at",    "updated_at",    "deleted_at",
	    "entry_id",    "flavor_params_id",    "status",    "version",    "description",    "width",    "height",
	    "bitrate",    "frame_rate",    "size",    "is_original",  /*  "file_ext",*/  
	);
	
	public static function printFlavorAssetHeader (  )
	{
		return self::printFieldNames ( self::$flavorAssetParams );
	}

	public static function printFlavorAsset ( $fa , $tr_style )
	{
		$str = "<tr style='$tr_style'>";
		$str .= self::printFields ( $fa , self::$flavorAssetParams , array ( "description" => "color:red" ) , false , array ( "id" => "onclick='toggleDisplay(\"flavor_asset_details_{?}\")'" ) );
		$str .= "</tr>";
		return $str;
	}
	

	private static $mediaInfoParams =  array ( 
	     "id",    "created_at",   "updated_at",    "flavor_asset_id",    "file_size",
    "container_format",    "container_id",    "container_profile",    "container_duration",    "container_bit_rate",	
    "video_format",    "video_codec_id",    "video_duration",    "video_bit_rate",    "video_bit_rate_mode",    "video_width",    "video_height",    "video_frame_rate",    "video_dar",
	"audio_format",    "audio_codec_id",    "audio_duration",    "audio_bit_rate",    "audio_bit_rate_mode",    "audio_channels",    "audio_sampling_rate",    "audio_resolution",	
    "writing_lib",    "custom_data",   /* "raw_data", */   "multi_stream_info",
	);
	
	public static function printMediaInfoHeader (  )
	{
		return self::printFieldNames ( self::$mediaInfoParams );
	}

	public static function printMediaInfo ( $mi_one_or_more )
	{
		if ( ! is_array($mi_one_or_more) ) 
		{
			$arr = array ( $mi_one_or_more );			
		}
		else
			$arr = $mi_one_or_more;
		
		$trs = "";
		foreach ( $arr as $mi )
		{
			$trs .= self::printFields ( $mi , self::$mediaInfoParams , array ( "raw_data" => "height:50px;width:100px;overflow:hidden"));
		}
		
		return $trs;
	}
	
	
	private static $trackEntryParams =  array ( 
    "id",    "track_event_type_id",   "created_at",    "updated_at",   "ps_version",    "context",    "partner_id",    "entry_id",    "host_name",    "uid",
    "track_event_status_id",    "changed_properties",    "param_1_str",    "param_2_str",    "param_3_str",    "ks",
    "description",  "user_ip" ,
	);
	
	public static function printTrackEntryHeader (  )
	{
		return self::printFieldNames ( self::$trackEntryParams );
	}

	public static function printTrackEntryParams ( $te )
	{
		return self::printFields ( $te , self::$trackEntryParams );
	}
		
	
	private static $flavorParams =  array ( 
	   "id",    "version",     "partner_id",     "name",    "tags",    "description",    "ready_behavior",
    "created_at",    "updated_at",    "deleted_at",    "is_default",    "format",    "video_codec",
    "video_bitrate",    "audio_codec",    "audio_bitrate",    "audio_channels",    "audio_sample_rate",
    "audio_resolution",    "width",    "height",    "frame_rate",    "gop_size",    "two_pass",    "conversion_engines",
    "conversion_engines_extra_params",    "custom_data",  /*  "view_order" ,*/ "creation_mode" ,
	);
	
	public static function printFlavorParamsHeader (  )
	{
		return self::printFieldNames ( self::$flavorParams );
	}

	public static function printFlavorParams ( $fp )
	{
		return self::printFields ( $fp , self::$flavorParams );
	}

	
	private static $flavorParamsOutput =  array ( 
   "id",    "flavor_params_id",    "flavor_params_version",    "partner_id",    "entry_id",    "flavor_asset_id",
    "name",    "tags",    "description",    "ready_behavior",    "created_at",    "updated_at",    "deleted_at",
    "is_default",    "format",    "video_codec",    "video_bitrate",    "audio_codec",    "audio_bitrate",    "audio_channels",
    "audio_sample_rate",    "audio_resolution",    "width",    "height",    "frame_rate",    "gop_size",    "two_pass",
    "conversion_engines",    "conversion_engines_extra_params",    "custom_data",    "command_lines_str",   /* "file_ext" ,*/
	);
	
	public static function printFlavorParamsOutputHeader (  )
	{
		return self::printFieldNames ( self::$flavorParamsOutput );
	}

	public static function printFlavorParamsOutputs ( $fpo_one_or_more )
	{
		if ( ! is_array($fpo_one_or_more) ) 
		{
			$arr = array ( $fpo_one_or_more );			
		}
		else
			$arr = $fpo_one_or_more;
		
		$trs = "";
		foreach ( $arr as $mi )
		{
			$trs .= "<tr>";
			$partial_list = self::$flavorParamsOutput ;
			unset ( $partial_list[30] ); // remove the "command_lines_str" - will deal with it specifically
			$trs .= self::printFields ( $mi , $partial_list , null, false );
			if ( $mi->getCommandLinesStr() )
			{
				$content = str_replace ( array ( flavorParamsOutput::KEY_SEPARATOR ) , "\n" ,  $mi->getCommandLinesStr() );
				$trs .= '<td onclick="show(this,event)" ><img width="40px" height="30px" src="/lib/images/magnifying_glass.jpg" ><textarea style="display:none">' . $content .'</textarea></td>';
			}
			else
				$trs .= "<td></td>";
			$trs .= "</tr>";
		}
		
		return $trs;		
	}	
	
	public static function printKshowHeader (  )
	{
		$str = "<tr><td>Id(pid)</td>" .
			"<td>Name</td>" . 
			"<td>Producer Id</td>".
			"<td>Tags</td>".
			"<td width='10%'>Type</td>".
			"<td>Media Type</td>".
			"<td>Thumbnail</td>".
			"<td>Status</td>" .
			"<td>Views</td>".	
			"<td>Votes</td>".
			"<td>Comments</td>".
			"<td>Favorites</td>".
			"<td>Rank</td>".
			"<td>Entries</td>".
			"<td>Duration</td>".
			"<td>View Permissions</td>".
			"<td>Contrib Permissions</td>".
			"<td>Edit Permissions</td>".
			"<td>Created At</td>".
			"<td>Updated At</td>".
		"</tr>";
		
		return $str;
	}	
	
	public static function printKshow ( $kshow )
	{
		if ( $kshow === NULL )	{	return ""; 	}
		
		if ( $kshow instanceof kshow )
			$kshow = new genericObjectWrapper ( $kshow  , true );
			
		$id_partner_id = $kshow->id . "(" . $kshow->partnerId . ")";
			
		$str = "" .
			"<td>" . $id_partner_id . "</td>".
			"<td>" . $kshow->name . "</td>" . 
			"<td>" . $kshow->producerId . "</td>".
			"<td>" . htmlentities( $kshow->tags ). "</td>".
			"<td>" . self::formatEntryType( $kshow->type ). "</td>".
			"<td>" . $kshow->mediaType . "</td>".
			'<td><img width="100" height="80"	src="' . $kshow->ThumbnailPath . '"></td>'.
			"<td>" . $kshow->status . "</td>" .
			"<td>" . $kshow->views . "</td>".	
			"<td>" . $kshow->votes . "</td>".
			"<td>" . $kshow->comments . "</td>".
			"<td>" . $kshow->favorites . "</td>".
			"<td>@@" . $kshow->rank . "</td>".
			"<td>" . $kshow->entries . "</td>".
			"<td>" . $kshow->lengthInMsecs . "</td>".
			"<td>" . $kshow->viewPermissions . "(" . $kshow->viewPassword . ")</td>".
			"<td>" . $kshow->contribPermissions . "(" . $kshow->contribPassword . ")</td>".
			"<td>" . $kshow->editPermissions . "(" . $kshow->editPassword . ")</td>".
			"<td>" . $kshow->createdAt . "</td>".
			"<td>" . $kshow->updatedAt . "</td>";
		
		return $str;
	}	
	
	public static function printConversionHeader ( $entry_id , $add_reconvert_link = false )
	{
		$status_txt = "-1=CONVERSION_STATUS_ERROR\n".
			"1=CONVERSION_STATUS_PRECONVERT\n" . 
			"2=CONVERSION_STATUS_COMPLETED" ;

		$reconvert_link = $add_reconvert_link ? 
				"<br/><a href='javascript:reconvert(\"" . url_for ( "/system" ) . "/reconvert?conversion_id=&entry_id={$entry_id}\")'>reconvert</a>" :
				"";
							
		$str = "<tr>" .
			"<td>Id $reconvert_link</td>" .
			"<td>Entry Id</td>".
			"<td>> File Name</td>".
			"<td>> File Ext</td>".
			"<td>> File Size</td>".
			"<td>Source</td>".
			"<td title=\"$status_txt\">Status</td>".
			"<td>Conversion Params</td>".
			"<td>< File Name</td>".
			"<td>< File Size</td>".
			"<td>< File Name Edit</td>".
			"<td>< File Size Edit</td>".
			"<td>Conversion Time</td>".
			"<td>Total Time</td>".
			"<td>Created</td>".
			"<td>Updated</td>".
		"</tr>";
		return $str;
	}

	public static function printConversion ( $conversion , $entry_id , $link_to_investigate = false , $add_reconvert_link = false )
	{
		$orig_conv = $conversion;
		if ( $conversion instanceof $conversion )
			$conversion = new genericObjectWrapper ( $conversion  , true );
//		$entry_id = $conversion->entryId ;
			
		if ( $orig_conv )
			$orig_created_at = $orig_conv->getCreatedAt ( null );
		else
			$orig_created_at  = null;

		$status_bg = "lime";
		$created_at_bg = "lime";
		
		$title = "";
		if ( $conversion->status == conversion::CONVERSION_STATUS_ERROR )
		{
			$status_bg = "red";
			$created_at_bg = "white";
		}
		elseif ( $conversion->status == conversion::CONVERSION_STATUS_PRECONVERT )
		{
			$delta = time() - $orig_created_at;
			if ( $delta > 1600 ) // 1/2 an hour
			{
				$minutes = (int)($delta / 60 );
				$title = "Over $delta seconds ($minutes minutes) since created!";
				$status_bg = "yellow";
				$created_at_bg = "yellow";
			}
		}
		
		$reconvert_link = $add_reconvert_link ? 
				"<br/><a href='javascript:reconvert(\"" . url_for ( "/system" ) . "/reconvert?conversion_id={$conversion->id}&entry_id={$entry_id}\")'>reconvert</a>" :
				"";
		$str = 	'<tr style="vertical-align: top">' .
				'<td>' . $conversion->id . $reconvert_link . '</td>' . 
				'<td>' . ( $link_to_investigate ? "<a href='" . url_for ( "/system" ) . "/investigate?entry_id=" . $entry_id . "'> $entry_id </a>" : $entry_id ) . '</td>' .
				'<td>' . $conversion->inFileName . '</td>' .
				'<td>' . $conversion->inFileExt . '</td>' .
				'<td>' . $conversion->inFileSize .'</td>'. 
				'<td>' . $conversion->source .'</td>'.
				'<td title="' . $title . '" style="background-color:' . $status_bg . '">' . $conversion->status .'</td>'.
				'<td>' . str_replace ( "|" , "<br>" , $conversion->conversionParams ) .'</td>'.
				'<td>' . $conversion->outFileName .'</td>'.
				'<td ' . ( ! $conversion->outFileOk ? "style='color:red'" : "" ) . ">" . $conversion->outFileSize . '<br/>' . self::fileSizeRatio( $conversion->outFileSize ,  $conversion->inFileSize ). '</td>'.
				'<td>' . $conversion->outFileName2 .'</td>'.
				'<td ' . ( ! $conversion->outFile2Ok ? "style='color:red'" : "" ) . ">". $conversion->outFileSize2 . '<br/>' . self::fileSizeRatio( $conversion->outFileSize2 ,  $conversion->inFileSize ).'</td>'.
				'<td>' . $conversion->conversionTime .'</td>'.
				'<td>' . $conversion->totalProcessTime .'</td>'.
				'<td title="' . $title . '" style="background-color:' . $created_at_bg . '">' . $conversion->createdAt .'</td>'.
				'<td>' . $conversion->updatedAt .'</td>'.
				'</tr>' ; 
		return $str;				
	}
	
	private static function fileSizeRatio ( $f1 , $f2 )
	{
		if ( !$f2 ) return "-";
		return  "(" . floor(100 * $f1 / $f2 ) . "%)"; 	
	}
	
	
	private static $batchJobsParams =  array ( 
	"root_job_id","parent_job_id",
"deleted_at", "priority", "work_group_id","queue_time","finish_time","entry_id",
"partner_id","subp_id","scheduler_id","worker_id","batch_index","last_scheduler_id",
"last_worker_id","last_worker_remote",/*"processor_name",*/"processor_expiration",
/*"processor_location",*/"execution_attempts","lock_version","twin_job_id","bulk_job_id",
"dc","err_type","err_number","on_stress_divert_to"
	);
	public static function printBatchjobHeader ()
	{
/*
 * 	const BATCHJOB_STATUS_PENDING = 0;
	const BATCHJOB_STATUS_QUEUED = 1;
	const BATCHJOB_STATUS_PROCESSING = 2;
	const BATCHJOB_STATUS_PROCESSED = 3;
	const BATCHJOB_STATUS_MOVEFILE = 4;
	const BATCHJOB_STATUS_FINISHED = 5;
	const BATCHJOB_STATUS_FAILED = 6;
	const BATCHJOB_STATUS_ABORTED = 7;
 */		
		$status_txt = "0=BATCHJOB_STATUS_PENDING\n".
			"1=BATCHJOB_STATUS_QUEUED\n".
			"2=BATCHJOB_STATUS_PROCESSING\n".
			"3=BATCHJOB_STATUS_PROCESSED\n".
			"4=BATCHJOB_STATUS_MOVEFILE\n".
			"5=BATCHJOB_STATUS_FINISHED\n".
			"6=BATCHJOB_STATUS_FAILED\n".
			"7=BATCHJOB_STATUS_ABORTED\n".
			"8=BATCHJOB_STATUS_ALMOST_DONE\n".
			"9=BATCHJOB_STATUS_RETRY\n".
			"10=BATCHJOB_STATUS_FATAL\n".
			"11=BATCHJOB_STATUS_DONT_PROCESS\n";
		
		$str = "<tr>".
		"<td>Id</td>".
		"<td>Type</td>".
		"<td>Sub Type</td>".
		"<td>EntryId</td>".
		"<td>Data</td>".
		"<td title=\"$status_txt\">Status</td>".
		"<td>Abort</td>".
		"<td>Progress</td>".
		"<td style='width:120px' >Message</td>".
		"<td style='width:120px' >Description</td>".
		"<td>Updates Count</td>".
		"<td>Created</td>".
		"<td>Updated</td>";
		
		$str .=  self::printFieldNames( self::$batchJobsParams , false );
		
		$str .= 		"<td>Check Again Timeout</td>"; // move to end
		$str .= "</tr>"; 
		return $str;
	}

	public static function printBatchjob ( $bj , $link_to_investigate = false , $tr_style = null )
	{
		
		$orig = $bj;
		
		$orig_created_at = $orig->getCreatedAt ( null );

			$status_bg = "lime";
		$created_at_bg = "lime";
		
			
		if ( $bj instanceof BatchJob  )
			$bj = new genericObjectWrapper ( $bj  , true );
			
//		$data = "datatata";
		$data = substr ( htmlentities( print_r ( $orig->getdata() ,true) ) , 0 , 100 ) . "...";
//		$data = print_r ((  $bj->data ) ,true ) ;
		
		$entry_id = $bj->entryId;

		$title = "";
		if ( $bj->status == BatchJob::BATCHJOB_STATUS_FAILED || $bj->status == BatchJob::BATCHJOB_STATUS_FATAL )
		{
			$status_bg = "red";
			$created_at_bg = "white";
		}
		elseif ( $bj->status == BatchJob::BATCHJOB_STATUS_ABORTED )
		{
			$status_bg = "purple";
			$created_at_bg = "white";
		}
		elseif ( $bj->status != BatchJob::BATCHJOB_STATUS_FINISHED && $bj->status != BatchJob::BATCHJOB_STATUS_ABORTED )
		{
			$delta = time() - $orig_created_at;
			if ( $delta > 1600 ) // 1/2 an hour
			{
				$minutes = (int)($delta / 60 );
				$title = "Over $delta seconds ($minutes minutes) since created!";
				$status_bg = "yellow";
				$created_at_bg = "yellow";
			}
		}
	
		$data_div_id= "batch_job_data_tr_$bj->id";
		
		$resubmit_import_job = $bj->jobType == BatchJobType::IMPORT ?
			("<br/><a href='javascript:restartJob(\"" . url_for ( "/system" ) . "/restartJob?batchjob_id={$bj->id}&entry_id={$entry_id}\")'>restart</a>") : "";
		
		$job_type_desc = self::formatBatchJobType(  $bj->jobType );			
		$str = '<tr style="vertical-align: top; ' . $tr_style . '">'.
		'<td><a href="javascript:void" onclick="toggleDisplay(\''.$data_div_id.'\')">expand data</a><br/> ' . $bj->id . $resubmit_import_job.'</td>'.
		'<td title="' . $job_type_desc . '">'. $bj->jobType .'</td>'.
		'<td>'. $bj->jobSubType .'</td>'.
		'<td>' . ( $link_to_investigate ? "<a href='" . url_for ( "/system" ) . "/investigate?entry_id=" . $entry_id . "'> $entry_id </a>" : $entry_id ) . '</td>' .
		'<td style="font-size:11px; width:150px; ">' .
		'<div class="bj_div" style="font-size:11px; width:150px; max-height:50px; overflow: hidden">' .
			str_replace ( ";" , "; " , $data ) .
		'</div>'.
		'</td>'.
		'<td title="' . $title . '" style="background-color:' . $status_bg . '">' . $bj->status .'</td>'.
		'<td>'. $bj->abort .'</td>'.
		
		'<td>'. $bj->progress .'</td>'.
		'<td>'. $bj->message .'</td>'.
		'<td>'. $bj->description  .'</td>'.
		'<td>'. $bj->updatesCount .'</td>'.
		'<td title="' . $title . '" style="background-color:' . $created_at_bg . '">' . $bj->createdAt .'</td>'.
		'<td>'. $bj->updatedAt .'</td>';
		$str .=  self::printFields( $orig , self::$batchJobsParams , null , false ) . 
		'<td>'. $bj->checkAgainTimeout .'</td>';
		$str .= '</tr>';
		
		// add a new row for the data
		$raw_data = $orig->getdata();
		$formatted_data = "";
		// try unserializing then un-json
		if ( $raw_data )
		{
			$formatted_data = htmlentities( print_r ( $raw_data ,true) );
		}
		else
		{
			$raw_data = $orig->getdata(true);
			// try json
			$formatted_data= var_dump ( $raw_data ,true) ;
		}
		
		$str .= "<tr style='display:none; $tr_style ' id='$data_div_id' ><td style='font-size:11px;' colspan='39'><pre>$formatted_data</pre></td></tr>";
		return $str;
	}
	
	public static function printNotificationHeader ()
	{
		$str = "<tr>".
		"<td>Id</td>".
		"<td>Type</td>".
		"<td>Status</td>".
		"<td>Attempts</td>".
		"<td>Result</td>".
		"<td>Data</td>".
		"<td>Partner Id</td>".
		"<td>Puser Id</td>".
		"<td>Created</td>".
		"<td>Updated</td>".
		"</tr>"; 
		return $str;
	}

	public static function printNotification ( $dbJob , $link_to_investigate = false )
	{
		$orig = $dbJob;
		
		$orig_created_at = $orig->getCreatedAt ( null );

		$status_bg = "lime";
		$created_at_bg = "lime";
			
		$data = preg_replace( "/;/" , ";<br>" , $dbJob->getData()->getData() )  ;
		$data = preg_replace( "/&/" , "<br> &" , $data  )  ;
		
		$entry_id = $dbJob->getData()->getObjectId();

		if ( $dbJob instanceof BatchJob &&  $dbJob->getJobType() == BatchJobType::NOTIFICATION  )
			$job = new genericObjectWrapper ( $dbJob  , true );
		else
			return "";

		$not_type_status = @self::$not_type_statuses[$job->data->status];
		$not_type_text = @self::$not_type_texts[$job->data->type];
		$not_result_text = @self::$not_result_texts[$job->data->notificationResult];
		
		$title = "";
		if ( $job->data->status == BatchJob::BATCHJOB_STATUS_FAILED)
		{
			$status_bg = "red";
			$created_at_bg = "white";
		}
		elseif ( $job->data->status != BatchJob::BATCHJOB_STATUS_FINISHED )
		{
			$delta = time() - $orig_created_at;
			if ( $delta > 1600 ) // 1/2 an hour
			{
				$minutes = (int)($delta / 60 );
				$title = "Over $delta seconds ($minutes minutes) since created!";
				$status_bg = "yellow";
				$created_at_bg = "yellow";
			}
		}
		
		$resubmit_notification = "<br/><a href='javascript:resendNotification(\"" . url_for ( "/system" ) . "/resendNotification?notification_id={$job->id}&entry_id={$entry_id}\")'>resend</a>";
		
		$str = '<tr style="vertical-align: top">'.
		'<td>'. $job->id .$resubmit_notification.'</td>'.
		'<td>'.$not_type_text .'('.$job->data->type.')</td>'.
		'<td title="' . $title . '" style="background-color:' . $status_bg . '">' . $not_type_status .'('.$job->data->status.')</td>'.
		'<td>'. $job->execution_attempts .'</td>'.
		'<td>'.$not_result_text .'('.$job->data->notificationResult.')</td>'.
		'<td style="width:800px; height:50px; overflow: hidden">'. $data .'</td>'.
		'<td>'. $job->partnerId .'</td>'.
		'<td>'. $job->data->userId .'</td>'.
		'<td title="' . $title . '" style="background-color:' . $created_at_bg . '">' . $job->createdAt .'</td>'.
		'<td>'. $job->updatedAt .'</td>'.
		'</tr>';
		
		return $str;
	}

}
?>