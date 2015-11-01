<?php
/**
 * @package plugins.ffmpeg
 * @subpackage lib
 */
class KDLOperatorFfmpeg2_1_3 extends KDLOperatorFfmpeg1_1_1 {

	/* ---------------------------
	 * generateSinglePassCommandLine
	 */
    public function generateSinglePassCommandLine(KDLFlavor $design, KDLFlavor $target, $extra=null)
	{
		$cmdStr = parent::generateSinglePassCommandLine($design, $target, $extra);
		if(isset($target->_video) && isset($target->_video->_watermarkData)) {
				// Fading requires looping of the WM image
			if(isset($target->_video->_watermarkData->fade)){
				$loopTime = 0;
				/*
				 * The loop time should be minimum of the video duration (_explicitClipDur) and 
				 * the calculated largest fade time.
				 * Otherwise set loop time to 60 sec
				 */
				if(isset($target->_video->_watermarkFadeLoopTime) && $target->_video->_watermarkFadeLoopTime)
					$loopTime = $target->_video->_watermarkFadeLoopTime + 0.5;
				if(isset($target->_explicitClipDur) && $target->_explicitClipDur>0){
					if($loopTime>0) 
						$loopTime = min($loopTime, round($target->_explicitClipDur/1000));
					else $loopTime = round($target->_explicitClipDur/1000);
				}
				if($loopTime==0)
					$loopTime = 60;
					
				$loopStr = "-loop 1 -t $loopTime";
			}
			else 
				$loopStr = null;
			$cmdStr = str_replace(KDLCmdlinePlaceholders::InFileName, 
					KDLCmdlinePlaceholders::InFileName." $loopStr -i ".KDLCmdlinePlaceholders::WaterMarkFileName,
					$cmdStr);
		}
		
		$cmdValsArr = explode(' ', $cmdStr);
		if(self::rearrangeFiltersAndOpts($cmdValsArr)){
			$cmdStr = implode(" ", $cmdValsArr);
		}

		KalturaLog::log("CmdLine==>".$cmdStr);
		return $cmdStr;
	}
	
	/* ---------------------------
	 * processClipping
	 */
	protected function processClipping(KDLFlavor $target, $cmdStr)
	{

		$startStr=null;
		if(isset($target->_clipStart) && $target->_clipStart>0){
			$startStr.= " -ss ".$target->_clipStart/1000;
		}
		
		$durStr = null;
		if(isset($target->_clipDur) && $target->_clipDur>0){
			/*
			 * For low audio resample-filter use case (low sr source audio), 
			 * express the clipping duration in vid/aud frames, rather than in seconds
			 */
			if(isset($target->_audio->_useResampleFilter) && $target->_audio->_useResampleFilter==true){
				if(isset($target->_video->_frameRate) && $target->_video->_frameRate>0){
					$aux = round($target->_video->_frameRate*$target->_clipDur/1000,0,PHP_ROUND_HALF_UP);
					$durStr.= " -frames:v $aux";
				}
				if(isset($target->_audio->_sampleRate) && $target->_audio->_sampleRate>0){
					$aux = round($target->_clipDur*$target->_audio->_sampleRate/(1000*1024),0,PHP_ROUND_HALF_UP);
					$durStr.= " -frames:a $aux";
				}
			}
				/*
				 * If for some reason dur-in-frames cannot be calculated, 
				 * or this is not a resample use-case 
				 * ==> go for clip timing in secs
				 */
			if(!isset($durStr)) {
				$durStr.= " -t ".$target->_clipDur/1000;
			}
		}
		
		if(!(isset($startStr) || isset($durStr)))
			return $cmdStr;
		
				/*
				 * In 'fastSeekTo' mode, reposition on input stream (-ss placed before the source)
				 * and add dummy '-ss 0.01' on output stream
				 * Otherwise - -ss on output stream
				 */
		if($target->_fastSeekTo==true){
			$cmdStr = $startStr.$cmdStr ;
			if(isset($startStr) && !$target->_container->IsFormatOf(array(KDLContainerTarget::WEBM))){
				$durStr.= ' -ss 0.01';
			}
		}
		else {
			$cmdStr.= $startStr ;
		}

		$cmdStr.= $durStr;
		
		return $cmdStr;
	}
	
	/* ---------------------------
	 * generateAudioParams
	 */
	protected function generateAudioParams(KDLFlavor $design, KDLFlavor $target)
	{
		$cmdStr = parent::generateAudioParams($design, $target);
		if(!isset($target->_audio))
			return $cmdStr;

			/*
			 * Handle audio multi stream 
			 */
		$filterStr = null;
		if(isset($target->_multiStream)){
			$filterStr = self::getMultiStream($target->_multiStream);
		}
		else if(isset($target->_audio->_downmix)){
			$filterStr = "pan=stereo:c0=c0:c1=c1";
		}
		
		$cmdValsArr = null;
			/*
			 * Switch the 'ar' setting to 'aresample' filter, for cases that require it.
			 * - Remove the 'ar' setting
			 * - Add 'aresample' to the end of the audio cmd operands
			 * - If multiStream exists - merge all together.
			 */
		if(isset($target->_audio->_useResampleFilter) && $target->_audio->_useResampleFilter==true){
			$cmdValsArr = explode(' ', $cmdStr);
			
			$key=array_search("-ar", $cmdValsArr);
			if($key!==false) {
				$resampleStr = "aresample=".$cmdValsArr[$key+1];
					// For mutiStream cases, concatenate with reample filter
				if(isset($filterStr)) {
					$filterStr .= "[amerged];[amerged]$resampleStr";
				}
				else {
					$filterStr = $resampleStr;
				}
				unset($cmdValsArr[$key+1]);
				unset($cmdValsArr[$key]);
			}
		}

		if(isset($filterStr)) {
			$cmdValsArr = isset($cmdValsArr)? $cmdValsArr: explode(' ', $cmdStr);
			$filterStr = "'$filterStr'";
			$cmdValsArr[] = "-filter_complex";
			$cmdValsArr[] = $filterStr;
			$cmdStr = implode(" ", $cmdValsArr);
		}
		return $cmdStr;
	}
	
	/* ---------------------------
	 * generateVideoParams
	 */
	protected function generateVideoParams(KDLFlavor $design, KDLFlavor $target)
	{
		$cmdStr = parent::generateVideoParams($design, $target);
		if(!isset($target->_video) || !isset($target->_video->_watermarkData))
			return $cmdStr;

		$vid = $target->_video;
		$watermarkStr = $this->generateWatermarkParams($vid);

		$cmdValsArr = explode(' ', $cmdStr);
			
		$key=array_search("-vf", $cmdValsArr);
		if($key!==false) {
			$filters = $this->generateVideoFilters($vid);
			if(isset($filters) && count($filters)>0){
				$filtersStr = array_shift($filters);
				foreach($filters as $i=>$filter){
					$filtersStr.="[vflt$i];[vflt$i]$filter";
				}
			}
			unset($cmdValsArr[$key+1]);
			unset($cmdValsArr[$key]);
			$cmdStr = implode(' ', $cmdValsArr);
		}
		
		$mergedStr = null;
		if(isset($watermarkStr)) {
			$mergedStr = $watermarkStr;
			if(isset($filtersStr))
				$mergedStr.="[wm];[wm]$filtersStr";
		}
		else if(isset($filtersStr))
			$mergedStr = $filtersStr;
		
		if(isset($mergedStr))
			$cmdStr.= " -filter_complex '$mergedStr'";

		return $cmdStr;
	}
	
	/* ---------------------------
	 * getVideoCodecSpecificParams
	 */
	protected function getVideoCodecSpecificParams(KDLFlavor $design, KDLFlavor $target)
	{
		if($target->_video->_id==KDLVideoTarget::VP9) {
			return "libvpx-vp9";
		}
		return parent::getVideoCodecSpecificParams($design, $target);
	}
	
	/* ---------------------------
	 * generateContainerParams
	 */
	protected function generateContainerParams(KDLFlavor $design, KDLFlavor $target)
	{
		if(!isset($target->_container))
			return null;
		
		$con = $target->_container;
		if($con->_id==KDLContainerTarget::HLS){
			$cmdStr = " -hls_list_size 100000 -hls_time 10 -f hls";
			return $cmdStr;
		}

		$cmdStr = parent::generateContainerParams($design, $target);
		if(!isset($target->_container))
			return $cmdStr;
		
		/*
		 * On multi-lingual, add:
		 * - explicit mapping for video (if required) 
		 * - the required audio channels 
		 */
		if(isset($target->_audio) && isset($target->_multiStream) && isset($target->_multiStream->audio)
		&& isset($target->_multiStream->audio->languages) && count($target->_multiStream->audio->languages)>0){
			if(isset($target->_video)) {
				$cmdStr.= " -map v";
			}
				// Add language prop to the mapped output audio streams
			$langIdx = 0;
			foreach ($target->_multiStream->audio->languages as $lang){
				$cmdStr.= " -map 0:".$lang->id;
				$cmdStr.= " -metadata:s:a:$langIdx language=$lang->audioLanguage";
				$langIdx++;
			}
		}
		
		if(in_array($target->_container->_id, array(KDLContainerTarget::MKV,KDLContainerTarget::WEBM))){
			$cmdStr.= " -sn";
		}
		return $cmdStr;
	}

	/**
	 * 
	 * @param array $cmdValsArr
	 * @param unknown_type $multiStream
	 */
	protected static function getMultiStream($multiStream)
	{
		if(!(isset($multiStream->audio) && isset($multiStream->audio->mapping)))
			return null;
		$mapping = $multiStream->audio->mapping;
		$mapStr = null;
		foreach($mapping as $m){
			$mapStr.="[0:$m]";
		}
		if(!isset($mapStr))
			return null;
		if(count($mapping)==1) {
			$mapStr = $mapStr."pan=stereo:c0=c0:c1=c1";
		}
		else {
			$mapStr = $mapStr."amix=inputs=".count($mapping);
		}
		return $mapStr;
	}
	
	/**
	 * 
	 * @param string $cmdStr
	 */
	protected static function rearrangeFiltersAndOpts(array &$cmdValsArr)
	{
		$reImplode = false;
		$keys=array_keys($cmdValsArr, "-x264opts");
		if(count($keys>1)) {
			$first = array_shift($keys);
			$x264opts = $cmdValsArr[$first+1];
			foreach ($keys as $key){
				$x264opts.= ':'.$cmdValsArr[$key+1];
			}
			$cmdValsArr[$first+1] = $x264opts;
			
			$keys  = array_reverse($keys);
			foreach ($keys as $key){
				unset($cmdValsArr[$key+1]);
				unset($cmdValsArr[$key]);
			}
			$reImplode = true;
		}

		/*
		 * For resample-filter case -
		* 'async 2' causes aud-br distortion ==> set to 'async 1'
		*/
		if(self::rearrngeAudioFilters($cmdValsArr)==true) {
			$reImplode = true;
		}
		
		
		return $reImplode;
	}
	
	/**
	 * 
	 * @param string $cmdStr
	 */
	protected static function rearrngeAudioFilters(array &$cmdValsArr)
	{
		/*	
		 * Switch the '-async' to 'resample=asyn=...' filter, to follow ffmpeg's runtime notification.
		 * Since it is a filter, it should be merged intoi the same graph with other audio filters
		 */
		$key=array_search("-async", $cmdValsArr);
		if($key===false)
		{
			return false;
		}
			
		unset($cmdValsArr[$key+1]);
		unset($cmdValsArr[$key]);
		
		$keys=array_keys($cmdValsArr, "-filter_complex");
		$resampleStr = false;
		$keyAudFilters = null;
		$keyResampleFilter = null;
		foreach ($keys as $key){
			$filters = explode(';', trim($cmdValsArr[$key+1],"'"));
			foreach ($filters as $f=>$filter){
				$resampleStr=strstr($filter,'aresample=');
				if($resampleStr!=false){
					$keyResampleFilter = $f;
					$keyAudFilters = $key+1;
					break;
				}
				else if(strstr($filter,'amerge')!=false || strstr($filter,'amix')!=false || strstr($filter,'pan')!=false){
					$keyAudFilters = $key+1;
				}
			}
			if($resampleStr!=false || isset($keyAudFilters)){
				break;
			}
		}
		
		$asyncStr="async=1:min_hard_comp=0.100000:first_pts=0";
		if(!isset($keyAudFilters)){
			$key=array_search("-c:a", $cmdValsArr);
			if($key!==false) {
				array_splice($cmdValsArr,$key+2,0,array("-filter_complex","'aresample=$asyncStr'"));
			}
		}
		else {
			if($resampleStr!==false){
				$filters[$keyResampleFilter].=":$asyncStr";
			}
			else {
				$str = $filters[count($filters)-1];
				$filters[count($filters)-1] = $str.'[aflt];[aflt]aresample='.$asyncStr;
			}
			$str = implode(';',$filters);
			$cmdValsArr[$keyAudFilters] = "'$str'";
		}
		return true;
	}

	/**
	 * 
	 * @param unknown_type $targetVid
	 * @param unknown_type $wmWid
	 * @param unknown_type $wmHgt
	 * @return string
	 */
	protected static function generateWatermarkParams($targetVid, $wmWid=KDLCmdlinePlaceholders::WaterMarkWidth, $wmHgt=KDLCmdlinePlaceholders::WaterMarkHeight)
	{
/*
	Watermark Data
	- imageEntry (optional, either 'imageEntry or 'url' must be provided)
		An image entry that will be used as a watermark image. 
		Supported - PNG and JPG. Transparent alpha layer (PNG only) is supported.
	?- url (optional, either 'imageEntry or 'url' must be provided)
		External url for the watermark image file. 
		Formats same as above.
	- margins (optional)
		'WxH', distance from the video frame borders. 
		Positive numbers refer to LeftUp corner, negative to RightDown corner of the video frame.
		'center' allowed to place the WM relatively to the center of the image. Offset allowed
		If omitted - LeftUp is assumed. 
		Example - 
			'-100x10'- 100pix from right side, 10 pixs from the upper side
			'center-10xcenter+30' - place the WM 10pix left to the middle and 30pix bellow the middle 
	- opacity (optional) 
		0-1.0 range. Defines the blending level between the watermark image and the video frame. 
		If omitted the watermark is presented un-blended.
	- scale (optional)
		'WxH' - scale the water mark image to the given size. If one of the dimensions is omitted, 
		it is calculated to preserve the watermark image aspect ratio.
		'n%' to scale to n% of the source size. 
		Example
			'x30%' - scale to 30% of the source height. Calc the width to match the aspect ratio
	- fade (optional)
		Several fade in/outs are allowed
		-- type - in/out, default 'in'
		-- start_time - in sec (flaot), default 0
		-- alpha - If set to 1, fade only alpha channel, if one exists on the input. default 0 
		-- duration - in sec, default ~1sec.
		
	Sample Json string - 
		{"imageEntry":"0_abcd1234", "margins":"centerxcenter","scale":"0x100%",
		"fade":[{"type":"in","start_time":"0.3","alpha":"1","duration":"0.5"},
				{"type":"out","start_time":"10","alpha":"1","duration":"0.6"}]}
		
*/
		$watermarkStr = null;
		if(isset($targetVid->_rotation)){
			switch ($targetVid->_rotation){
				case 90:
					$prepArr[]="transpose=1";
				case 180:
					$prepArr[]="transpose=1";
				case 270:
				case -90:
					$prepArr[]="transpose=1";
			}
		}

			// Don't change the passed WM arg, clone it!!!
		$wmAux = clone($targetVid->_watermarkData);

		KalturaLog::log("Watermark data:\n".print_r($wmAux,1));
			// Scaling
		if(isset($wmAux->scale)) {
			$wmAux->scale = explode("x",$wmAux->scale);
			$prepArr[] ="scale=$wmWid:$wmHgt,setsar=$wmWid/$wmHgt";
		}
			// Fading
		if(isset($wmAux->fade)){
			if(is_array($wmAux->fade)) $fadeArr = $wmAux->fade;
			else $fadeArr = array($wmAux->fade);
			$duration = $start_time = 0;
			foreach($fadeArr as $fade){
				$params = array();
				foreach($fade as $k=>$v) {
					switch($k){
						case "duration":
						case "start_time":
							$$k = max($$k, $v);
						case "type":
						case "alpha":
							$params[] = "$k=$v";
							break;
					}
				}
				if(count($params)>0)
					$prepArr[] = "fade=".(implode(":",$params));
			}
			if($duration>0 || $start_time>0){
				$targetVid->_watermarkFadeLoopTime = $duration+$start_time;
			}
		}
		if(isset($prepArr)) {
			$wmImg = "wmimg";
			$watermarkStr = "[1:v]".(implode(",", $prepArr))."[$wmImg];";
		}
		else {
			$wmImg = "1:v";
		}
		$marginsCrop = null;
		$marginsOver = null;
		if(isset($wmAux->margins)) {
			$wmAux->margins = explode("x",$wmAux->margins);
			if(isset($rotation) && $rotation!=180){
				$m=$wmAux->margins[0];
				$wmAux->margins[0]=$wmAux->margins[1];
				$wmAux->margins[1]=$m;
			}
			$w = $wmAux->margins[0];
			if(($centerW=strstr($w,"center"))!=false) $w = (int)str_replace("center", "",$w);
			$w = in_array($w, array(null,0,-1))? 0: $w;
			
			$h = $wmAux->margins[1];
			if(($centerH=strstr($h,"center"))!=false) $h = (int)str_replace("center", "",$h);
			$h = in_array($h, array(null,0,-1))? 0: $h;
			
			if($centerW){
				$marginsCrop = "(iw-ow)/2";
				if($w!=0) $marginsCrop.= ($w<0)? "$w": "+$w";
			}
			else
				$marginsCrop = ($w<0)? "iw-ow$w": "$w";
			$marginsCrop.=":";
			
			if($centerH){
				$marginsCrop.= "(ih-oh)/2";
				if($h!=0) $marginsCrop.= ($h<0)? "$h": "+$h";
			}
			else
				$marginsCrop.= ($h<0)? "ih-oh$h": "$h";
		}
		else {
			$marginsCrop = "0:0";
		}
		$marginsOver = str_replace(array("iw","ow","ih","oh"), array("main_w","overlay_w","main_h","overlay_h"), $marginsCrop);

		if(isset($wmAux->opacity)){
			$watermarkStr.= "[0:v]crop=$wmWid:$wmHgt:".$marginsCrop.",setsar=$wmWid/".$wmHgt."[cropped];";
			$watermarkStr.= "[cropped][$wmImg]blend=all_mode='overlay':all_opacity=".min(1,$wmAux->opacity)."[blended];";
			$watermarkStr.= "[0:v][blended]overlay=$marginsOver";
		}
		else {
			$watermarkStr.= "[0:v][$wmImg]overlay=$marginsOver";
		}
		
		return $watermarkStr;
	}

	/* ---------------------------
	 * CheckConstraints
	 */
	public function CheckConstraints(KDLMediaDataSet $source, KDLFlavor $target, array &$errors=null, array &$warnings=null)
	{
		$vidCodecArr = array("g2m3", "g2m4", "gotomeeting3", "gotomeeting4", "gotomeeting","icod","intermediate codec");
		if(isset($source->_video) && $source->_video->IsFormatOf($vidCodecArr)){
			return false;
		}
		
		if(parent::CheckConstraints($source, $target, $errors, $warnings)==true)
			return true;
		return false;
	}
}
	
