<?php
/*
 * Will hold severa helper functions common both to the client-side and the server side
 */
class kConversionHelper
{
	const INDICATOR_SUFFIX = ".indicator";
	const INPROC_SUFFIX = ".inproc";
	
	public static function getExclusiveFile ( $path , $process_id = 1 , $log_number_of_files = false )
	{
		$indicators = glob ( $path . "/*" . self::INDICATOR_SUFFIX  );
		$count = count ( $indicators );
		if ( $count > 0 || $log_number_of_files )
		{
			TRACE ( "[" . $count . "] indicator in directory [" . $path . "]" );
		}
		
		if ( $indicators == null || count ( $indicators ) == 0 ) return null;
		
		foreach ( $indicators as $indicator )
		{
			$new_indicator = $indicator . "-{$process_id}";

			$move_res = @rename ( $indicator, $new_indicator );
			// only one server will actually move the indicator ... 
			if ( $move_res )
			{
				$file = str_replace ( kConversionCommand::INDICATOR_SUFFIX , ""  , $indicator );
				$file_name = basename ( $file );
				// now remove the indicator 
				//unlink( $new_indicator );
				// move to in-proc
				$in_proc = self::inProcFromIndicator ( $indicator );
				@rename ( $new_indicator ,  $in_proc );

				return array ( $file , $file_name , $in_proc );
			}
			else
			{
				TRACE ( "[$indicator] grabbed by other process");
			}
			
			// keep on trying ...
		}
		
		return null;
	}
		
	public static function inProcFromIndicator ( $full_file_path )
	{
		return str_replace ( self::INDICATOR_SUFFIX , self::INPROC_SUFFIX, $full_file_path );
	}

	public static function removeInProc ( $in_proc )
	{
		@unlink( $in_proc );
	}
	
	public static function createFileIndicator ( $full_file_path )
	{
		$path = $full_file_path . self::INDICATOR_SUFFIX ;
		if ( file_exists( $path ))
		{
			$content = file_get_contents( $path ); // sync - OK
			if ( is_numeric( $content ) )
				$content++;
			else
				$content = 1;
			file_put_contents( $path , $content ); // sync - OK
		}
		else
		{
			touch( $path );
			$content = "";
		}	
		return array ( $path , $content );	
	}
	
	public static function isFlv ( $full_file_path )
	{
		return myFlvStaticHandler::isFlv( $full_file_path );
	}
	
	
	public static function getFlvDuration ( $full_file_path )
	{
		return myFileConverter::getFlvDuration( $full_file_path );	
	}
	
	// will return an array  ( $width , $height )
	public static function getVideoDimensions ( $full_file_path )
	{
		return myFileConverter::getVideoDimensions( $full_file_path );	
	}

	// will return an array ( $found_video , $found_audio )
	public static function fileHasVideoAndAudio ( $full_file_path )
	{
		$audio = $video = true;
		$audio_video_status = myFileConverter::videoAudioStatus ( $full_file_path );
		if ( $audio_video_status == myFileConverter::VIDEO_ONLY ) $audio = false;
		elseif ( $audio_video_status == myFileConverter::AUDIO_ONLY ) $video = false;

		return array ( $video , $audio );
	}
	
	// return the full_file_path as if it was FLV
	public static function flvFileName ( $full_file_path )
	{
		$full_path = kFile::getFileNameNoExtension( $full_file_path , true ) . ".flv";
		return $full_path;
	}
	
	/*
	 * Will fill missing params from the conv_params according to data from the source_file
	 * 1. will check if the file is audio only
	 * 2. will check if the file is video only
	 * 3. will calculate the heigth from the width and the size of the source video depending on the aspect_radio 
	 */
	public static function fillConversionParams ( $source_file , kConversionParams $conv_params )
	{
		// check to see if audio or video should be set
		if ( $conv_params->audio === null || $conv_params->video === null )
		{
			list ( $video , $audio ) = self::fileHasVideoAndAudio ( $source_file );
			if ( $conv_params->audio === null ) $conv_params->audio = $audio;
			if ( $conv_params->video === null ) $conv_params->video = $video;
		}
		
		// if should use "aspect_ratio" but have no width - act as if "original_size" 
		if ( $conv_params->aspect_ratio == kConversionParams::CONV_PARAMS_ASPECT_RATIO_KEEP_ORIG_DIMENSIONS || 
			 ( $conv_params->aspect_ratio == kConversionParams::CONV_PARAMS_ASPECT_RATIO_KEEP_ORIG_RATIO &&  $conv_params->width == 0 ) 
			)
		{
			$conv_params->width = -1;
			$conv_params->height = -1;
			//list ( $conv_params->width , $conv_params->height ) = self::getVideoDimensions ( $source_file );
			// in this case we don't even need to pass the size 
			return;
		}

		if ( $conv_params->aspect_ratio == kConversionParams::CONV_PARAMS_ASPECT_RATIO_IGNORE )
		{
			// use the width & heigth from the params - IGNORE all the external requests 
		}
		elseif ( $conv_params->aspect_ratio == "" && $conv_params->height > 0 && $conv_params->width > 0 )
		{
			// leave untouched 
		}
		// if the aspect_ratio implies to keep the height - see if the height is 0 or not... 
		elseif ( $conv_params->height == 0 || 
			( 	$conv_params->aspect_ratio != kConversionParams::CONV_PARAMS_ASPECT_RATIO_KEEP_HEIGHT &&  
				$conv_params->aspect_ratio != kConversionParams::CONV_PARAMS_ASPECT_RATIO_KEEP_ORIG_DIMENSIONS )
			) // can be empty , null or 0
		{
			if ( $conv_params->aspect_ratio == kConversionParams::CONV_PARAMS_ASPECT_RATIO_KEEP_ORIG_RATIO )
			{
				list ( $video_width , $video_height ) = self::getVideoDimensions ( $source_file );
				
				if ( $video_height != 0 ) 
					$conv_params->height = self::calcHeight ( $conv_params->width , $video_width / $video_height );
				else
					$conv_params->height = 0;
			}
			elseif ( $conv_params->aspect_ratio == kConversionParams::CONV_PARAMS_ASPECT_RATIO_16_9 )
			{
				$conv_params->height = self::calcHeight ( $conv_params->width , ( 16/9 ) );
			}
		
			else //( $conv_params->aspect_ratio == kConversionParams::CONV_PARAMS_ASPECT_RATIO_4_3 )
			{
				// default is CONV_PARAMS_ASPECT_RATIO_4_3
				$conv_params->height = self::calcHeight ( $conv_params->width , ( 4/3 ) );
			}			
		}
		
		// make sure the width and heigth are even numbers
		if ( $conv_params->width % 2 == 1 ) $conv_params->width = $conv_params->width+1;
		if ( $conv_params->height % 2 == 1 ) $conv_params->height = $conv_params->height+1;
		
		// if by the end of all the calculations - still 0 or smaller - set to hard-coded defaults...
		if ( $conv_params->width <= 0 ) $conv_params->width = 400;
		if ( $conv_params->height <= 0 ) $conv_params->height = 300;
	}
	
	
	// TODO - extract all the data that might be required for fixing the prams
	protected static function getSourceInfo ( $source_file )
	{
			
	}
	
	protected static function calcHeight ( $width , $aspect_ratio )
	{
		if ( $aspect_ratio == 0 ) return $width;
		return  (int)( $width / $aspect_ratio );
	}
	
}
?>