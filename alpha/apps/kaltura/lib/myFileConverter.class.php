<?php
require_once ( "lib/model/conversion.php");

class myFileConverter
{
	const CROP_TYPE_ORIGINAL_ASPECT_RATIO = 1;
	const CROP_TYPE_WITHIN_BG_COLOR = 2;
	const CROP_TYPE_EXACT_SIZE = 3;
	const CROP_TYPE_UPPER = 4;
	
	// TODO - change to read from configuration !!
	//const FFMPEG = "\"C:\\web\\ffmpeg\\ffmpeg-0.4.9\\Riva FLV Encoder 2.0\\ffmpeg.exe\" ";
	const FFMPEG = "ffmpeg";
	const MENCODER = "mencoder";
	const IMAGE_MAGICK = "convert"; 
	
	const ENCODE_FFMPEG = 1;
	const ENCODE_MENCODER = 2;
	const ENCODE_BOTH = 3;

	const DEFAULT_THUMBNAIL_WIDTH = 120;
	const DEFAULT_THUMBNAIL_HEIGHT = 90;

	const AUDIO_ONLY = 1;
	const VIDEO_ONLY = 2;
	const VIDEO_AND_AUDIO = 3;

	const NO_COVERSION = "NO_CONVERSION" ;
	
	private static $AUDIO_EXT = array ( "wav" , "mp3" , "wma" , "au" , "ra" , "aac", "amr" );

	static public function getRed5Duration ( $source_file )
	{
		$duration = null;

		try
		{
			$meta_file_name =  $source_file . '.meta';
			if ( ! file_exists( $meta_file_name ) )
			{
				return -10; // indicates that the file doesn't exist
			}
			$xml_doc = new DOMDocument();

			$xml_doc->loadXML( kFile::getFileContent( $source_file . '.meta') );  // sync - OK

			$list = $xml_doc->getElementsByTagName( "FrameMetadata" );
			if ( $list != null && $list->length > 0 )
			{
				$duration = $list->item(0)->getAttribute ( "duration" );
			}
		}
		catch ( Exception $ex)
		{
			// indicates some unknown error
			$duration = -1;
		}

		return $duration;
	}

	static public function getFlvDuration ( $source_file )
	{
		$source_file = kFile::fixPath ( $source_file );

		$conversion_info = new conversionInfo();
		$conversion_info->fillFromMetadata( $source_file );

		return $conversion_info->duration;
	}

	// "F:\web\ffmpeg\ffmpeg-0.4.9\Riva FLV Encoder 2.0\ffmpeg.exe" -i "F:\web\ffmpeg\robot.avi" -b 360 -r 25 -s 400x300 -hq -deinterlace  -ab 56 -ar 22050 -ac 1  "F:\web\ffmpeg\robot.flv" 2>encode.txt
	// -hq: activate high quality settings
	// -deinterlace:  deinterlace pictures
	/**
	 * The return value is an array with 2 paramters:
	 * return_value - the result of the execution
	 * output - an array of strings whihc were printed by the exec function
	 */
	//static public function convert ( $source_file , $target_file , $target_type = "flv" , $text_output_file = NULL , $width = 426 , $height = 350 )
	static public function convert ( $source_file , $target_file , $target_type = "flv" , $text_output_file = NULL ,
	$width = 400 , $height = 300 ,
	$encode_mode = self::ENCODE_BOTH ,
	$quote = false )
	{
		$source_file = kFile::fixPath ( $source_file );
		$target_file = kFile::fixPath ( $target_file );
		$text_output_file = kFile::fixPath( $text_output_file );

		if ( $text_output_file == NULL )
		{
			$text_output_file = self::createLogFileName ($source_file );
		}
			
		$video_audio  = self::videoAudioStatus ( $source_file );

		// adding the output and return_value makes the call synchronous.
		$output = array ();
		$conversion_string = "";
		$conversion_time  = 0;

		$conversion_info = new conversionInfo();
		$extra_data = new conversion(); // the DB struct for extra info
		$conversion_info->extra_data = $extra_data;

		$conversion_info->source_file_name = $source_file;
		$conversion_info->target_file_name = $target_file;

		$conversion_string_aggr = "" ;
		$start_time = microtime(true);

		$return_value = -1;

		$fixed_source_file = $quote ? "'$source_file'" :  $source_file;

//TRACE ( "Before calling ffmpegConvert:\n[$fixed_source_file]->[$target_file]")		;
		if ( $encode_mode & self::ENCODE_FFMPEG )
		{
			$return_value = self::ffmpegConvert( $fixed_source_file , $target_file , $text_output_file , $width , $height ,$video_audio , $conversion_info , $output  );
			$conversion_string_aggr .= $conversion_info->extra_data->getConversionParams();
		}

		// 0 is success
		if ( $return_value != 0 && ( $encode_mode & self::ENCODE_MENCODER ))
		{
			$return_value = self::mencoderConvert( $fixed_source_file , $target_file , $text_output_file , $width , $height ,$video_audio , $conversion_info  , $output , true  );

			$conversion_string_aggr .= " || mencoder: " . $conversion_info->extra_data->getConversionParams();
		}


		$end_time = microtime(true);

		$conversion_time = (int)( ( $end_time-$start_time ) * 1000 ); // thre time is in seconds with floating point - *1000 to get miliiseconds

		$conversion_info->fillFromMetadata( $target_file );

		$extra_data->setConversionParams( $conversion_string_aggr );
		$extra_data->setConversionTime( $conversion_time );
		if ( file_exists( $target_file ))
		{
			$extra_data->setOutFileName ( pathinfo ( $target_file, PATHINFO_BASENAME )  );
			$extra_data->setOutFileSize ( filesize( $target_file ) );
		}

		$target_file_edit = myContentStorage::getFileNameEdit( $target_file );
		if ( file_exists(  $target_file_edit) )
		{
			$extra_data->setOutFileName2 ( pathinfo ( $target_file_edit, PATHINFO_BASENAME )  );
			$extra_data->setOutFileSize2 ( filesize( $target_file_edit ) );
		}

		// try to extract the rest of the data from the output file

		$konverted = false;

		// encapsulte
		return array ( "return_value" => $return_value , "output" => $output , "conversion_info" => $conversion_info , "konverted" => $konverted );
	}

	// -b 500kb -r 25 -g 5 -s 400x300 -ar 22050 -ac 2 -y 
	public static function formatConversionString ( $conversion_str , 
		$real_video_width = 400 , $real_video_height = 300 , $gop_size = -1 , $bitrate = -1 , $qscale = -1 )
	{
		// set good defaults to prevent devision by zero
		if ( $real_video_width <= 0 ) $real_video_width = 400;
		if ( $real_video_height <= 0 ) $real_video_height = 300;

		$calculated_width = 400; // if there was a problem - set the width to be the default 400;
		$calculated_height = 300; // if there was a problem - set the height to be the default 300;

		if (preg_match('/-s (.*)x(.*?) /', $conversion_str, $matches ) )
		{
			$conversion_str_width = $matches[1];
			$conversion_str_height = $matches[2];
			if ( ! is_numeric ( $conversion_str_width ) )
			{
				// need to replace the width
				if ( ! is_numeric ( $conversion_str_height ) )
				{
					// need to replace width & height - take both from the input 
					$calculated_width = $real_video_width;
					$calculated_height = $real_video_height;
				}
				else
				{
					// need to replace width but have height
					$calculated_height = $conversion_str_height;
					$calculated_width = ( $real_video_width * $calculated_height ) / $real_video_height;
				}
			}
			else
			{
				if ( ! is_numeric ( $conversion_str_height ) )
				{
					// need to replace height but have width
					$calculated_width = $conversion_str_width;
//echo "[$calculated_height = ( $real_video_width * $calculated_width ) / $real_video_height]\n";					
					$calculated_height = ( $real_video_height * $calculated_width ) / $real_video_width;
				}
				else
				{
					// the calculated are the exact values from the conversion string
					$calculated_width = $conversion_str_width;
					$calculated_height = $conversion_str_height;
				}
			}
		}

		// round the numbers
		$calculated_width = round ( $calculated_width );
		$calculated_height = round ( $calculated_height );
		
		// make sure are even numbers
		if ( $calculated_width % 2 ==1 ) $calculated_width+=1;
		if ( $calculated_height % 2 ==1 ) $calculated_height+=1;
		
		$conversion_str = str_replace( array ( "{width}" , "{height}" ) , array ( $calculated_width , $calculated_height ) , $conversion_str );
		return $conversion_str;
	}
/*
 * 
 * 
HD: 
ffmpeg -i /path/to/your/video -y -vcodec libx264 -acodec libfaac -title 'your title' -f mp4 -mbd rd -flags
4mv+trell+aic+qprd+mv0 -cmp 2 -subcmp 2 -flags2 dct8x8+skiprd -level 41 -b your_video_bitrate -bf 3 -ac
your_channels -ab your_audio_bitrate -threads your_threads -pass 1|2 /path/to/your/putput.mp4

$edit_only=true will be used when ffmpeg is used to create the second flavor after mencoder succeeded the first one
 * 
 */
	public static function ffmpegConvert ( $source_file , $target_file , $text_output_file ,
	$width , $height , $video_audio , &$conversion_info , &$output ,
	$append_to_log = false , $edit_only = false )
	{
		// once was 260kb & 360kb
		$bitrate = "400kb" ; // kbit / second
		$bitrate_2 = "500kb" ; // kbit / second
		$gop_size = 25;  // the size of the frame group - a keyframe will be forced every <gop_size>
		$gop_size_2 = 5;  // the size of the frame group - a keyframe will be forced every <gop_size>

		$frame_rate = 25 ; // frames / second
		$audio_bitrate = "56kb";  //  kbit/s
		$audio_sampling_rate = 22050; // in Hz
		$audio_channels = 2; // sterio
		// TODO - change gop size to 2 !!!!!
		$qscale = 5; // quality scale - 1 best | 31 worst

		if ( $video_audio == self::AUDIO_ONLY )				$video_audio_str = " -vn "; 	// video none
		elseif  ( $video_audio == self::VIDEO_ONLY )		$video_audio_str = " -an ";		// audio node
		else $video_audio_str = " ";

		$conversion_string_2 = "";
		$target_file_2 = "";
					
		// IMPORTANT: qscale omitted ! it causes files to be very very big!
		// conversion string for play-time
		if ( !$edit_only )
		{
			$conversion_string = self::conversionStringForFile ( $source_file );
			// if the file is audio only - still use our the hard-coded conversion string 
			if ( ! $conversion_string || $video_audio == self::AUDIO_ONLY )
			{
				$conversion_string = " -b " . $bitrate .
				//			" -qscale " . $qscale .
				" -r " . $frame_rate .
				" -g " . $gop_size .
				" -s " . $width ."x" . $height .
				//			" -ab " . $audio_bitrate .
				" -ar " .  $audio_sampling_rate .
				" -ac " . $audio_channels .
				$video_audio_str .
				//			" -f " . $target_type .
				" -y ";
			}
		}

		// in case of audio-only - there is no reason to create another flavor of the file
		if ( $video_audio != self::AUDIO_ONLY  )
		{
			// conversion string for edit-time
			$conversion_string_2 = " -b " . $bitrate_2 .
			//			" -qscale " . $qscale .
			" -r " . $frame_rate .
			" -g " . $gop_size_2 .
			" -s " . $width ."x" . $height .
			//			" -ab " . $audio_bitrate .
			" -ar " .  $audio_sampling_rate .
			" -ac " . $audio_channels .
			$video_audio_str .
			//			" -f " . $target_type .
			" -y ";

			$target_file_2 = myContentStorage::getFileNameEdit ( $target_file );
		}
		else
		{
			$conversion_string_2 = "";
			$target_file_2 = "";
		}

		// if edit_only - set the parameters to empty
		if ( $edit_only )
		{
			$conversion_string = "";
			$target_file = "";
		}

		// I have commented out the audio parameters so we don't decrease the quality - it stays as-is
		$exec_cmd = kConversionEngineFfmpeg::getCmd() . " -i " . "\"$source_file\"" .
		$conversion_string .
		" \"$target_file\"  $conversion_string_2  \"$target_file_2\"  2" . ">>" . "\"$text_output_file\"";

//TRACE ( "This is what will be executed:\n$exec_cmd" );
		
		self::addToLogFile ( $text_output_file , $exec_cmd ) ;
		self::addToLogFile ( $text_output_file , $conversion_string . "|" .  $conversion_string_2 ) ;
		
		//echo ( $target_file . "\n" );
		//		echo ( "\n\n" . $exec_cmd . "\n");

		// adding the output and return_value makes the call synchronous.
		$return_value = "";

		exec ( $exec_cmd , $output , $return_value );

		// here we set some data that is important
		$conversion_info->video_width = $width;
		$conversion_info->video_height = $height;
		$conversion_info->video_bitrate = $bitrate;
		$conversion_info->video_framerate = $frame_rate;
		$conversion_info->video_gop = $gop_size;
		if ( !$conversion_info->extra_data )
		{
			$conversion_info->extra_data = new conversion(); // the DB struct for extra info
		}
		$conversion_info->extra_data->setConversionParams ( " || ffmpeg: " . $conversion_string . "|" . $conversion_string_2 );

		$conversion_info->target_file_name_2 = $target_file_2;
		// TODO - the return_value should reflect if the conversion worked or not
		self::addToLogFile ( $text_output_file , "ffmpegConvert return_value:\n" . print_r ( $return_value , true ) ) ;
		
		return $return_value;
	}

	// TODO - find how to create the second converted file
	public static function mencoderConvert ( $source_file , $target_file , $text_output_file , $width , $height , $video_audio , &$conversion_info , &$output , $append_to_log = false )
	{
		$bitrate = "360" ; // kbit / second
		$frame_rate = 25 ; // frames / second
		$audio_bitrate = "128";  //  kbit/s
		$audio_sampling_rate = 22050; // in Hz
		$audio_channels = 2; // sterio
		// TODO - change gop size to 2 !!!!!
		$gop_size = 4;  // the size of the frame group - a keyframe will be forced every <gop_size>
		$qscale = 5; // quality scale - 1 best | 31 worst
			
		$conversion_string = "" .
		" -of lavf " .
		" -ofps $frame_rate " .
		" -oac mp3lame -lameopts abr:br=$audio_bitrate -srate $audio_sampling_rate " ;
		if ( $video_audio != self::AUDIO_ONLY )
		$conversion_string .=
		" -ovc lavc " .
		" -lavcopts vcodec=flv:vbitrate=$bitrate:mbd=2:mv0:trell:v4mv:cbp:last_pred=3:keyint=$gop_size " . // :vqscale=$qscale " .
		" -vf scale=$width:$height " ;
		else
		$conversion_string .= " -ovc frameno ";

//		$conversion_string .= " -lavfopts i_certify_that_my_video_stream_does_not_use_b_frames " ;


		$exec_cmd = kConversionEngineMencoder::getCmd() . " " . "\"$source_file\"" .
		$conversion_string .
		" -o " . "\"$target_file\""  . " 2" . ( $append_to_log ? ">>" : ">" ) . "\"$text_output_file\"";

		self::addToLogFile ( $text_output_file , $exec_cmd ) ;
		self::addToLogFile ( $text_output_file , $conversion_string ) ;
				
		//echo ( $target_file . "\n" );
		//		echo ( "\n\n" . $exec_cmd . "\n");

		// adding the output and return_value makes the call synchronous.
		$return_value = "";

		// benchmark the conversion in microseconds - a float (in seconds) is returned
		exec ( $exec_cmd , $output , $return_value );

		// now, if all's well - use FFMGET to convert the output to the edit mode
		// use the target file as the input - use the edit_only=true
		$ffmpeg_conversion_info = new conversionInfo();
		$ffmpeg_output = array ();
		self::ffmpegConvert ( $target_file , $target_file , $text_output_file ,		$width , $height , $video_audio , $ffmpeg_conversion_info , $ffmpeg_output ,		true , true );
		$conversion_string .= $ffmpeg_conversion_info->extra_data->getConversionParams();
			
		// here we set some data that is important
		$conversion_info->video_width = $width;
		$conversion_info->video_height = $height;
		$conversion_info->video_bitrate = $bitrate;
		$conversion_info->video_framerate = $frame_rate;
		$conversion_info->video_gop = $gop_size;
		$conversion_info->extra_data->setConversionParams ( $conversion_string );

		self::addToLogFile ( $text_output_file , "mencoderConvert return_value:\n" . print_r ( $return_value , true ) ) ;
		
		// TODO - the return_value should reflect if the conversion worked or not
		return $return_value;
	}

	public static function videoAudioStatus ( $source_file )
	{
		$res = self::VIDEO_AND_AUDIO;
		if( self::isAudioOnlyfile ( $source_file ))		$res = self::AUDIO_ONLY;
		elseif ( ! self::hasAudio( $source_file )) 		$res = self::VIDEO_ONLY;

		return $res;
	}

	
	static public  function isAudioOnlyfile ( $source_file )
	{
		$ext = pathinfo( $source_file , PATHINFO_EXTENSION );
		return ( in_array( $ext , self::$AUDIO_EXT ) );
	}

	public static function hasAudio ( $source_file )
	{
		$ext = pathinfo( $source_file , PATHINFO_EXTENSION );
		if ( $ext == "flv" || myFlvStaticHandler::isFlv( $source_file ))
		{
			$res = myFlvStaticHandler::fileHasAudio( $source_file );
			if ( $res === null ) return true; // if we don't know for sure - we'll assume the file has audio 
			return $res;
		}
		return true;
	}

	static public function freeConvert ( $source_file , $target_file , $params , $text_output_file , $override_output = true )
	{
		$start_time = microtime(true);
		$source_file = kFile::fixPath ( $source_file );
		$target_file = kFile::fixPath ( $target_file );

		if ( $text_output_file == NULL )
		{
			$text_output_file = $target_file . ".log.txt" ;
		}

		$text_output_file = kFile::fixPath( $text_output_file );

		$exec_cmd = kConversionEngineFfmpeg::getCmd() . ( $override_output ? " -y " : "" ) . " -benchmark -i " . "\"$source_file\"" . " " .
		$params .
		" " . "\"$target_file\"" . " 2>" . $text_output_file;

		//echo ( $target_file . "\n" );
		//		echo ( "\n\n" . $exec_cmd . "\n");

		// adding the output and return_value makes the call synchronous.
		$output = array ();
		$return_value = "";
		exec ( $exec_cmd , $output , $return_value );
		$end_time =  microtime(true);

		echo ( "Took [" . ( $end_time - $start_time ) . "] seconds." );

		// encapsulte
		return array ( "return_value" => $return_value , "output" => $output , "log_file" => $text_output_file );
	}

	static public function autoCaptureFrame ( $source_file , $thumbTempPrefix , $position = null, $width = 0, $height = 0 , $plain_log_file_name = false )
	{
		self::captureFrame($source_file, $thumbTempPrefix.'%d.jpg', 1, "image2", $width, $height, $position, $plain_log_file_name );

		// in case the file wasnt created, the movie clip might be too short
		// try taking a snapshot at the first frame
		if (!file_exists($thumbTempPrefix.'1.jpg'))
		{
			self::captureFrame($source_file, $thumbTempPrefix.'%d.jpg', 1, "image2", $width, $height, 0 , $plain_log_file_name );
		}
	}
	// "F:\web\ffmpeg\ffmpeg-0.4.9\Riva FLV Encoder 2.0\ffmpeg.exe" -an -y  -i "F:\web\ffmpeg\robot.avi" -t 0.001 -s 640x480 -deinterlace   -hq -f image2 "F:\web\ffmpeg\robot%%d.jpg" 2>encode.txt
	// -an: disable audio
	// -y: overwrite output file
	// -t: duration
	// -r: fps (frames per second)
	// -f: format
	// -dframes: number     set the number of data frames to record
	// -ss:  set the start time offset
	static public function captureFrame ( $source_file , $target_file , $frame_count = 1, $target_type = "image2" ,
	$width = self::DEFAULT_THUMBNAIL_WIDTH , $height = self::DEFAULT_THUMBNAIL_HEIGHT , $position = null , $plain_log_file_name = false )
	{
		if ($width == 0)
		$width = self::DEFAULT_THUMBNAIL_WIDTH;
			
		if ($height == 0)
		$height = self::DEFAULT_THUMBNAIL_HEIGHT;
			
		$temp_flv = null;
		
		if ($frame_count == 1 && $position > 10 && pathinfo($source_file, PATHINFO_EXTENSION) == "flv") 
		{
			$timestamp = $position * 1000;
			$temp_flv = myContentStorage::getFSUploadsPath()."capture_".microtime(true).rand().".flv";
			$first_timestamp = myFlvStaticHandler::clipToNewFile($source_file, $temp_flv, $timestamp, $timestamp + 1);
			$source_file = $temp_flv;
			$position = floor(($timestamp -  $first_timestamp) / 1000);
			if ($position < 0)
				$position = 0;
		}
		
		//$duration = 0.01;

		//$text_output_file = myContentStorage::getFSUploadsPath().hash ( 'md5' , $target_file ) . ".txt";
		$text_output_file = self::createLogFileName ($source_file , $plain_log_file_name );
		// hq - activate high quality settings
		// deinterlace - deinterlace pictures
		
		// TODO cut long movie and take the image from the short video 
		$position_str = $position ? " -ss $position " : "";
		$dimensions = ($width == -1 || $height == -1) ? "" : ("-s ". $width ."x" . $height);
		$exec_cmd = kConversionEngineFfmpeg::getCmd() . " -i " . "\"$source_file\"" . " -an -y -r 1 " . $dimensions .
		" " . $position_str . " -vframes $frame_count -f \"" . $target_type . "\" " . "\"$target_file\"" . " 2>&1";

		KalturaLog::log("ffmpeg cmd [$exec_cmd]");
		$output = array ();
		$return_value = "";

		set_time_limit(120);
		exec ( $exec_cmd , $output , $return_value );

		$conversion_info = new conversionInfo();
		$conversion_info->fillFromMetadata( $source_file );
		$conversion_info->video_width = $width;
		$conversion_info->video_height = $height;
		// try to extract the rest of the data from the output file
		
		if ($temp_flv)
			unlink($temp_flv);

		// encapsulte
		return array ( "return_value" => $return_value , "output" => $output , "conversion_info" => $conversion_info );


	}

	/**
	 * this function reads a file with a similar name as the source_file to be converted but with a suffix of '.conversionString'.
	 * It adds the -y flag if does not already exist
	 */
	public static function conversionStringForFile ( $file_name )
	{
		$conversion_string_file_name = $file_name . ".conversionString";
		if ( file_exists( $conversion_string_file_name ))		$conversion_string = @file_get_contents( $conversion_string_file_name );
		else $conversion_string ="";
		
		if ( empty ( $conversion_string ) ) return null;
		if ( strpos ( $conversion_string , "-y" ) === FALSE )		$conversion_string .= " -y ";
		$conversion_string = " " . $conversion_string . " "; // pad left and write with spaces in case the content of the file does not include them
		//		TRACE ( "Found conversionString for file [$file_name]\n$conversion_string" );
		return $conversion_string;
	}

	public static function createConversionStringForFile ( $file_name , $conversion_string )
	{
		$conversion_string_file_name = $file_name . ".conversionString";
		$result = @file_put_contents( $conversion_string_file_name , $conversion_string ); // sync - OK
		return $result;
	}

	public static function removeConversionStringForFile ( $file_name )
	{
		$conversion_string_file_name = $file_name . ".conversionString";
		@kFile::deleteFile( $conversion_string_file_name ); 
	}

	private static function setDurationForFailedFlv( $conversion_info , $source_file , $text_output_file )
	{
		$conversion_info->fillFromMetadata( $source_file );
	}

	// Use ffmpeg to extract the video dimensions
	public static function getVideoDimensions (  $source_file )
	{
		$source_file = kFile::fixPath( $source_file ) ;
	
		ob_start();
		$cmd_line = kConversionEngineFfmpeg::getCmd() . " -i \"". $source_file . "\" 2>&1";
		passthru( $cmd_line );
		echo $cmd_line;
		$size = ob_get_contents();
		ob_end_clean();
		
		$width = "";
		$height = "";
		//extract the video size line (used to suggest 25x25 sise for entry 25x25xqajo)
		// used to search for , after the {width}x{height} however both of the following lines are valid:
		// Stream #0.0: Video: h264, yuv420p, 320x240 [PAR 1:1 DAR 4:3], 202 kb/s, 29.92 tbr, 1k tbn, 2k tbc
		// Stream #0.1(und): Video: h264, yuv420p, 480x270, 59.92 tbr, 29.94 tbn, 59.89 tbc
		if (preg_match('/Video:.*? (\d{2,4})x(\d{2,4})/', $size, $matches))
		{
			$width = $matches[1];
			$height = $matches[2];
		}
		
		
		$res = array ( $width , $height );
		return $res; 
	}
	
	static public function getImageInfo ($source_file)
	{
		list ($sourcewidth, $sourceheight, $type, $attr, $srcIm) = self::createImageByFile($source_file);

		$valid = 0;
		if ( $sourcewidth > 0 && $sourceheight > 0 )
		{
			$valid = 1;
		}
		if ( $srcIm )		imagedestroy($srcIm);
		return array ( $valid , $sourcewidth, $sourceheight  , $type , $attr);
	}

	static public function createImageByFile($source_file)
	{
		global $global_kaltura_memory_limit;
		if ( ! isset ( $global_kaltura_memory_limit ) )
		{
			ini_set("memory_limit","256M");
		}
		
		if ( ! file_exists ($source_file))
		{
			KalturaLog::log( "changeImageSize:: file not found [$source_file]" ) ;
			return null;	
		}
		
		if($source_file == '/web') return;
		
		list($sourcewidth, $sourceheight, $type, $attr) = getimagesize($source_file);

		$srcIm = NULL;
		switch ( $type )
		{
			case IMAGETYPE_JPEG: $srcIm = imagecreatefromjpeg( $source_file ); break;
			case IMAGETYPE_PNG: $srcIm = imagecreatefrompng( $source_file ); break;
			case IMAGETYPE_GIF: $srcIm = imagecreatefromgif( $source_file ); break;
			case IMAGETYPE_BMP: $srcIm = self::imagecreatefrombmp( $source_file ); break;
			case IMAGETYPE_WBMP: $srcIm = imagecreatefromwbmp( $source_file ); break;
			default: $srcIm = NULL;
		}
		if( !$srcIm )
		{
			$output = array();
			$jpeg_file = myContentStorage::getFSUploadsPath(true).pathinfo($source_file, PATHINFO_FILENAME).".jpg";
			exec( kConf::get ( "bin_path_imagemagick") . " \"$source_file\" \"$jpeg_file\"", $output);
			if (file_exists($jpeg_file))
			{
				list($sourcewidth, $sourceheight, $type, $attr) = getimagesize($jpeg_file);
				$srcIm = imagecreatefromjpeg( $jpeg_file );
			}
			//fixme echo ('myFileConverter::createImageByFile - file extension not supported: '.$source_file.' type:'.$type );
		}

		return array($sourcewidth, $sourceheight, $type, $attr, $srcIm);
	}

	static public function changeImageSize( $source_file , $target_file , $target_type = "image2" , $width = 0, $height = 0 , $crop=false )
	{
		list($sourcewidth, $sourceheight, $type, $attr, $srcIm) = self::createImageByFile($source_file);

		if ($width == 0 || $height == 0)
		{
			$width = $sourcewidth;
			$height = $sourceheight;
		}

		if ( $crop )
		{
			$sourceheight = ($sourcewidth / $width) * $height; // crop height
		}
			
		try
		{
			$im = imagecreatetruecolor( $width , $height );

			imagecopyresampled( $im, $srcIm, 0, 0, 0 , 0 , $width  , $height, $sourcewidth , $sourceheight );
			imagedestroy($srcIm);
			imagejpeg($im, $target_file);
			imagedestroy($im);
		}
		catch ( Exception $ex )
		{
			KalturaLog::log ( "changeImageSize:: Cannot change image size: $source_file [$sourcewidth x $sourceheight]" , 'warning' );
		}
	}

	// create a thumbnail from an image
	// source_file:  source file
	// target_file:  target file
	// target_type:  target file type
	// width : target image width
	// height : target image height
	static public function createImageThumbnail ( $source_file , $target_file , $target_type = "image2" , $width = self::DEFAULT_THUMBNAIL_WIDTH , $height = self::DEFAULT_THUMBNAIL_HEIGHT )
	{
		if ( ! file_exists( $source_file ) )
		{
			KalturaLog::log ( __CLASS__ . " File not found [$source_file]" );
			return;
		}

		if ( is_dir( $source_file ))
		{
			KalturaLog::log ( __CLASS__ . " Cannot create image from directory [$source_file]" );
			return;
		}
		//$text_output_file = self::createLogFileName ($source_file );

		list($sourcewidth, $sourceheight, $type, $attr, $srcIm) = self::createImageByFile($source_file);
		if (!$srcIm || !$sourcewidth || !$sourceheight)
		{
			KalturaLog::log ( __CLASS__ . " bad image / dimensions [$source_file]" );
			return;
		}

		$im = imagecreatetruecolor( $width , $height );

		// we'll play with the ratios, so that images retain proportions
		$ratio1=$sourcewidth/$width;
		$ratio2=$sourceheight/$height;
		if($ratio1>$ratio2) $height=$sourceheight/$ratio1;
		else $width=$sourcewidth/$ratio2;

		//$colorBackgr = imagecolorallocate($im, 0, 0, 0 );
		//imageInterlace($im, 1);
		//imageColorTransparent($im, $colorBackgr);

		$srcIm_x = imagesx($srcIm);
		$srcIm_y = imagesy($srcIm);

		imagecopyresampled( $im, $srcIm, max( ( self::DEFAULT_THUMBNAIL_WIDTH - $width ) / 2 , 0), max( ( self::DEFAULT_THUMBNAIL_HEIGHT - $height ) / 2 ,  0), 0 , 0 , $width  , $height, $sourcewidth , $sourceheight );
		imagedestroy($srcIm);
		imagejpeg($im, $target_file);
		imagedestroy($im);
	}

	// create a square thumbnail from an image
	// source_file:  source file
	// target_file:  target file
	// target_type:  target file type
	// width : target image width
	// height : target image height
	static public function createSquareImageThumbnail ( $source_file , $target_file , $target_type, $cropSize, $sourcesize, $sourceleft, $sourcetop  )
	{

		//echo 'sourcefile:'.$source_file .' , target_file:' . $target_file .' , target_type:' . $target_type.' , cropsize:' . $cropSize.' , sourcesize:' . $sourcesize.' , sourceleft:' . $sourceleft.' , sourcetop:' . $sourcetop ;
			
		list($sourcewidth, $sourceheight, $type, $attr, $srcIm) = self::createImageByFile($source_file);

		$im = ImageCreateTrueColor($cropSize,$cropSize);

		imagecopyresampled(
		$im,
		$srcIm,
		0,
		0,
		$sourceleft,
		$sourcetop,
		$cropSize,
		$cropSize,
		$sourcesize,
		$sourcesize
		);

		imagedestroy($srcIm);
		imagejpeg($im, $target_file);
		imagedestroy($im);
	}



	static public function saveImageByType($im, $type, $target_file, $quality)
	{
		if ($type == IMAGETYPE_GIF)
			imagegif($im, $target_file);
		else if ($type == IMAGETYPE_PNG)
			imagepng($im, $target_file, 9, PNG_ALL_FILTERS);
		else
			imagejpeg($im, $target_file, $quality ? $quality : 75);
	}

	static public function imageExtByType($type)
	{
		if ($type == IMAGETYPE_GIF)
		return "gif";
		else if ($type == IMAGETYPE_PNG)
		return "png";
		else if ($type == IMAGETYPE_BMP)
		return "png";
		else if ($type == IMAGETYPE_JPEG)
		return "jpg";

		return "";
	}

	static public function imageColorAllocateFromHex ($img, $hexstr)
	{
		$int = hexdec($hexstr);
		return ImageColorAllocate ($img,
		0xFF & ($int >> 0x10),
		0xFF & ($int >> 0x8),
		0xFF & $int);
	}

	// convert an image to a desired size while maintaining its aspect ratio
	// if the image is of type BMP it will be converted into JPEG
	// NOTE: images are only scaled down, so a small image wont be changed (apart for the JPEG quality)
	// the function returns the $target_file after changing its extension
	//
	static public function convertImage($source_file, $target_file,	$width = self::DEFAULT_THUMBNAIL_WIDTH, $height = self::DEFAULT_THUMBNAIL_HEIGHT, $crop_type = 1, $bgcolor = 0xffffff, $force_jpeg = false, $quality = 0, $src_x = 0, $src_y = 0, $src_w = 0, $src_h = 0)
	{
		$attributes = array();
		
		list($source_width, $source_height, $type, $attr) = getimagesize($source_file);

		if ($type == IMAGETYPE_BMP) // convert bmp to jpeg
			$type = IMAGETYPE_JPEG;

		if ($force_jpeg)
		{
			$target_file = kFile::replaceExt($target_file, "jpg");
			$type = IMAGETYPE_JPEG;
		}
		else
			$target_file = kFile::replaceExt($target_file, self::imageExtByType($type));

		$exif_data = exif_read_data($source_file);
		$orientation = $exif_data["Orientation"];
		
		switch($orientation)
		{
			case 1: // nothing
			break;
		
			case 2: // horizontal flip
				$attributes[] = "-flop";
			break;
									
			case 3: // 180 rotate left
				$attributes[] = "-rotate 180";
			break;
						
			case 4: // vertical flip
				$attributes[] = "-flip";
			break;
					
			case 5: // vertical flip + 90 rotate right
				$attributes[] = "-transpose";
			break;
					
			case 6: // 90 rotate right
				$attributes[] = "-rotate 90";
			break;
					
			case 7: // horizontal flip + 90 rotate right
				$attributes[] = "-transverse";
			break;
					
			case 8:    // 90 rotate left
				$attributes[] = "-rotate 270";
			break;
		}

		if($quality)
			$attributes[] = "-quality $quality";
			
		// pre-crop
		if($src_x || $src_y || $src_w || $src_h)
		{
			$geometrics = "{$src_w}x{$src_h}";
			$geometrics .= ($src_x < 0 ? $src_x : "+$src_x");
			$geometrics .= ($src_y < 0 ? $src_y : "+$src_y");
			
			$attributes[] = "-crop $geometrics";
		}
		
		// crop or resize
		if($width || $height)
		{
			switch($crop_type)
			{
				case self::CROP_TYPE_ORIGINAL_ASPECT_RATIO:
					$w = $width ? $width : '';
					$h = $height ? $height : '';
					$attributes[] = "-resize {$w}x{$h}";
					break;
					
				case self::CROP_TYPE_WITHIN_BG_COLOR:
					if($width && $height)
					{
						$borderWidth = 0;
						$borderHeight = 0;
						
						if($width < $height)
						{
							$w = $width;
							$h = ceil($source_height * ($width / $source_width));
							$borderHeight = ceil(($height - $h) / 2);
						}
						else 
						{
							$h = $height;
							$w = ceil($source_width * ($height / $source_height));
							$borderWidth = ceil(($width - $w) / 2);
						}
						
						$attributes[] = "-bordercolor '#$bgcolor'";
						$attributes[] = "-resize {$w}x{$h}";
						$attributes[] = "-border {$borderWidth}x{$borderHeight}";
					}
					else 
					{
						$w = $width ? $width : '';
						$h = $height ? $height : '';
						$attributes[] = "-resize {$w}x{$h}";
					}
					break;
					
				case self::CROP_TYPE_EXACT_SIZE:
				case self::CROP_TYPE_UPPER:
					$w = $width ? $width : $height;
					$h = $height ? $height : $width;
					
					$resizeWidth = '';
					$resizeHeight = '';
					
					if($width > $height)
						$resizeWidth = $width;
					else
						$resizeHeight = $height;
						
					
					if($crop_type == self::CROP_TYPE_EXACT_SIZE)
						$attributes[] = "-gravity Center";
					elseif($crop_type == self::CROP_TYPE_UPPER)
						$attributes[] = "-gravity North";
						
					$attributes[] = "-resize {$resizeWidth}x{$resizeHeight}";
					$attributes[] = "-crop {$w}x{$h}+0+0";
					break;
			}
		}

		// no conversion required
		if(!count($attributes))
		{
			copy($source_file, $target_file);
			return $target_file;
		}
		
		$options = implode(' ', $attributes);
		$cmd = "convert $options $source_file $target_file";
		$retValue = null;
		$output = system($cmd, $retValue);
		KalturaLog::info("ImageMagic cmd [$cmd] returned value [$retValue] output:\n$output");
		
		return $target_file;
	}

	static public function convertImageUsingCropProvider ( $source_file , $target_file ,
	$width = self::DEFAULT_THUMBNAIL_WIDTH, $height = self::DEFAULT_THUMBNAIL_HEIGHT, $crop_type = 1, $crop_provider = null, $bgcolor = 0xffffff, $force_jpeg = false,
	$quality = 0, $src_x = 0, $src_y = 0, $src_w = 0, $src_h = 0)
	{
		// first we create the thumbnail using the passed size
		self::convertImage($source_file, $target_file, $width, $height, $crop_type, $bgcolor, $force_jpeg, $quality, $src_x, $src_y, $src_w, $src_h);
		
		// now lets load our cropping provider and let it do its work
		if (!$crop_provider)
		{
			return $target_file;
		}
	
		// this will convert "some_provider_name" to "SomeProviderName" which is the file to include
		$temp_array = explode("_", $crop_provider);
		$class_name = "";
		foreach($temp_array as $key => $value)
		{
			if (strlen($value) > 0)
			{
				$value[0] = strtoupper($value[0]);
				$new_array[] = $value;
			}
		}
		$class_name = implode("", $new_array);
		$include_cp_path = SF_ROOT_DIR.DIRECTORY_SEPARATOR.'apps'.DIRECTORY_SEPARATOR.SF_APP.DIRECTORY_SEPARATOR.'lib/crop_providers/'.$class_name.".class.php";

		if (!file_exists($include_cp_path))
			die();				

		require_once($include_cp_path);
		executeCropProvider($source_file, $target_file, $width, $height, $crop_type, $bgcolor, $force_jpeg);
	}

	public static function getImageDimensions ( $source_file )
	{
		@list($sourcewidth, $sourceheight, $type, $attr, $srcIm) = self::createImageByFile($source_file);
		return array ( $sourcewidth , $sourceheight );
	}
	
	private static function ConvertBMP2GD($src, $dest = false) {
		if(!($src_f = fopen($src, "rb"))) {
			return false;
		}
		if(!($dest_f = fopen($dest, "wb"))) {
			return false;
		}
		$header = unpack("vtype/Vsize/v2reserved/Voffset", fread($src_f,
		14));
		$info = unpack("Vsize/Vwidth/Vheight/vplanes/vbits/Vcompression/Vimagesize/Vxres/Vyres/Vncolor/Vimportant",
		fread($src_f, 40));

		extract($info);
		extract($header);

		if($type != 0x4D42) { // signature "BM"
			return false;
		}

		$palette_size = $offset - 54;
		$ncolor = $palette_size / 4;
		$gd_header = "";
		// true-color vs. palette
		$gd_header .= ($palette_size == 0) ? "\xFF\xFE" : "\xFF\xFF";
		$gd_header .= pack("n2", $width, $height);
		$gd_header .= ($palette_size == 0) ? "\x01" : "\x00";
		if($palette_size) {
			$gd_header .= pack("n", $ncolor);
		}
		// no transparency
		$gd_header .= "\xFF\xFF\xFF\xFF";

		fwrite($dest_f, $gd_header);

		if($palette_size) {
			$palette = fread($src_f, $palette_size);
			$gd_palette = "";
			$j = 0;
			while($j < $palette_size) {
				$b = $palette{$j++};
				$g = $palette{$j++};
				$r = $palette{$j++};
				$a = $palette{$j++};
				$gd_palette .= "$r$g$b$a";
			}
			$gd_palette .= str_repeat("\x00\x00\x00\x00", 256 - $ncolor);
			fwrite($dest_f, $gd_palette);
		}

		$scan_line_size = (($bits * $width) + 7) >> 3;
		$scan_line_align = ($scan_line_size & 0x03) ? 4 - ($scan_line_size &
		0x03) : 0;

		for($i = 0, $l = $height - 1; $i < $height; $i++, $l--) {
			
			$gd_scan_line = null;
			// BMP stores scan lines starting from bottom
			fseek($src_f, $offset + (($scan_line_size + $scan_line_align) *
			$l));
			$scan_line = fread($src_f, $scan_line_size);
			if($bits == 24) {
				$gd_scan_line = "";
				$j = 0;
				while($j < $scan_line_size) {
					$b = $scan_line{$j++};
					$g = $scan_line{$j++};
					$r = $scan_line{$j++};
					$gd_scan_line .= "\x00$r$g$b";
				}
			}
			else if($bits == 8) {
				$gd_scan_line = $scan_line;
			}
			else if($bits == 4) {
				$gd_scan_line = "";
				$j = 0;
				while($j < $scan_line_size) {
					$byte = ord($scan_line{$j++});
					$p1 = chr($byte >> 4);
					$p2 = chr($byte & 0x0F);
					$gd_scan_line .= "$p1$p2";
				} $gd_scan_line = substr($gd_scan_line, 0, $width);
			}
			else if($bits == 1) {
				$gd_scan_line = "";
				$j = 0;
				while($j < $scan_line_size) {
					$byte = ord($scan_line{$j++});
					$p1 = chr((int) (($byte & 0x80) != 0));
					$p2 = chr((int) (($byte & 0x40) != 0));
					$p3 = chr((int) (($byte & 0x20) != 0));
					$p4 = chr((int) (($byte & 0x10) != 0));
					$p5 = chr((int) (($byte & 0x08) != 0));
					$p6 = chr((int) (($byte & 0x04) != 0));
					$p7 = chr((int) (($byte & 0x02) != 0));
					$p8 = chr((int) (($byte & 0x01) != 0));
					$gd_scan_line .= "$p1$p2$p3$p4$p5$p6$p7$p8";
				} $gd_scan_line = substr($gd_scan_line, 0, $width);
			}

			if ( $gd_scan_line ) fwrite($dest_f, $gd_scan_line);
		}
		fclose($src_f);
		fclose($dest_f);
		return true;
	}

	private static function imagecreatefrombmp($filename) {
		$tmp_name = tempnam("/tmp", "GD");
		if(self::ConvertBMP2GD($filename, $tmp_name)) {
			$img = imagecreatefromgd($tmp_name);
			unlink($tmp_name);
			return $img;
		} return false;
	}

	public static function createLogFileName ( $source_file , $plain_log_file_name = false )
	{
		$add_on = $plain_log_file_name ? "" :  "-" . time() ;
		return $source_file . $add_on . ".txt";
	}


	private static function addToLogFile ( $file_name , $str )
	{
		// TODO - append text to file, don't read it all and then write it again
		if ( file_exists ( $file_name ))		$log_content = @file_get_contents( $file_name ) ; // sync - OK
		else $log_content = "";
		$extra_content = "\n\n----------------------\n$str\n----------------------\n\n";
		file_put_contents( $file_name , $log_content . $extra_content ); // sync - OK
	}
}

class conversionInfo
{
	public $source_file_name;
	public $target_file_name;
	public $target_file_name_2;
	public $status_ok = true;
	public $video_width = 0;
	public $video_height = 0;
	public $video_framerate = 0;
	public $video_bitrate = 0;
	public $video_gop = 0;
	public $duration = 0;  // in milliseconds
	public $lasttimestamp = -1;
	public $extra_data = NULL;

	public function toString ()
	{
		return serialize( $this );
	}

	public static function fromString ( $str )
	{
		return unserialize( $str );
	}
	/**
	 * Will parse the metadata of the file and try and figure out the paramters
	 *  either by analyzing flv tags or by using ffmpeg -i
	 *
	 */
	public function fillFromMetadata ( $source_file )
	{
		$this->duration = -1;

		if ( $this->duration < 0 )
		{
			try
			{
				$duration_in_milliseconds = myFlvStaticHandler::getLastTimestamp( $source_file );
				if ( $duration_in_milliseconds > 0  )
				{
					$this->duration = $duration_in_milliseconds;
					return;
				}
			}
			catch ( Exception $ex )
			{
				// nothing much to do here
				$this->duration = -2;
			}
		}

		ob_start();
		$cmd_line = kConversionEngineFfmpeg::getCmd() . " -i \"". $source_file . "\" 2>&1";
		passthru( $cmd_line );
		$content = ob_get_contents();
		ob_end_clean();

		// Trying to find the duration from the output
		$subpattern = array ();
		// echo ( $log_file_content );
		if ( preg_match('/Duration: ([^,]*),/', $content , $subpattern) > 0 )
		{
			$duration_str = $subpattern[1]; // 00:00:20.1
			$arr = explode( ":" , $duration_str);
			if ( count ( $arr ) > 2 )
			{
				$duration_in_seconds = $arr[0] * 3600 + $arr[1] * 60 + $arr[2];
				$this->duration = $duration_in_seconds * 1000;
			}

		}

	}
}
?>