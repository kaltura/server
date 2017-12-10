<?php
/**
 * @package server-infra
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
	
	public function createThumnail($position = null, $width = null, $height = null, $params = array())
	{
		$params = self::normalizeParams($params);
		
		KalturaLog::debug("position[$position], width[$width], height[$height], params[".serialize($params)."]");
		$dar = $params['dar'];
		if(isset($dar) && $dar>0 && isset($height)){
			$width = floor(round($height*$dar) /2) * 2;
		}
		// TODO - calculate the width and height according to dar
		$cmdArr = $this->getCommand($position, $width, $height, $params);

		$cmd= $cmdArr[0];
		$rv = null;
		KalturaLog::info("Executing: $cmd");
		$logFilePath = "$this->targetPath.log";
		
		$logFileDir = dirname($logFilePath);
		if(!file_exists($logFileDir))
			mkdir(dirname($logFilePath), 0665, true);
			
		file_put_contents($logFilePath, $cmd, FILE_APPEND);
		$output = system( $cmd , $rv );
		KalturaLog::debug("Returned value: '$rv'");

		if($rv==0 && $this->parseOutput($output)==true)
			return true;

		KalturaLog::warning("First attempt failed due to ffmpeg crash or 'missing-keyframe' issue.\nSecond attempt with 'slow-thumb-capture' mode");
		$cmd= $cmdArr[1];
		if(isset($cmd) ){
			if($position>30) {
				KalturaLog::err("Can not run 2nd attempt - 'slow-thumb-capture' is allowed up to 30 sec position");
			}
			else {
				$rv = null;
				KalturaLog::info("Executing: $cmd");
				file_put_contents($logFilePath, $cmd, FILE_APPEND);
				$output = system( $cmd , $rv );
				KalturaLog::debug("Returned value: '$rv'");
				
				if($rv==0 && $this->parseOutput($output)==true)
					;//return true;
			}
		}

		return $rv? false: true;
	}
	
	protected function getCommand($position = null, $width = null, $height = null, $params = array())
	{
		$frameCount = $params['frameCount']; 
		$targetType = $params['targetType']; 
		$vidDur = 	$params['vidDur'];
		$scanType = $params['scanType'];
		if(isset($scanType) && $scanType==1)
			$scanType = " -deinterlace";
		else $scanType = null;

		$dimensions = (is_null($width) || is_null($height)) ? '' : ("-s ". $width ."x" . $height);
		
		//In case the video length is less than 30 sec to the seek in the decoding phase and not in the muxing phase (related to SUP-2172)
		if(!isset($vidDur) || $vidDur > 30)
		{
			$position_str = $position ? " -ss $position " : '';
			$position_str_suffix = $position ? " -ss 0.01 " : "";
		}
		else
		{
			$position_str = '';
			$position_str_suffix = $position ? " -ss $position " : "";
		}
		
		$cmdArr = array();
			// '-noautorotate' to adjust to ffm2.7.2 that automatically normalizes rotated sources
		$cmdArr[] = "$this->cmdPath $position_str -noautorotate -i $this->srcPath -an$scanType -y -r 1 $dimensions -vframes $frameCount -f $targetType $position_str_suffix" .
			" $this->targetPath >> $this->targetPath.log 2>&1";
		$cmdArr[] = "$this->cmdPath -noautorotate -i $this->srcPath $position_str -an$scanType -y -r 1 $dimensions -vframes $frameCount -f $targetType" .
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
