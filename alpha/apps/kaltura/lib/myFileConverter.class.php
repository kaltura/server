<?php

class myFileConverter
{
	const CROP_TYPE_ORIGINAL_ASPECT_RATIO = 1;
	const MENCODER = 'mencoder';
	const DEFAULT_THUMBNAIL_WIDTH = 120;
	const DEFAULT_THUMBNAIL_HEIGHT = 90;

	// -b 500kb -r 25 -g 5 -s 400x300 -ar 22050 -ac 2 -y 
	public static function formatConversionString ( $conversion_str , 
		$real_video_width = 400 , $real_video_height = 300 , $gop_size = -1 , $bitrate = -1 , $qscale = -1 )
	{
		// set good defaults to prevent division by zero
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
		
		$conversion_str = str_replace( array ( '{width}' , '{height}' ) , array ( $calculated_width , $calculated_height ) , $conversion_str );
		return $conversion_str;
	}

	/**
	 * @param $source_file
	 * @param $thumbTempPrefix
	 * @param null $position
	 * @param int $width
	 * @param int $height
	 * @param bool $plain_log_file_name
	 * @param null $decryptionKey
	 * @throws Exception
	 */
	static public function autoCaptureFrame ($source_file , $thumbTempPrefix , $position = null, $width = 0, $height = 0, $decryptionKey = null)
	{
		self::captureFrame($source_file, $thumbTempPrefix . '%d.jpg', 1, 'image2', $width, $height, $position, $decryptionKey );

		// in case the file was not created, the movie clip might be too short
		// try taking a snapshot at the first frame
		if (!kFile::checkFileExists($thumbTempPrefix . '1.jpg'))
		{
			self::captureFrame($source_file, $thumbTempPrefix.'%d.jpg', 1, 'image2', $width, $height, 0, $decryptionKey);
		}
	}


	/**
	 * @param $width
	 * @param $height
	 * @param $frame_count
	 * @param $position
	 * @param $source_file
	 * @throws Exception
	 */
	protected static function validateCaptureFrameInput($width, $height, $frame_count, $position, $source_file)
	{
		$validInput = TRUE;
		$validInput &= is_numeric($width);
		$validInput &= is_numeric($height);
		$validInput &= is_numeric($frame_count);
		$validInput &= is_numeric($position);
		$validInput &= (realpath($source_file) !== FALSE);

		if(!$validInput)
		{
			throw new Exception('Illegal input was given');
		}
	}

	/**
	 * @param $source_file
	 * @param $target_file
	 * @param int $frame_count
	 * @param string $target_type
	 * @param int $width
	 * @param int $height
	 * @param int $position
	 * @param string $decryptionKey
	 * @return array
	 * @throws Exception
	 */
	static public function captureFrame($source_file , $target_file , $frame_count = 1, $target_type = 'image2',
										 $width = self::DEFAULT_THUMBNAIL_WIDTH , $height = self::DEFAULT_THUMBNAIL_HEIGHT , $position = null, $decryptionKey = null )
	{
		if ($width == 0)
		{
			$width = self::DEFAULT_THUMBNAIL_WIDTH;
		}

		if ($height == 0)
		{
			$height = self::DEFAULT_THUMBNAIL_HEIGHT;
		}

		self::validateCaptureFrameInput($width, $height, $frame_count, $position, $source_file);
		$captureCmd = kFfmpegUtils::getCaptureFrameCmd($source_file, $target_file, $position, $width, $height, $frame_count, $target_type, $decryptionKey);
		list($output, $return_value) = kFfmpegUtils::executeCmd($captureCmd);
		if($position<30 && isset($position_str))
		{
			foreach($output as $outLine)
			{
				if(strpos($outLine,'first frame not a keyframe') === false && strpos($outLine,'first frame is no keyframe') === false)
				{
					continue;
				}

				KalturaLog::log("FFMpeg response - \n" . print_r(implode($output),1));
				KalturaLog::log("The ffmpeg responded with 'first-frame-not-a-keyframe'. The fast-seek mode failed to properly get the right frame. Switching to the 'slow-mode' that is limited to th3 first 30sec only ".print_r(implode($output),1));
				$capturesSlowCmd = kFfmpegUtils::getSlowCaptureFrameCmd($source_file, $target_file, $position, $width, $height, $frame_count, $target_type, $decryptionKey);
				list($output, $return_value) = kFfmpegUtils::executeCmd($capturesSlowCmd);
				break;
			}
		}

		$conversion_info = new kConversionInfo();
		$conversion_info->fillFromMetadata( $source_file );
		$conversion_info->video_width = $width;
		$conversion_info->video_height = $height;
		// encapsulate
		return array ( 'return_value' => $return_value , 'output' => $output , 'conversion_info' => $conversion_info );
	}

	// Use ffmpeg to extract the video dimensions
	/**
	 * @param string $source_file
	 * @return array
	 * @throws Exception
	 */
	public static function getVideoDimensions ($source_file)
	{
		$source_file = kFile::fixPath( $source_file );
		$size = kFfmpegUtils::extractInfo($source_file);
		$width = '';
		$height = '';
		//extract the video size line (used to suggest 25x25 size for entry 25x25xqajo)
		// used to search for , after the {width}x{height} however both of the following lines are valid:
		// Stream #0.0: Video: h264, yuv420p, 320x240 [PAR 1:1 DAR 4:3], 202 kb/s, 29.92 tbr, 1k tbn, 2k tbc
		// Stream #0.1(und): Video: h264, yuv420p, 480x270, 59.92 tbr, 29.94 tbn, 59.89 tbc
		if (preg_match('/Video:.*? (\d{2,4})x(\d{2,4})/', $size, $matches))
		{
			$width = $matches[1];
			$height = $matches[2];
		}

		return array ( $width , $height );
	}

	/**
	 * @param string $source_file
	 * @return array|null
	 * @throws Exception
	 */
	static public function createImageByFile($source_file)
	{
		global $global_kaltura_memory_limit;
		if ( ! isset ( $global_kaltura_memory_limit ) )
		{
			ini_set('memory_limit','256M');
		}
		
		if(!kFile::checkFileExists($source_file))
		{
			KalturaLog::log( "file not found [$source_file]" ) ;
			return null;	
		}
		
		if(!kFile::isFile($source_file))
		{
			KalturaLog::log( "path is not file [$source_file]" ) ;
			return null;	
		}
		
		list($sourcewidth, $sourceheight, $type, $attr) = getimagesize($source_file);

		$srcIm = NULL;
		switch ( $type )
		{
			case IMAGETYPE_JPEG:
				$srcIm = imagecreatefromjpeg( $source_file );
				break;
			case IMAGETYPE_PNG:
				$srcIm = imagecreatefrompng( $source_file );
				break;
			case IMAGETYPE_GIF:
				$srcIm = imagecreatefromgif( $source_file );
				break;
			case IMAGETYPE_WBMP:
				$srcIm = imagecreatefromwbmp( $source_file );
				break;
			default: $srcIm = NULL;
		}

		if( !$srcIm )
		{
			$output = array();
			$cmd = kConf::get ( 'bin_path_imagemagick');
			$jpeg_file = myContentStorage::getFSUploadsPath(true).pathinfo($source_file, PATHINFO_FILENAME) . '.jpg';
			$source_file = kFile::realPath($source_file);
			if($source_file === FALSE)
			{
				throw new Exception('Illegal input was given');
			}
			
			exec($cmd . " \"$source_file\" \"$jpeg_file\"", $output);
			if (kFile::checkFileExists($jpeg_file))
			{
				list($sourcewidth, $sourceheight, $type, $attr) = getimagesize($jpeg_file);
				$srcIm = imagecreatefromjpeg( $jpeg_file );
			}
		}
		
		// if image failed to load return a null result instead of an errornous one due to a bad file
		if (!$srcIm)
		{
			return array(0, 0, IMAGETYPE_JPEG, '', null);
		}
	        
		return array($sourcewidth, $sourceheight, $type, $attr, $srcIm);
	}

	// create a thumbnail from an image
	// source_file:  source file
	// target_file:  target file
	// target_type:  target file type
	// width : target image width
	// height : target image height
	static public function createImageThumbnail ( $source_file , $target_file , $target_type = 'image2' , $width = self::DEFAULT_THUMBNAIL_WIDTH , $height = self::DEFAULT_THUMBNAIL_HEIGHT )
	{
		if (!kFile::checkFileExists($source_file))
		{
			KalturaLog::log ( __CLASS__ . " File not found [$source_file]" );
			return;
		}

		if (kFile::isDir($source_file))
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
		if($ratio1>$ratio2)
		{
			$height=$sourceheight/$ratio1;
		}
		else
		{
			$width=$sourcewidth/$ratio2;
		}

		imagecopyresampled( $im, $srcIm, max( ( self::DEFAULT_THUMBNAIL_WIDTH - $width ) / 2 , 0), max( ( self::DEFAULT_THUMBNAIL_HEIGHT - $height ) / 2 ,  0), 0 , 0 , $width  , $height, $sourcewidth , $sourceheight );
		imagedestroy($srcIm);
		imagejpeg($im, $target_file);
		imagedestroy($im);
	}

	/**
	 * return the extension (as string) according to it's type
	 * @param int $type
	 * @return string
	 */
	static public function imageExtByType($type)
	{
		if ($type == IMAGETYPE_GIF)
		{
			return 'gif';
		}
		else if ($type == IMAGETYPE_PNG)
		{
			return 'png';
		}
		else if ($type == IMAGETYPE_BMP)
		{
			return 'png';
		}
		else if ($type == IMAGETYPE_JPEG)
		{
			return 'jpg';
		}
		else if($type == IMAGETYPE_TIFF_II || $type == IMAGETYPE_TIFF_MM)
		{
			return 'tiff';
		}

		return '';
	}

	/**
	 *
	 * convert an image to a desired size while maintaining its aspect ratio
	 * if the image is of type BMP it will be converted into JPEG
	 * NOTE: images are only scaled down, so a small image wont be changed (apart for the JPEG quality)
	 * the function returns the $target_file after changing its extension
	 * @param string $source_file - Source file path
	 * @param string $target_file - Target file path (after converting)
	 * @param int $width - Requested width in pixels
	 * @param int $height - Requested height in pixels
	 * @param int $crop_type - Type of crop to be used [1-4] :
	 *        self::CROP_TYPE_ORIGINAL_ASPECT_RATIO:    Resize according to the given dimensions while maintaining the original aspect ratio.
	 *        self::CROP_TYPE_WITHIN_BG_COLOR:        Place the image within the given dimensions and fill the remaining spaces using the given background color.
	 *        self::CROP_TYPE_EXACT_SIZE:                Crop according to the given dimensions while maintaining the original aspect ratio.
	 *                                                The resulting image may be cover only part of the original image.
	 *        self::CROP_TYPE_UPPER:                    Crops the image so that only the upper part of the image remains.
	 * @param int $bgcolor - backround color (6 hex digits web colorcode)
	 * @param bool $force_jpeg - Force the source image file to convert into a Jpeg file
	 * @param int $quality - Jpeg quality for output [0-100]
	 * @param int $src_x - 1st part of a rectangle to take from original picture (starting from vertical picsal {value} to right end of picture)
	 * @param int $src_y - 2nd part of a rectangle to take from original picture (starting from horizonal picasl {value} downto down end of picture)
	 * @param int $src_w - 3rd part of a rectangle to take from original picture (starting from picsal left end of picture to vertical pixel {value})
	 * @param int $src_h - 4rd part of a rectangle to take from original picture (starting from up end of picture downto horizonal pixesl {value})
	 * @param int $density
	 * @param bool $stripProfiles
	 * @param array $thumbParams
	 * @param string $format
	 * @param bool $forceRotation
	 * @return path to targetFile or null if the $source_file is not an image file
	 * @throws Exception
	 */
	static public function convertImage($source_file, $target_file,	$width = self::DEFAULT_THUMBNAIL_WIDTH, $height = self::DEFAULT_THUMBNAIL_HEIGHT,
		$crop_type = self::CROP_TYPE_ORIGINAL_ASPECT_RATIO, $bgcolor = 0xffffff, $force_jpeg = false, $quality = 0,
		$src_x = 0, $src_y = 0, $src_w = 0, $src_h = 0, $density = 0, $stripProfiles = false, $thumbParams = null, $format = null, $forceRotation = null)
	{
		if (is_null($thumbParams) || !($thumbParams instanceof kThumbnailParameters))
			$thumbParams = new kThumbnailParameters();

		if (is_string($bgcolor) && strpos($bgcolor, '0x') === false)
			$bgcolor = hexdec('0x' . $bgcolor);
		
		// check if the source file is not an image file
		if (!file_exists($source_file) || filesize($source_file) === 0 || getimagesize($source_file) === false)
		{
        	KalturaLog::log("convertImage - failed to get image size [$source_file] while creating [$target_file]");
        		return null;
		}
		
		// change target file extension if needed
		list($source_width, $source_height, $type, $attr) = getimagesize($source_file);
		if ($type == IMAGETYPE_BMP) // convert bmp to jpeg
			$type = IMAGETYPE_JPEG;
		if ($force_jpeg)
		{
			$ext = self::imageExtByType($type);
			if($thumbParams->getSupportAnimatedThumbnail() && $ext == "gif")
			{
				$target_file = kFile::replaceExt($target_file, "gif");
			}
			else
			{
				$target_file = kFile::replaceExt($target_file, "jpg");
				$type = IMAGETYPE_JPEG;
			}
		}
		else
			$target_file = kFile::replaceExt($target_file, self::imageExtByType($type));
		
		if(!is_null($format))
			$target_file = kFile::replaceExt($target_file, $format);
		
		// do convertion
		if (file_exists($target_file))
			unlink($target_file); // remove target file before converting in order to avoid imagemagick security wrapper script from detetcing irrelevant errors  
		$status = null;
		$imageCropper = new KImageMagickCropper($source_file, $target_file, kConf::get('bin_path_imagemagick'));
		$status = $imageCropper->crop($quality, $crop_type, $width, $height, $src_x, $src_y, $src_w, $src_h, null, null, $bgcolor, $density, $forceRotation, $stripProfiles);
		if (!$status)
			return null;
		return $target_file;
	}

	/**
	 *
	 *  convert an image to a desired size while maintaining its aspect ratio.
	 *  use a provided crop to load the image onto it
	 * @param unknown_type $source_file - see myFileConverter::converImage
	 * @param unknown_type $target_file - see myFileConverter::converImage
	 * @param int $width - see myFileConverter::converImage
	 * @param int $height - see myFileConverter::converImage
	 * @param int $crop_type - see myFileConverter::converImage
	 * @param unknown_type $crop_provider -
	 * @param int $bgcolor - see myFileConverter::converImage
	 * @param bool $force_jpeg - see myFileConverter::converImage
	 * @param int $quality - see myFileConverter::converImage
	 * @param int $src_x - see myFileConverter::converImage
	 * @param int $src_y - see myFileConverter::converImage
	 * @param int $src_w - see myFileConverter::converImage
	 * @param int $src_h - see myFileConverter::converImage
	 * @param int $density
	 * @param bool $stripProfiles
	 * @param bool $forceRotation
	 * @return unknown_type
	 * @throws Exception
	 */
	static public function convertImageUsingCropProvider ($source_file , $target_file ,
	$width = self::DEFAULT_THUMBNAIL_WIDTH, $height = self::DEFAULT_THUMBNAIL_HEIGHT, $crop_type = 1, $crop_provider = null, $bgcolor = 0xffffff, $force_jpeg = false,
	$quality = 0, $src_x = 0, $src_y = 0, $src_w = 0, $src_h = 0, $density = 0, $stripProfiles = false, $forceRotation = null)
	{
		// first we create the thumbnail using the passed size
		self::convertImage($source_file, $target_file, $width, $height, $crop_type, $bgcolor, $force_jpeg, $quality, $src_x, $src_y, $src_w, $src_h, $density, $stripProfiles, null, null, $forceRotation);
		
		// now lets load our cropping provider and let it do its work
		if (!$crop_provider)
		{
			return $target_file;
		}
	
		// this will convert "some_provider_name" to "SomeProviderName" which is the file to include
		$temp_array = explode("_", $crop_provider);
		$new_array = array();
		foreach($temp_array as $key => $value)
		{
			if (strlen($value) > 0)
			{
				$value[0] = strtoupper($value[0]);
				$new_array[] = $value;
			}
		}

		$class_name = implode('', $new_array);
		$include_cp_path = SF_ROOT_DIR.DIRECTORY_SEPARATOR.'apps'.DIRECTORY_SEPARATOR.SF_APP.DIRECTORY_SEPARATOR.'lib/crop_providers/'.$class_name.".class.php";

		if (!file_exists($include_cp_path))
			die();				

		require_once($include_cp_path);
		executeCropProvider($source_file, $target_file, $width, $height, $crop_type, $bgcolor, $force_jpeg);
	}

	public static function getImageDimensionsFromString($imgStr)
	{
		$image = imagecreatefromstring($imgStr);
		$width = imagesx($image);
		$height = imagesy($image);
		imagedestroy($image);
		return array($width, $height);
	}
	
	public static function createLogFileName ( $source_file , $plain_log_file_name = false )
	{
		$add_on = $plain_log_file_name ? "" :  "-" . time() ;
		return $source_file . $add_on . ".txt";
	}

}
