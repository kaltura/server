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

	static public function getFlvDuration ( $source_file )
	{
		$source_file = kFile::fixPath ( $source_file );

		$conversion_info = new conversionInfo();
		$conversion_info->fillFromMetadata( $source_file );

		return $conversion_info->duration;
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
	
	static public function createImageByFile($source_file)
	{
		global $global_kaltura_memory_limit;
		if ( ! isset ( $global_kaltura_memory_limit ) )
		{
			ini_set("memory_limit","256M");
		}
		
		if ( ! file_exists ($source_file))
		{
			KalturaLog::log( "file not found [$source_file]" ) ;
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