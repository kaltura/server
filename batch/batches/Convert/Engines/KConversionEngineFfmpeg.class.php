<?php
/**
 * @package Scheduler
 * @subpackage Conversion.engines
 */
class KConversionEngineFfmpeg  extends KJobConversionEngine
{
	const FFMPEG = "ffmpeg";
	
	const TAG_VARIANT_A = 'watermark_a';
	const TAG_VARIANT_B = 'watermark_b';
	const TAG_VARIANT_PAIR_ID = 'watermark_pair_';
	const TAG_NGS_STUB = "stub";

	public function getName()
	{
		return self::FFMPEG;
	}
	
	public function getType()
	{
		return KalturaConversionEngineType::FFMPEG;
	}
	
	public function getCmd ()
	{
		return KBatchBase::$taskConfig->params->ffmpegCmd;
	}
	
	/**
	 *
	 */
	protected function getExecutionCommandAndConversionString ( KalturaConvertJobData $data )
	{
		$wmData = null;
		if(isset($data->flavorParamsOutput->watermarkData)){
			$wmData = json_decode($data->flavorParamsOutput->watermarkData);
			if(!isset($wmData)){
				KalturaLog::err("Bad watermark JSON string($data->flavorParamsOutput->watermarkData), carry on without watermark");
			}
		}
		$cmdLines =  parent::getExecutionCommandAndConversionString ($data);
		KalturaLog::log("cmdLines==>".print_r($cmdLines,1));
			/*
			 * The code below handles the ffmpeg 0.10 and higher option to set up 'forced_key_frame'.
			 * The ffmpeg cmd-line should contain list of all forced kf's, this list might be up to 40Kb for 2hr videos.
			 * Since the cmd-lines are stored in db records (flavor_params_output), it would blow it up.
			 * The solution is to setup a placeholer w/duration and step, the full cmd-line is generated over here
			 * just before the activation.
			 * Sample:
			 *    	__forceKeyframes__462_2
			 *		stands for duration of 462 seconds, gop size 2 seconds
			 */
		foreach($cmdLines as $k=>$cmdLine){
			$exec_cmd = self::experimentalFixing($cmdLine->exec_cmd, $data->flavorParamsOutput, $this->getCmd(), $this->inFilePath, $this->outFilePath);
			$exec_cmd = self::expandForcedKeyframesParams($exec_cmd);
			
			if(strstr($exec_cmd, "ffmpeg")==false) {
				$cmdLines[$k]->exec_cmd = $exec_cmd;
				continue;
			}
			
				// impersonite
			KBatchBase::impersonate($data->flavorParamsOutput->partnerId);
			
			$wmCmdLine = null;
			if(isset($wmData)){
				$wmCmdLine = self::buildWatermarkedCommandLine($wmData, $data->destFileSyncLocalPath, $exec_cmd, 
								KBatchBase::$taskConfig->params->ffmpegCmd, KBatchBase::$taskConfig->params->mediaInfoCmd);
			}
					/*
					 * 'watermark_pair_'/TAG_VARIANT_PAIR_ID tag for NGS digital signature watermarking flow
					 */
			if(isset($data->flavorParamsOutput->tags) && strstr($data->flavorParamsOutput->tags,KConversionEngineFfmpeg::TAG_VARIANT_PAIR_ID)!=false){
				$wmCmdLine = self::buildNGSPairedDigitalWatermarkingCommandLine((isset($wmCmdLine)?$wmCmdLine:$exec_cmd), $data);
			}
				// un-impersonite
			KBatchBase::unimpersonate();
				
			if(isset($wmCmdLine))
				$cmdLines[$k]->exec_cmd = $wmCmdLine;
			else
				$cmdLines[$k]->exec_cmd = $exec_cmd;
		}
		return $cmdLines;
	}
	
	/**
	 * 
	 * @param unknown_type $execCmd
	 * @return unknown|mixed
	 */
	public static function expandForcedKeyframesParams($execCmd)
	{
		$cmdLineWithKeyframes = strstr($execCmd, KDLCmdlinePlaceholders::ForceKeyframes);
		if($cmdLineWithKeyframes==false){
			return $execCmd;
		}
		
		$cmdLineWithKeyframes = explode(" ",$cmdLineWithKeyframes);	// 
		$cmdLineWithKeyframes = $cmdLineWithKeyframes[0];
		$kfPrms = substr($cmdLineWithKeyframes,strlen(KDLCmdlinePlaceholders::ForceKeyframes));
		$kfPrms = explode("_",$kfPrms);
		$forcedKF=null;
		for($t=0,$tr=0;$t<=$kfPrms[0]; $t+=$kfPrms[1], $tr+=round($kfPrms[1])){
			// The check bellow is to prevent 'dripping' of the kf timing
			if($tr && round($t)>$tr) {
				$t=$tr;
			}
			$forcedKF.=",".round($t,4);
		}
		$forcedKF[0] = ' ';
		$execCmd = str_replace ( 
				array($cmdLineWithKeyframes), 
				array($forcedKF),
				$execCmd);
		return $execCmd;
	}
	
	/**
	 * 
	 * @param unknown_type $cmdStr
	 * @param unknown_type $flavorParamsOutput
	 * @param unknown_type $binCmd
	 * @param unknown_type $srcFilePath
	 * @param unknown_type $outFilePath
	 * @return unknown|string
	 */
	public static function experimentalFixing($cmdStr, $flavorParamsOutput, $binCmd, $srcFilePath, $outFilePath)
	{
/*
 * Samples - 
 * Original 
 * ffmpeg -i SOURCE 
 * 	   -c:v libx265 
 * 		-pix_fmt yuv420p -aspect 640:360 -b:v 8000k -s 640x360 -r 30 -g 60 
 * 		-c:a libfdk_aac -b:a 128k -ar 44100 -f mp4 -y OUTPUT
 * 
 * Switched - 
 * ffmpeg -i SOURCE 
 * 		-pix_fmt yuv420p -aspect 640:360 -b:v 8000k -s 640x360 -r 30 -g 60 -f yuv4mpegpipe -an - 
 * 		-vn 
 * 		-c:a libfdk_aac -b:a 128k -ar 44100 -f mp4 -y OUTPUT.aac 
 * 		| /home/dev/x265 - --y4m --scenecut 40 --keyint 60 --min-keyint 1 --bitrate 2000 --qpfile OUTPUT.qp OUTPUT.h265 
 * 		&& ~/ffmpeg-2.4.3 -i OUTPUT.aac -r 30 -i OUTPUT.h265 -c copy -f mp4 -y OUTPUT
 * 
 */

		/*
		 * New binaries/aliases on transcoding servers
		 */
$x265bin = "x265";
$ffmpegExperimBin = "ffmpeg-experim";

		if($flavorParamsOutput->videoCodec==KDLVideoTarget::H265){ //video_codec	!!!flavorParamsOutput->videoCodec
			KalturaLog::log("trying to fix H265 conversion");
			$gop = $flavorParamsOutput->gopSize; 					//gop_size	!!!$flavorParamsOutput->gopSize;
			$vBr = $flavorParamsOutput->videoBitrate; 				//video_bitrate	!!!$flavorParamsOutput->videoBitrate;
			$frameRate = $flavorParamsOutput->frameRate; 			//frame_rate	!!!$flavorParamsOutput->frameRate;
				
$threads = 4;
$pixFmt = "yuv420p";
			$cmdValsArr = explode(' ', $cmdStr);
			
			/*
			 * Rearrange the ffmpeg cmd-line into a complex pipe and multiple command
			 * - ffmpeg transcodes audio into an output.AAC file and decodes video into a raw resized video to be piped
			 * - into x265 that encodes raw output.h265
			 * - upon completion- mux into an out.mp4
			 * 
			 * To Do's
			 * - support other audio
			 * - support other formats
			 * 
			 */
			
				/*
				 * remove video codec
				 */
			if(in_array('-c:v', $cmdValsArr)) {
				$key = array_search('-c:v', $cmdValsArr);
				unset($cmdValsArr[$key+1]);
				unset($cmdValsArr[$key]);
			}
			if(in_array('-threads', $cmdValsArr)) {
				$key = array_search('-threads', $cmdValsArr);
				$threads = $cmdValsArr[$key+1];
			}
				/*
				 * add dual stream generation
				 */
			if(in_array('-c:a', $cmdValsArr)) {
				$key = array_search('-c:a', $cmdValsArr);
				$cmdValsArr[$key] = "-f yuv4mpegpipe -an - -vn -c:a";
			}
				/*
				 * handle pix-format (main vs main10)
				 */
			if(in_array('-pix_fmt', $cmdValsArr)) {
				$key = array_search('-pix_fmt', $cmdValsArr);
				$pixFmt = $cmdValsArr[$key+1];
			}
			switch($pixFmt){
				case "yuv420p10":
				case "yuv422p":
					$profile = "main10";
					break;
				case "yuv420p":
				default:
					$profile = "main";
					break;
			}

				/*
				 * Get source duration
				 */
			$ffParser = new KFFMpegMediaParser($srcFilePath);
			$ffMi = null;
			try {
				$ffMi = $ffParser->getMediaInfo();
			}
			catch(Exception $ex)
			{
				KalturaLog::log(print_r($ex,1));
			}
			if(isset($ffMi->containerDuration) && $ffMi->containerDuration>0)
				$duration = $ffMi->containerDuration/1000;
			else if(isset($ffMi->videoDuration) && $ffMi->videoDuration>0)
				$duration = $ffMi->videoDuration/1000;
			else if(isset($ffMi->audioDuration) && $ffMi->audioDuration>0)
				$duration = $ffMi->audioDuration/1000;
			else
				$duration = 0;
			
			$keyFramesArr = array();
			/*
			 * Generate x265 qpfile with forced key-frames
			 */
			if(isset($gop) && $gop>0 && isset($frameRate) && $frameRate>0 && isset($duration) && $duration>0){
				$gopInSec 	= $gop/round($frameRate);
				$frameDur 	= 1/$frameRate;
				for($kfTime=0,$kfId=0,$kfTimeGop=0;$kfTime<$duration; ){
					$keyFramesArr[] = $kfId;
					$kfId+=$gop;
					$kfTime=$kfId*$frameDur;
					$kfTimeGop+=$gopInSec;
					$kfTimeDelta = $kfTime-$kfTimeGop;
						/*
						 * Check for time derift conditions (for float fps, 29.97/23.947/etc) and fix when required
						 */
					if(abs($kfTimeDelta)>$frameDur){
						$aaa = $kfId;
						if($kfTimeDelta>0)
							$kfId--;
						else
							$kfId++;
					}
				}
				$keyFramesStr = implode(" I\n",$keyFramesArr)." I\n";
				file_put_contents("$outFilePath.qp", $keyFramesStr);
			}
			else {
				KalturaLog::log("Missing gop($gop) or frameRate($frameRate) or duration($duration) - will be generated without fixed keyframes!");
			}

			if(!in_array($outFilePath, $cmdValsArr)) {
				return $cmdStr;
			}
			
			$key = array_search($outFilePath, $cmdValsArr);
			$cmdValsArr[$key] = "$outFilePath.aac |"; 
			$cmdValsArr[$key].= " $x265bin - --profile $profile --y4m --scenecut 40 --min-keyint 1";
			if(isset($gop)) $cmdValsArr[$key].= " --keyint $gop";
			if(isset($vBr)) $cmdValsArr[$key].= " --bitrate $vBr";
			if(count($keyFramesArr)>0) $cmdValsArr[$key].= " --qpfile $outFilePath.qp";
			$cmdValsArr[$key].= " --threads $threads $outFilePath.h265";
			$cmdValsArr[$key].= " && $ffmpegExperimBin -i $outFilePath.aac -r $frameRate -i $outFilePath.h265 -c copy -f mp4 -y $outFilePath";
	
			$cmdStr = implode(" ", $cmdValsArr);
		}
		
			/*
			 * VP9 - switch to 'experimental ffmpeg'
			 */
		else if($flavorParamsOutput->videoCodec==KDLVideoTarget::VP9){ //video_codec ||!flavorParamsOutput->videoCodec
			$cmdValsArr = explode(' ', $cmdStr);
			$cmdValsArr[0] = $ffmpegExperimBin;
			$cmdStr = implode(" ", $cmdValsArr);
		}
		return $cmdStr;
	}
	
	/**
	 * 
	 * @param         $wmData
	 * @param string  $destFileSyncLocalPath
	 * @param string  $cmdLine
	 * @param string  $ffmpegBin
	 * @param string  $mediaInfoBin
	 * @return string
	 */
	public static function buildWatermarkedCommandLine($wmData, $destFileSyncLocalPath, $cmdLine, $ffmpegBin = "ffmpeg", $mediaInfoBin = "mediainfo")
	{
		KalturaLog::log("After:cmdline($cmdLine)");
		if(!isset($mediaInfoBin) || strlen($mediaInfoBin)==0)
			$mediaInfoBin = "mediainfo";
			/*
			 * evaluate WM scale and margins, if any
			 */
		KalturaLog::log("Watermark data($mediaInfoBin):\n".print_r($wmData,1));
		if(isset($wmData->scale) && is_string($wmData->scale)) {
			$wmData->scale = explode("x",$wmData->scale);
		}
		if(isset($wmData->margins) && is_string($wmData->margins)) {
			$wmData->margins = explode("x",$wmData->margins);
		}

		KalturaLog::log("Watermark data:\n".print_r($wmData,1));
			/*
		 	 * Retrieve watermark image file,
		 	 * either from image entry or from external url
		 	 * If both set, prefer image entry
		 	 */
		$imageDownloadUrl = null;
		$errStr = null;
		if(isset($wmData->imageEntry)){
			$version = null;
			try {
				$imgEntry = KBatchBase::$kClient->baseEntry->get($wmData->imageEntry, $version);
			}
			catch ( Exception $ex ) {
				$imgEntry = null;
				$errStr = "Exception on retrieval of an image entry($wmData->imageEntry),\nexception:".print_r($ex,1);
			}
			if(isset($imgEntry)){
				KalturaLog::log("Watermark entry: $wmData->imageEntry");
				$imageDownloadUrl = $imgEntry->downloadUrl;
			}
			else if(!isset($errStr)){
				$errStr = "Failed to retrieve an image entry($wmData->imageEntry)";
			}
			if(!isset($imgEntry))
				KalturaLog::err($errStr);
		}
		
		if(!isset($imageDownloadUrl)){
			if(isset($wmData->url)) {
				$imageDownloadUrl = $wmData->url;
			}
			else {
				KalturaLog::err("Missing watermark image data, neither via image-entry-id nor via external url.");
				return null;
			}
		}
		
		$wmTmpFilepath = $destFileSyncLocalPath.".wmtmp";
		KalturaLog::log("imageDownloadUrl($imageDownloadUrl), wmTmpFilepath($wmTmpFilepath)");
			/*
			 * Get the watermark image file
			 */
		$curlWrapper = new KCurlWrapper();
		$res = $curlWrapper->exec($imageDownloadUrl, $wmTmpFilepath);
		KalturaLog::debug("Curl results: $res");
		if(!$res || $curlWrapper->getError()) {
			$errDescription = "Error: " . $curlWrapper->getError();
			$curlWrapper->close();
			KalturaLog::err("Failed to curl the caption file url($imageDownloadUrl). Error ($errDescription)");
			return null;
		}
		$curlWrapper->close();
		
		if(!file_exists($wmTmpFilepath))
		{
			KalturaLog::err("Error: output file ($wmTmpFilepath) doesn't exist");
			return null;
		}
		KalturaLog::log("Successfully retrieved the watermark image file ($wmTmpFilepath) ");
		
			/*
			 * Query the image file for format and dims
			 */
		$output = array();
		exec("$mediaInfoBin  $wmTmpFilepath | grep -i \"width\|height\|format\"",$ouput, $rv);
		if($rv!=0) {
			KalturaLog::err("Failed to retrieve media data from watermark file ($wmTmpFilepath). rv($rv). Carry on without watermark.");
			return null;
		}
		foreach($ouput as $line){
			if(!isset($wmData->format) && stristr($line,"format")!=false) {
				$str = stristr($line,":");
				$wmData->format = strtolower(trim($str,": "));
			}
			else if(stristr($line,"width")!=false) {
				$str = stristr($line,":");
				$wmData->width = (int)trim($str,": ");
			}
			else if(stristr($line,"height")!=false) {
				$str = stristr($line,":");
				$wmData->height = (int)trim($str,": ");
			}
		}
		switch($wmData->format){
		case "jpeg":
			$wmData->format = "jpg";
		case "jpg";
		case "png":
		default:
			rename($wmTmpFilepath, "$wmTmpFilepath.$wmData->format");
			$wmTmpFilepath = "$wmTmpFilepath.$wmData->format";
			break;
		}
		KalturaLog::log("Updated Watermark data:\n".print_r($wmData,1));

			/*
			 * Evaluate WM scaling params and scale it accordingly
			 */
		$wid=null; $hgt=null;
		if(!isset($wmData->scale) && ($wmData->width%2!=0 || $wmData->height%2!=0)){
			$wmData->scale = array();
			$wmData->width -= $wmData->width%2;
			$wmData->height -= $wmData->height%2;
			$wmData->scale[0] = $wmData->width;
			$wmData->scale[1] = $wmData->height;
		}
		if(isset($wmData->scale)){
			$wid = in_array($wmData->scale[0],array(null,0,-1))? -1: $wmData->scale[0];
			$hgt = in_array($wmData->scale[1],array(null,0,-1))? -1: $wmData->scale[1];
			if($wid<=0) {
				$wid = round($hgt*$wmData->width/$wmData->height);
			}
			if($hgt<=0) {
				$hgt = round($wid*$wmData->height/$wmData->width);
			}
			if(!($wid>0 && $hgt>0)) {
				$wid = $wmData->width; $hgt = $wmData->height;
			}
			if($wid>0) $wid -= $wid%2;
			if($hgt>0) $hgt -= $hgt%2;

			$wmFilePath = $wmTmpFilepath;
		}
		else{
			$wmFilePath = $wmTmpFilepath;
			$wid = $wmData->width; $hgt = $wmData->height;
		}
/* Samples - 
"[1]scale=100:100,setsar=100/100[logo];[0:v]crop=100:100:iw-ow-10:300,setsar=100/100[cropped];[cropped][logo]blend=all_expr='if(eq(mod(X,2),mod(Y,2)),A,B)'[blended];[0:v][blended]overlay=main_w-overlay_w-10:300[out]"
"[1]scale=100:100,setsar=100/100[logo];[0:v][logo]overlay=main_w-overlay_w-10:300[out]" -map "[out]"
*/
		$cmdLine = str_replace(
				array(KDLCmdlinePlaceholders::WaterMarkFileName,KDLCmdlinePlaceholders::WaterMarkWidth,KDLCmdlinePlaceholders::WaterMarkHeight), 
				array($wmFilePath, $wid, $hgt),
				$cmdLine);
		KalturaLog::log("After:cmdline($cmdLine)");
		return $cmdLine;
	}
	
	/**
	 * 
	 */
	protected static function buildNGSPairedDigitalWatermarkingCommandLine($cmdLine, $data)
	{
			/*
			 * Get source mediainfo for NGS prepprocessor params
			 * Use default 'NGS_FragmentPreprocessorYUV' if batch config does not contain 'ngsPreprocessorCmd'
			 */
		if(count($data->srcFileSyncs)>0 && isset($data->srcFileSyncs[0]->assetId)) { 
			$mediaInfoFilter = new KalturaMediaInfoFilter();
			$mediaInfoFilter->flavorAssetIdEqual = $data->srcFileSyncs[0]->assetId;
			try {
				$mediaInfoList = KBatchBase::$kClient->mediaInfo->listAction($mediaInfoFilter);
			}
			catch (Exception $ex) {
				$mediaInfoList = null;
				$errStr = "Exception on retrieval of an mediaInfo List ($mediaInfoFilter->flavorAssetIdEqual),\nexception:".print_r($ex,1);
			}

			if(!(isset($mediaInfoList) && isset($mediaInfoList->objects) && count($mediaInfoList->objects)>0)){
				if(!isset($errStr))
					$errStr = "Bad source media info object";
				KalturaLog::err($errStr);
				return null;
			}
			
			$mediaInfo = $mediaInfoList->objects[0];
			$ngsBin = isset(KBatchBase::$taskConfig->params->ngsPreprocessorCmd)? KBatchBase::$taskConfig->params->ngsPreprocessorCmd: "NGS_FragmentPreprocessorYUV";
			$srcWid = $mediaInfo->videoWidth;
			$srcHgt = $mediaInfo->videoHeight;
			$srcFps = $mediaInfo->videoFrameRate;
			if(strstr($data->flavorParamsOutput->tags,KConversionEngineFfmpeg::TAG_VARIANT_A)!=false) 
				$prepMode='A';
			else if(strstr($data->flavorParamsOutput->tags,KConversionEngineFfmpeg::TAG_VARIANT_B)!=false)
				$prepMode='APrime';
			else
				return null;
		}

$stub=null;
	if(strstr($data->flavorParamsOutput->tags,KConversionEngineFfmpeg::TAG_NGS_STUB)!=false)
		$stub="--stub";
	//$digSignStub = "-f rawvideo -pix_fmt $srcPixFmt - | $ngsBin -w $srcWid -h $srcHgt -f $srcFps $stub --$prepMode| $ffmpegBin -f rawvideo -s $srcWidx$srcHgt -r $srcFps -i - -i $srcFile -map 0:v -map 1:a ";
$digSignStub = "-f rawvideo -pix_fmt yuv420p - | %s -w %d -h %d -f %s %s --%s| %s -f rawvideo -s %dx%d -r %s -i -";

		KalturaLog::log("Before:cmdLine($cmdLine)");
		$cmdLineArr = explode(' ', $cmdLine);
		$ffmpegBin = $cmdLineArr[0];
		$kArr = array_keys($cmdLineArr,'-i');
		$srcFile = $cmdLineArr[$kArr[0]+1];
		$is2pass=(count($kArr)>1);
		if($is2pass){
			$keyId = $kArr[1]+1;
		}
		else {
			$keyId = $kArr[0]+1;
		}
		
		$digSignStr = sprintf($digSignStub, $ngsBin, $srcWid, $srcHgt, $srcFps, $stub, $prepMode, $ffmpegBin, $srcWid, $srcHgt, $srcFps, $srcFile);
		$kArr = array_keys($cmdLineArr,'-an');
		if(count($kArr)==0 || ($is2pass && count($kArr)==1)) {
			$digSignStr.= " -i $srcFile -map 0:v -map 1:a";
		}
		KalturaLog::log("Fixed part:$digSignStr");
		$cmdLineArr[$keyId].= " $digSignStr";
		$cmdLine = implode(" ", $cmdLineArr);
		
		KalturaLog::log("After:cmdLine($cmdLine)");
		return $cmdLine;
	}


}

