<?php
/**
 * @package Core
 * @subpackage utils
 */

class kFfmpegUtils
{
	const FFMPEG_PATH_CONF_NAME = 'bin_path_ffmpeg';
	const MAX_EXECUTION_TIME = 120;

	/**
	 * @param string $source_file
	 * @param string $target_file
	 * @param $position
	 * @param int $width
	 * @param int $height
	 * @param int $frame_count
	 * @param string $target_type
	 * @param string $decryptionKey
	 * @return string
	 * @throws Exception
	 */
	public static function getSlowCaptureFrameCmd($source_file, $target_file, $position, $width, $height, $frame_count, $target_type, $decryptionKey = null)
	{
		$position_str = $position ? " -ss $position " : '';
		$dimensions = ($width == -1 || $height == -1) ? '' : ('-s '. $width . 'x' . $height);
		$source_file = kFile::realPath($source_file);
		$cmd = " -noautorotate -i \"$source_file\"". $position_str  . ' -an -y -r 1 ' . $dimensions .
				' ' . " -vframes $frame_count -f \"" . $target_type . "\" " . "\"$target_file\"" . ' 2>&1';
		if ($decryptionKey)
		{
			$cmd = ' -decryption_key ' . $decryptionKey . $cmd;
		}

		return $cmd;
	}

	/**
	 * The '-ss 0.01' is  'dummy' seek-to setting is done to ensure preciseness of the main seek command
	 * that is done at the beginning of the command line here 'the -ss 0.01' is presented in the $position_str_suffix
	 * the $position_str presets the specific second to capture
	 * @param string $source_file
	 * @param string $target_file
	 * @param $position
	 * @param int $width
	 * @param int $height
	 * @param int $frame_count
	 * @param string $target_type
	 * @param string $decryptionKey
	 * @return string
	 * @throws Exception
	 */
	public static function getCaptureFrameCmd($source_file, $target_file, $position, $width, $height, $frame_count, $target_type, $decryptionKey = null)
	{
		$position_str = $position ? " -ss $position " : '';
		$dimensions = ($width == -1 || $height == -1) ? '' : ('-s '. $width . 'x' . $height);
		$source_file = kFile::realPath($source_file);
		$position_str_suffix = $position ? ' -ss 0.01 ' : '';
		$cmd = $position_str . ' -noautorotate -i ' . "\"$source_file\"" . ' -an -y -r 1 ' . $dimensions .
			' ' . " -vframes $frame_count -f \"" . $target_type . "\" " . $position_str_suffix . "\"$target_file\"" . ' 2>&1';
		if ($decryptionKey)
		{
			$cmd = ' -decryption_key ' . $decryptionKey . $cmd;
		}

		return $cmd;
	}

	public static function getCopyCmd($source, $clipToSec, $target)
	{
		$source = kFile::realPath($source);
		return " -i {$source} -vcodec copy -acodec copy -f mp4 -t {$clipToSec} -y {$target} 2>&1";
	}

	public static function executeCmd($cmd, $timeLimit = self::MAX_EXECUTION_TIME)
	{
		$baseCmd = kConf::get(self::FFMPEG_PATH_CONF_NAME);
		$exec_cmd = $baseCmd . $cmd;
		KalturaLog::log("ffmpeg cmd [$exec_cmd]");
		$output = array();
		$return_value = '';
		if($timeLimit)
		{
			set_time_limit(self::MAX_EXECUTION_TIME);
		}

		exec ( $exec_cmd , $output , $return_value );
		return array($output, $return_value);
	}

	/**
	 * @param string $filePath
	 * @return false|string
	 * @throws Exception
	 */
	public static function extractInfo($filePath)
	{
		$filePath = kFile::realPath($filePath);
		if ($filePath === FALSE)
		{
			throw new Exception('Illegal input was supplied');
		}

		$baseCmd = kConf::get(self::FFMPEG_PATH_CONF_NAME);
		$cmd_line = $baseCmd . " -i \"". $filePath . "\" 2>&1";
		KalturaLog::log("ffmpeg cmd [$cmd_line]");
		ob_start();
		passthru( $cmd_line );
		$content = ob_get_contents();
		ob_end_clean();
		return $content;
	}
}