<?php
/**
 * @package plugins.ffmpeg
 * @subpackage lib
 */

	/**
	 * 
	 * KDLOperatorFfmpeg2_7_2
	 *
	 */
class KDLOperatorFfmpeg2_7_2 extends KDLOperatorFfmpeg2_2 {
	
	/* ---------------------------
	 * generateSinglePassCommandLine
	 */
	public function generateSinglePassCommandLine(KDLFlavor $design, KDLFlavor $target, $extra=null)
	{
		$cmdStr = parent::generateSinglePassCommandLine($design, $target, $extra);
		if(!isset($cmdStr)) 
			return null;
		
		if(isset($target->_decryptionKey)){
			$cmdStr = "-decryption_key $target->_decryptionKey $cmdStr";
		}
		// PLAT-9429 - allow 2 enc modes
		if(isset($target->_isEncrypted) && $target->_isEncrypted>0) {
				// Add key & key_if placeholder. To be replaced by real values after asset creation
			$str = " -encryption_scheme cenc-aes-ctr";
			$str.= " -encryption_key ".KDLFlavor::ENCRYPTION_KEY_PLACEHOLDER;
			$str.= " -encryption_kid ".KDLFlavor::ENCRYPTION_KEY_ID_PLACEHOLDER." -y ";
			$cmdStr = str_replace(" -y ", $str, $cmdStr);
			KalturaLog::log("On Encryption: $cmdStr");
		}

		return $cmdStr;
	}
		
	/* ---------------------------
	 * processTwoPass
	 */
    protected function processTwoPass(KDLFlavor $target, $cmdStr)
	{
		if(!isset($target->_isTwoPass) || $target->_isTwoPass==0)
			return $cmdStr;

		if($target->_video->_id!=KDLVideoTarget::H265) {
			return parent::processTwoPass($target, $cmdStr);
		}
$nullDev = "NUL";
$nullDev ="/dev/null";
		$statsLogFile = KDLCmdlinePlaceholders::OutFileName.".2pass.log";
		$x265PassParams = "stats=$statsLogFile:pass";
		
		$cmdValsArr = explode(' ', $cmdStr);

		$outFileNameKey=array_search(KDLCmdlinePlaceholders::OutFileName, $cmdValsArr);
		$cmdValsArr[$outFileNameKey] = "-an $nullDev";
		
		if(($x265ParamsKey=array_search('-x265-params', $cmdValsArr))!==false){
			$x265PassParams = $cmdValsArr[$x265ParamsKey+1].":$x265PassParams";
			$cmdValsArr[$x265ParamsKey+1] = "$x265PassParams=1:slow-firstpass=0";
		}
		else {
			$cmdValsArr[$outFileNameKey] = "-x265-params $x265PassParams=1:slow-firstpass=0 ".$cmdValsArr[$outFileNameKey];
		}
		$pass1cmdLine = implode(' ', $cmdValsArr);
		
		if($x265ParamsKey!==false) {
			$cmdValsArr[$x265ParamsKey+1] = "$x265PassParams=2";
			$cmdValsArr[$outFileNameKey] = KDLCmdlinePlaceholders::OutFileName;
		}
		else {
			$cmdValsArr[$outFileNameKey] = "-x265-params $x265PassParams=2 ".KDLCmdlinePlaceholders::OutFileName;
		}
		$pass2cmdLine = implode(' ', $cmdValsArr);

		$cmdStr = "$pass1cmdLine && ".KDLCmdlinePlaceholders::BinaryName." $pass2cmdLine";
		return $cmdStr;
	}
	
	/* ---------------------------
	 * generateAudioParams
	 */
	protected function generateAudioParams(KDLFlavor $design, KDLFlavor $target)
	{
		$cmdStr = parent::generateAudioParams($design, $target);
		if(isset($target->_audio)) {
			$cmdValsArr = explode(' ', $cmdStr);
			if(($key=array_search('-channel_layout', $cmdValsArr))!==false){
				$cmdValsArr[$key+1].= "c";
				$cmdStr = implode(" ", $cmdValsArr);
			}
		}
		return $cmdStr;
	}
	
	/**
	 * generateVideoFilters
	 * @param $vid
	 * @return array of filters 
	 */
	protected static function generateVideoFilters($vid)
	{
		/*
		 * FFMpeg 2.7 automatically rotates the output 
		 * into 'non-rotated' orientation. No need to do it explicitly 
		 */
		$rotation = null;
		if(isset($vid->_rotation)) {
			$rotation = $vid->_rotation;
			$vid->_rotation = null;
		}
		$filters = parent::generateVideoFilters($vid);
		$vid->_rotation = $rotation;
		return $filters;
	}

	/* ---------------------------
	 * getVideoCodecSpecificParams
	 */
	protected function getVideoCodecSpecificParams(KDLFlavor $design, KDLFlavor $target)
	{
		switch ($target->_video->_id){
			case  KDLVideoTarget::VP8:
				/*
				 * There is some quality degradation on old-style VP8 cmd line.
				 * 'qmax=8' fixes it. 
				 */
				$vidCodecSpecStr = "libvpx -quality good -cpu-used 0 -qmin 10";
				break;
			case  KDLVideoTarget::VP9:
				$vidCodecSpecStr = "libvpx-vp9".$this->calcForcedKeyFrames($target->_video, $target);
				break;
			case  KDLVideoTarget::H265:
				$vidCodecSpecStr = "libx265".$this->calcForcedKeyFrames($target->_video, $target);
				$vidCodecSpecStr.= " -x265-params min-keyint=1";
				if(isset($target->_video->_gop) && $target->_video->_gop>0){
					$vidCodecSpecStr.= ":keyint=".$target->_video->_gop;
				}
				break;
			default:
				$vidCodecSpecStr = parent::getVideoCodecSpecificParams($design, $target);
				break;
		}
		
		return $vidCodecSpecStr;
	}

	/**
	 *
	 * @param unknown_type $targetVid
	 * @return string
	 */
	protected static function generateWatermarkParams($targetVid, $vidIn)
	{
		/*
		 * FFMpeg 2.7 automatically rotates the output 
		 * into 'non-rotated' orientation. No need to do it explicitly 
		 */
$rotation = null;
		if(isset($targetVid->_rotation)) {
			$rotation = $targetVid->_rotation;
			$targetVid->_rotation = null;
		}
		$watermarkStr = parent::generateWatermarkParams($targetVid, $vidIn);
		$targetVid->_rotation = $rotation;
		return $watermarkStr;
	}

	/**
	 * 
	 * @param unknown_type $targetVid
	 * @param array $cmdValsArr
	 */
	protected static function adjustVideoCodecSpecificParams($targetVid, array &$cmdValsArr)
	{
		if(isset($targetVid) && ($targetVid->_id==KDLVideoTarget::H265)){
			$keys=array_keys($cmdValsArr, "-x265-params");
			if(count($keys)==1) {
				$cmdValsArr[$keys[0]+1].=":pools=4:aq-mode=3:bframes=4:ref=2:limit-refs=3:rc-lookahead=15:subme=2";
			}
			else
				self::mergeOpts("-x265-params", $cmdValsArr);
		}
		else 
			parent::adjustVideoCodecSpecificParams($targetVid, $cmdValsArr);
	}
	
	/* ---------------------------
	 * CheckConstraints
	 */
	public function CheckConstraints(KDLMediaDataSet $source, KDLFlavor $target, array &$errors=null, array &$warnings=null)
	{
		$isEncrypted = $target->_isEncrypted;
		if($target->_isEncrypted==true) {
			$target->_isEncrypted = false;
		}
		$rv = parent::CheckConstraints($source, $target, $errors, $warnings);
		$target->_isEncrypted = $isEncrypted;
		return $rv;
	}
}

	/**
	 * 
	 * KDLOperatorFfmpeg4_4
	 *
	 */
class KDLOperatorFfmpeg4_4 extends KDLOperatorFfmpeg2_7_2 {
	/* ---------------------------
	 * getVideoCodecSpecificParams
	 */
	protected function getVideoCodecSpecificParams(KDLFlavor $design, KDLFlavor $target)
	{
		switch ($target->_video->_id){
			case  KDLVideoTarget::AV1:
				$vidCodecSpecStr = "libaom-av1 -tag:v av01".$this->calcForcedKeyFrames($target->_video, $target);
				break;
			default:
				$vidCodecSpecStr = parent::getVideoCodecSpecificParams($design, $target);
				break;
		}
		
		return $vidCodecSpecStr;
	}
	
	/* ---------------------------
	 * generateSinglePassCommandLine
	 */
	public function generateSinglePassCommandLine(KDLFlavor $design, KDLFlavor $target, $extra=null)
	{
		$cmdStr = parent::generateSinglePassCommandLine($design, $target, $extra);
		$cmdValsArr = explode(' ', $cmdStr);

			// Retrieve the aud & vid filter graphs. they need to adjusted to the 'named' filters concept.
			// see the remark bellow
		$audioFiltersPattern = '/\b(' . implode('|', array_map('preg_quote', KDLConstants::$SupportedAudioFilters)) . ')\b/';
		$keyAudFilters = null;
		$videoFiltersPattern = '/\b(' . implode('|', array_map('preg_quote', KDLConstants::$SupportedVideoFilters)) . ')\b/';
		$keyVidFilters = null;

		$filterGraphKeys = array_keys($cmdValsArr, '-filter_complex');
		foreach ($filterGraphKeys as $flGrKey){
			if(preg_match($audioFiltersPattern, $cmdValsArr[$flGrKey+1], $matched)){
				KalturaLog::log("key:$flGrKey, ".$matched[0]." ==> ".$cmdValsArr[$flGrKey+1]);
				$keyAudFilters = $flGrKey+1;
			}
			else if(preg_match($videoFiltersPattern, $cmdValsArr[$flGrKey+1], $matched)){
				KalturaLog::log("key:$flGrKey, ".$matched[0]." ==> ".$cmdValsArr[$flGrKey+1]);
				$keyVidFilters = $flGrKey+1;
			}
		}

		if(isset($target->_forGenericSource) && $target->_forGenericSource==true) {
			$target->multyAudioMapping = null;
			$generalAudMapping = 'a';
		}
		else $generalAudMapping = 'a:0';
		
		$mappingStr = null;
		/*
		 * Following code adapts the FFM4.4 and earlier) cmdLines to FFM6 standard.
		 * FFM6 'resolves' the ambiguity in previous versions while using both 
		 * the filter_complex and stream mappings. 
		 * Starting with FFM6 the streams that are used by the filter_complex are 
		 * 'mapped' automatically. When in this case explicit '-map' is used as well, 
		 * it is ADDED to the filter_complex 'mapped' streams, resulting converted 
		 * streams duplication. FFM4 and older versions don't duplicate the 
		 * transcodided streams.
		 * The solution is to use 'named' filter graphs out connections and explicitly 
		 * to map them, and to avoid mapping of the source streams.
		 *
		 * Another task is to force the output vid stream as the 1st stream, 
		 * and the aud as the 2nd. It is needed for the multy-entry-edit feature.
		 * 
		 * !!! IMPORTANT !!!
		 *		This syntax is fully compatibale with the FFM4.4 as well.
		 */
		 
			// both aud & vid filters are used ==> 
			//  add aout/vout filter out connections and their mappings
		if(isset($keyAudFilters) && isset($keyVidFilters)) {
			$filterStr = trim($cmdValsArr[$keyVidFilters],"'");
			$cmdStr = str_replace($filterStr, $filterStr.'[vout]', $cmdStr);
			$filterStr = trim($cmdValsArr[$keyAudFilters],"'");
			$cmdStr = str_replace($filterStr, $filterStr.'[aout]', $cmdStr);
			$mappingStr = '-map \'[vout]\' -map \'[aout]\'';
		}
			// only vid filters are used ==> 
			//   add vout connection to the vidoe filter graph 
			//   and map the 'a:0' or the multi audio mappings
		else if(isset($keyVidFilters)) {
			$filterStr = trim($cmdValsArr[$keyVidFilters],"'");
			$cmdStr = str_replace($filterStr, $filterStr.'[vout]', $cmdStr);
			$mappingStr = '-map \'[vout]\' ';
			if(isset($target->multyAudioMapping)) 
				$mappingStr.= implode(' ', $target->multyAudioMapping);
			else $mappingStr.= "-map $generalAudMapping";
		}
			// only aud filters are used ==> 
			//   add aout connection to the audio filter graph and map the 'v:0' 
		else if(isset($keyAudFilters)) {
			$filterStr = trim($cmdValsArr[$keyAudFilters],"'");
			$cmdStr = str_replace($filterStr, $filterStr.'[aout]', $cmdStr);
			$mappingStr = '-map v:0 -map \'[aout]\'';
		}
			// if no filter graphs - add multyAudioMapping if one exists
		else if(isset($target->multyAudioMapping)) {
			$mappingStr = implode(' ', $target->multyAudioMapping);
		}
			// generation of Generic source flv, requires mapping of ALL source streams
		else if(isset($target->_forGenericSource) && $target->_forGenericSource==true) {
			$mappingStr = '-map v:0 -map a';
		}
		$cmdStr = str_replace(" -y", " $mappingStr -y", $cmdStr);

			// switch the vsync mode to textual name
		$cmdStr = preg_replace('/\s+/', ' ', $cmdStr);
		$pattern = "/-vsync\s+(\S+)/";
		$vsyncModeMapping = [0=>"passthrough",1=>"cfr",2=>"vfr",-1=>"auto"];
		if (preg_match($pattern, $cmdStr, $matches)) {
			$vsyncMode = $matches[1];
			KalturaLog::log("Next word after 'pattern': $vsyncMode");
			if (key_exists($vsyncMode, $vsyncModeMapping)) {
				$cmdStr=str_replace("-vsync $vsyncMode", "-vsync ".$vsyncModeMapping[$vsyncMode],$cmdStr);
			}
		} 
		KalturaLog::log($cmdStr);
		return $cmdStr;
	}
	
	/**
	 * 
	 * @param string $cmdStr
	 */
	protected static function rearrngeAudioFilters($target, array &$cmdValsArr)
	{
		KalturaLog::log("in cmdLine - ".implode(' ', $cmdValsArr));
		if(!isset($target->_audio)){
			return false;
		}
		
		if(isset($target->_multiStream->audio)){
			$multiStreamAudio = $target->_multiStream->audio;
			if(isset($multiStreamAudio->action) && $multiStreamAudio->action=='separate'){
				return false;
			}
			$mapping = $multiStreamAudio->getStreamMapping();
			if(isset($mapping) && count($mapping)==1){
				return false;
			}
		}
		
		/*	
		 * Switch the '-async' to 'resample=asyn=...' filter, to follow ffmpeg's runtime notification.
		 * Since it is a filter, it should be merged into the same graph with other audio filters
		 */
		$key=array_search("-async", $cmdValsArr);
		if($key!==false) {
			$cmdValsArr[$key+1]=' ';	// unset($cmdValsArr[$key+1]);
			$cmdValsArr[$key]=' ';		// unset($cmdValsArr[$key]);
			$switchAsyncToResaample = true;
		}
		else $switchAsyncToResaample = false;

		$filterGraphKeys = array_keys($cmdValsArr, '-filter_complex');

			// Scan 'filter_complex' commands for audio filter graphs
		$keyAudFilters = array();
		$supportedAudioFilters  = KDLConstants::$SupportedAudioFilters; //array("amerge","amix","pan");
		$pattern = '/\b(' . implode('|', array_map('preg_quote', $supportedAudioFilters)) . ')\b/';
		foreach ($filterGraphKeys as $flGrKey){
			$filters = explode(';', trim($cmdValsArr[$flGrKey+1],"'\""));
			foreach ($filters as $fIdx=>$filter){
				if(preg_match($pattern, $filter, $matched)){
					if(isset($target->_forGenericSource) && $target->_forGenericSource==true) {
						if(strstr($filter,'aresample=')!==false)
							$switchAsyncToResaample=false;
					}
					KalturaLog::log("key($flGrKey)-".print_r($matched,1));
					$keyAudFilters[] = $flGrKey+1;
				}
			}			
		}
			// 'async' opcode should be swurched to aresample
		if($switchAsyncToResaample===true) {
			$asyncStr="async=1:min_hard_comp=0.100000:first_pts=0";
			if(count($keyAudFilters)==0){
				$key=array_search("-c:a", $cmdValsArr);
				if($key!==false) {
					array_splice($cmdValsArr,$key+2,0,array("-filter_complex","'aresample=$asyncStr'"));
					$keyAudFilters[] = $key+3;
				}
			}
			else {
				$key = end($keyAudFilters);
				array_splice($cmdValsArr,$key+2,0,array("-filter_complex","'aresample=$asyncStr'"));
				$keyAudFilters[] = $key+3;
			}
		}
			// merge the all audio filter graphs, if there are more than 1
		if (count($keyAudFilters) > 1)
			self::mergeFilterCommands($cmdValsArr, $keyAudFilters, null, "aflt");
		
		if(count($keyAudFilters) > 0)
			$keyAudFilters = $keyAudFilters[0];
		KalturaLog::log("out cmdLine - ".implode(' ', $cmdValsArr));
		return true;
	}

	/**
	 * the following function merges video filters(filter complex) into one filter  as ffmpeg does not support multiple filters
	 * NOTE!!!! for mxf file types the fade effect will cause the screen to get black this is a known issue
	 * @param $target
	 * @param array $cmdValsArr
	 * @return bool
	 */
	protected static function rearrngeVideoFilters($target, array &$cmdValsArr)
	{
		if(!isset($target->_video))
			return false;

		$keys = array_keys($cmdValsArr, "-filter_complex");
		$videoFilterKeys = array();
		$count = 0;
		$supportedVideoFilters = KDLConstants::$SupportedVideoFilters;
		$pattern = '/\b(' . implode('|', array_map('preg_quote', $supportedVideoFilters)) . ')\b/';
		foreach ($keys as $key)
		{
			$filter = trim($cmdValsArr[$key+1]);
			/**
			 * Note we are looking for a fade substing -> video filter , however, afade -> audio filter contains fade,
			 * as such we are checking that afade does not exist in the filter, but fade exist
			 * we are under the assumption that filter complex element does not contains both audio and video filter!
			 */
			if (preg_match($pattern, $filter, $matched)) {
				$videoFilterKeys[] = $key+1;
				$count = $count + ceil (substr_count($filter,'vflt') / 2);
			}
		}
		if (count($videoFilterKeys) > 1)
			self::mergeFilterLines($cmdValsArr, $videoFilterKeys, $count);
		if(count($videoFilterKeys) > 0)
			$target->keyVidFilters = $videoFilterKeys[0];
		return true;
	}

	/**
	 * @param array $cmdValsArr
	 * @param $videoFilterKeys
	 * @param $count
	 */
	protected static function mergeFilterCommands(array &$cmdValsArr, $filterKeys, $count, $conName="vflt")
	{
		foreach($filterKeys as $key){
			KalturaLog::log("$key-".$cmdValsArr[$key]);
		}

		if($count===null)
			$count = -1;
		$mergedFilter = substr($cmdValsArr[$filterKeys[0]], 0, -1);
		KalturaLog::log("mergedFilter:$mergedFilter,".print_r($filterKeys,1));
		for ($i = 1; $i < count($filterKeys); $i++)
		{
			$toMerge = substr($cmdValsArr[$filterKeys[$i]], 1, -1);
			KalturaLog::log("toMerge($i):$toMerge");
			$count = $count + 1;
			$mergedFilter .= "[$conName$count]" . ';' . "[$conName$count]" . $toMerge;
			KalturaLog::log("mergedFilter:$mergedFilter");
		}
		$mergedFilter = "'".trim($mergedFilter,"'\"")."'";
		KalturaLog::log("mergedFilter:$mergedFilter");
		$cmdValsArr[$filterKeys[0]] = $mergedFilter;
		for ($i = 1; $i < count($filterKeys); $i++)
		{
			unset($cmdValsArr[$filterKeys[$i]]);
			unset($cmdValsArr[$filterKeys[$i] - 1]);
		}
	}
	protected static function mergeVideoFilterLines(array &$cmdValsArr, $videoFilterKeys, $count)
	{
		return $this->mergeFilterCommands($cmdValsArr, $videoFilterKeys, null, "vflt");
	}
	
	/**
	 * 
	 * @param KDLFlavor $target
	 * @param array $cmdValsArr
	 */
	protected function getMappingsForMultiStream(KDLFlavor $target, array &$cmdValsArr)
	{
		KalturaLog::log("in --".implode(' ', $cmdValsArr));

		if(!isset($target->_audio))
			return;

		/*
		 * On multi-lingual, add:
		 * - explicit mapping for video (if required)
		 * - the required audio channels
		 */
		if(isset($target->_multiStream->audio->streams) && count($target->_multiStream->audio->streams)>0){
			$auxArr = array();

			// Add language prop to the mapped output audio streams
			$auxIdx = 0;
			foreach ($target->_multiStream->audio->streams as $stream){
				$streamMapping = $stream->getMapping();
				foreach ($streamMapping as $m) {
					$target->multyAudioMapping[] = "-map 0:$m";
				}
				if(isset($stream->lang)){
					$auxArr[] = "-metadata:s:a:$auxIdx language=$stream->lang";
				}
				if(isset($stream->channels)){
					foreach ($stream->channels as $strId=>$chanIds) {
						foreach ($chanIds as $chanId) {
							$auxArr[] ="-map_channel 0.$strId.$chanId";
						}
					}
				}
				$auxIdx++;
			}
			$insertHere = array_search('-y', $cmdValsArr);
			array_splice ($cmdValsArr, $insertHere, 0, $auxArr);
		}
	}
}

	/**
	 * 
	 * KDLOperatorFfmpegMain
	 *
	 */
class KDLOperatorFfmpegMain extends KDLOperatorFfmpeg4_4 {}

	/**
	 * 
	 * KDLOperatorFfmpegAux
	 *
	 */
class KDLOperatorFfmpegAux extends KDLOperatorFfmpeg4_4 {}

