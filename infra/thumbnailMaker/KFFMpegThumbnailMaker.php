<?php
/**
 * @package infra
 * @subpackage Media
 */
class KFFMpegThumbnailMaker extends KBaseThumbnailMaker
{
	protected $cmdPath;
	
	/**
	 * @param string $filePath
	 * @param string $cmdPath
	 */
	public function __construct($srcPath, $targetPath, $cmdPath = 'ffmpeg')
	{
		$this->cmdPath = $cmdPath;
		parent::__construct($srcPath, $targetPath);
	}
	
	public function createThumnail($position = null, $width = null, $height = null, $frameCount = 1, $targetType = "image2")
	{
		KalturaLog::debug(__METHOD__."position = $position, width = $width, height = $height, frameCount = $frameCount, frameCount = $frameCount");
		$cmdArr = $this->getCommand($position, $width, $height, $frameCount, $targetType);

		$cmd= $cmdArr[0];
		{
			$rv = null;
			KalturaLog::info("Executing: $cmd");
			file_put_contents("$this->targetPath.log", $cmd, FILE_APPEND);
			$output = system( $cmd , $rv );
			KalturaLog::debug("Returned value: '$rv'");

			if($rv==0 && $this->parseOutput($output)==true)
				return true;
		}

		KalturaLog::debug("THUMB Capture Failure - First attempt failed due to ffmpeg crash or 'missing-keyframe' issue.\nSecond attempt with 'slow-thumb-capture' mode");
		$cmd= $cmdArr[1];
		if(isset($cmd) ){
			if($position>30) {
				KalturaLog::debug("THUMB Capture - can not run 2nd attempt - 'slow-thumb-capture' is allowed up to 30 sec position");
			}
			else {
				$rv = null;
				KalturaLog::info("Executing: $cmd");
				file_put_contents("$this->targetPath.log", $cmd, FILE_APPEND);
				$output = system( $cmd , $rv );
				KalturaLog::debug("Returned value: '$rv'");
				
				if($rv==0 && $this->parseOutput($output)==true)
					;//return true;
			}
		}

		return $rv? false: true;
	}
	
	protected function getCommand($position = null, $width = null, $height = null, $frameCount = 1, $targetType = "image2")
	{
		$dimensions = (is_null($width) || is_null($height)) ? '' : ("-s ". $width ."x" . $height);
		$position_str = $position ? " -ss $position " : '';
		$position_str_suffix = $position ? " -ss 0.01 " : "";
		$cmdArr = array();
		$cmdArr[] = "$this->cmdPath $position_str -i $this->srcPath -an -y -r 1 $dimensions -vframes $frameCount -f $targetType $position_str_suffix" .
			" $this->targetPath >> $this->targetPath.log 2>&1";
		$cmdArr[] = "$this->cmdPath -i $this->srcPath $position_str -an -y -r 1 $dimensions -vframes $frameCount -f $targetType" .
			" $this->targetPath >> $this->targetPath.log 2>&1";
		return $cmdArr;
		
	}
	
	protected function parseOutput($output)
	{
		$output=file_get_contents("$this->targetPath.log");
		if(strpos($output,"first frame not a keyframe")===false 
		&& strpos($output,"first frame is no keyframe")===false){
			return true;
		}

		return false;
	}
}