<?php
/**
* @package Core
* @subpackage utils
*/

class kConversionInfo
{
	public $video_width = 0;
	public $video_height = 0;
	public $video_bitrate = 0;
	public $duration = 0;  // in milliseconds

	public function toString ()
	{
		return serialize( $this );
	}

	public static function fromString ( $str )
	{
		return unserialize( $str );
	}

	/**
	 * Will parse the metadata of the file and try and figure out the parameters
	 *  either by analyzing flv tags or by using ffmpeg -i
	 *
	 * @param string $source_file
	 * @throws Exception
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

		$content = kFfmpegUtils::extractInfo($source_file);

		// Trying to find the duration from the output
		$subPattern = array ();
		if ( preg_match('/Duration: ([^,]*),/', $content , $subPattern) > 0 )
		{
			$duration_str = $subPattern[1]; // 00:00:20.1
			$arr = explode( ':' , $duration_str);
			if ( count ( $arr ) > 2 )
			{
				$duration_in_seconds = $arr[0] * 3600 + $arr[1] * 60 + $arr[2];
				$this->duration = $duration_in_seconds * 1000;
			}
		}
	}
}