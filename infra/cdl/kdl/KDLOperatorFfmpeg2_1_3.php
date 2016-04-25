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
		/*
		 * On multi-lingual, add:
		* - explicit mapping for video (if required)
		* - the required audio channels
		*/
		if(isset($target->_audio) && isset($target->_multiStream->audio->languages)
				&& count($target->_multiStream->audio->languages)>0){
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
		
		$cmdValsArr = explode(' ', $cmdStr);
		
		if(isset($target->_video->_watermarkData)) {
				
				// Fading requires looping of the WM image
			if(isset($target->_video->_watermarkFadeLoopTime) && $target->_video->_watermarkFadeLoopTime>0){
					/*
					 * The loop time should be minimum of the video duration (_explicitClipDur) and 
					 * the calculated largest fade time.
					 * Otherwise set loop time to 60 sec
					 */
				$loopTime = $target->_video->_watermarkFadeLoopTime + 0.5;
				if(isset($target->_explicitClipDur) && $target->_explicitClipDur>0){
					$loopTime = min($loopTime, round($target->_explicitClipDur/1000));
				}
			}
			$auxArr = array();
			if(is_array($target->_video->_watermarkData)){
				$watermarkDataArr = $target->_video->_watermarkData;
				$wmImgIdx = 1;
				foreach ($watermarkDataArr as $watermarkData){
					if(isset($loopTime))
						array_push($auxArr, "-loop", 1, "-t", $loopTime);
					array_push($auxArr, "-i",KDLCmdlinePlaceholders::WaterMarkFileName."_$wmImgIdx");
					$wmImgIdx++;
				}
			}
			else {
				if(isset($loopTime))
					array_push($auxArr, "-loop", 1, "-t", $loopTime);
				array_push($auxArr, "-i",KDLCmdlinePlaceholders::WaterMarkFileName);
			}
			$insertHere = end(array_keys($cmdValsArr, '-i'))+2;
			array_splice ($cmdValsArr, $insertHere, 0, $auxArr);
		}

		/*
		 * Subtitles, if any ...
		 */
		if(isset($target->_video->_subtitlesData->action) && $target->_video->_subtitlesData->action=='embed'){
			$subsData = $target->_video->_subtitlesData;
			$auxArr[] = '-i';
			$insertHere = end(array_keys($cmdValsArr, '-i'))+2;
			if(isset($subsData->filename))
				$subsFilename = $subsData->filename;
			else
				$subsFilename = KDLCmdlinePlaceholders::SubTitlesFileName;
			$auxArr = array('-i', $subsFilename, '-c:s','mov_text');
			if(isset($subsData->language)){
				$auxArr[] = "-metadata:s";
				$auxArr[] = "language=".$subsData->language;
			}
			// c:s:0 mov_text -metadata:s:s:0 ger
			array_splice ($cmdValsArr, $insertHere, 0, $auxArr);
		}
		
		if(isset($target->_video) && in_array($target->_video->_id, array(KDLVideoTarget::H264,KDLVideoTarget::H264B,KDLVideoTarget::H264M,KDLVideoTarget::H264H))){
			self::adjustH264Level($target->_video, $cmdValsArr);
		}
		
		self::rearrangeFiltersAndOpts($cmdValsArr);
		
		$cmdStr = implode(" ", $cmdValsArr);

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
		if(!isset($target->_video)){// || !isset($target->_video->_watermarkData))
			return $cmdStr;
		}
		$cmdValsArr = explode(' ', $cmdStr);
			
		$key=array_search("-vf", $cmdValsArr);
		if($key!==false) {
			$cmdValsArr[$key] = '-filter_complex';
			$cmdStr = implode(" ", $cmdValsArr);
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
		 * Addjust pipes ins/outs
		 */
		$nextVidIn = "0:v";
		$idxFlt = 0;
		$filters = parent::generateVideoFilters($vid);
		if(isset($filters) && count($filters)>0){
			foreach($filters as $idxFlt=>$filter){
				$filters[$idxFlt] = "[$nextVidIn]".$filters[$idxFlt];
				$nextVidIn = "vflt$idxFlt";
				if($idxFlt>0) {
					$prev = $idxFlt-1;
					$filters[$prev] = $filters[$prev]."[vflt$prev]";
				}
			}
		}

		/*
		 * Watermarking, if any ...
		 */
		$watermarkFilterStr = self::generateWatermarkParams($vid, $nextVidIn);
		if(isset($watermarkFilterStr)){
			if(count($filters)>0){
				$filters[$idxFlt] = $filters[$idxFlt]."[$nextVidIn]";
				$idxFlt++;
			}
			$filters[] = $watermarkFilterStr;
			$nextVidIn = "watermarked";
		}
		
		/*
		 * Subtitles, if defiended with 'render'(burned_in) mode ...
		 */
		if(isset($vid->_subtitlesData->action) && $vid->_subtitlesData->action=='render'){
			
			/*
			 * Scan for preset fields
			 */
			$params = array();
			foreach($vid->_subtitlesData as $k=>$v) {
				switch($k){
					case "filename":
					case "original_size":
					case "fontsdir":
					case "charenc":
					case "force_style":
						if(isset($v))
							$params[] = "$k=$v";
						break;
				}
			}
			if(!isset($vid->_subtitlesData->filename))
				array_unshift($params, "filename=".KDLCmdlinePlaceholders::SubTitlesFileName);
			
			if(count($params)>0){
				if(count($filters)>0){
					$filters[$idxFlt] = $filters[$idxFlt]."[$nextVidIn]";
					$idxFlt++;
				}
				$filters[] = "[$nextVidIn]subtitles=".(implode(":",$params));;
				$nextVidIn = "subtitled";
			}
		}
		return $filters;
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
	 * @return Ambigous <NULL, Ambigous, string>
	 */
	protected static function generateWatermarkParams($targetVid, $vidIn)
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
/*
Sample multi WM cmd line - 
ffmpeg -threads 1 -i VIDEO -i WM1.jpg -loop 1 -t 30 -i WM2.jpg 
	-c:v libx264 -subq 2 -qcomp 0.6 -qmin 10 -qmax 50 -qdiff 4 -coder 0 -vprofile baseline -force_key_frames expr:'gte(t,n_forced*2)' -pix_fmt yuv420p -b:v 317k -s 320x240 -r 25 -g 50 
	-filter_complex '[1:0]scale=48:50,setsar=48/50[wmimg1];
	                 [2:0]scale=48:50,setsar=48/50,fade=in:alpha=1:st=0.1:d=0.5,fade=out:alpha=1:st=6:d=0.5[wmimg2];
	                 [0:v][wmimg1]overlay=10:10[overlayed1];
	                 [overlayed1][wmimg2]overlay=(main_w-overlay_w)/2-10:(main_h-overlay_h)/2+10' -aspect 320:240 -c:a libfdk_aac -filter_complex 'aresample=async=1:min_hard_comp=0.100000:first_pts=0' -b:a 64k -ar 44100 -ac 2 -map_chapters -1 -map_metadata -1  -f mp4 -flags +loop+mv4 -cmp 256 -partitions +parti4x4+partp8x8+partb8x8 -trellis 1 -refs 1 -me_range 16 -keyint_min 20 -sc_threshold 40 -i_qfactor 0.71 -bt 100k -maxrate 400k -bufsize 1200k -rc_eq 'blurCplx^(1-qComp)' -level 30  -vsync 1 -threads 4  -y /web/content/shared/tmp/wm_tests.mp4
 */
		if(!isset($targetVid->_watermarkData))
			return null;
		
		$watermarkStr = null;
		$rotation = null;
		if(isset($targetVid->_rotation))
			$rotation = $targetVid->_rotation;

		$watermarkStr = null;
		$maxFadeDuration = 0;
		if(is_array($targetVid->_watermarkData))
			$watermarkDataArr = $targetVid->_watermarkData;
		else
			$watermarkDataArr = array($targetVid->_watermarkData);

		$wmImgIdx = 1;
		foreach($watermarkDataArr as $watermarkData) {
			if(isset($watermarkStr)){
				$watermarkStr.="[$vidIn];";
			}
			$fadeDur = 0;
			$watermarkStr.= self::generateSingleWatermark(clone($watermarkData), $vidIn, $fadeDur, $wmImgIdx, $rotation);
			$maxFadeDuration = max($maxFadeDuration,$fadeDur);
			$vidIn = "overlayed".$wmImgIdx;
			$wmImgIdx++;
		}
		if($maxFadeDuration>0){
			$targetVid->_watermarkFadeLoopTime = $maxFadeDuration;
		}
		return $watermarkStr;
		
	}

	/**
	 * 
	 * @param unknown_type $watermarkData
	 * @param unknown_type $vidInIdx - stream index of the input video
	 * @param unknown_type $maxFadeDuration
	 * @param unknown_type $wmImgIdx - WM file stream index
	 * @param unknown_type $rotation
	 * @return Ambigous <NULL, string>
	 */
	protected static function generateSingleWatermark($watermarkData, $vidInIdx, &$maxFadeDuration, $wmImgIdx, $rotation)
	{
		$wmHgt=KDLCmdlinePlaceholders::WaterMarkHeight."_$wmImgIdx";
		$wmWid=KDLCmdlinePlaceholders::WaterMarkWidth."_$wmImgIdx";
		
		$watermarkStr = null;
		$prepArr = array();
		switch ($rotation){
			case 90:
				$prepArr[]="transpose=1";
			case 180:
				$prepArr[]="transpose=1";
			case 270:
			case -90:
				$prepArr[]="transpose=1";
		}
		
		KalturaLog::log("Watermark data:\n".print_r($watermarkData,1));
			// Scaling
		if(isset($watermarkData->scale)) {
			$watermarkData->scale = explode("x",$watermarkData->scale);
			$prepArr[] ="scale=$wmWid:$wmHgt,setsar=$wmWid/$wmHgt";
		}
			// Fading
		if(isset($watermarkData->fade)){
			if(is_array($watermarkData->fade)) $fadeArr = $watermarkData->fade;
			else $fadeArr = array($watermarkData->fade);
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
				$maxFadeDuration = $duration+$start_time;
			}
		}
		if(isset($prepArr)) {
			$wmImg = "wmimg$wmImgIdx";
			$watermarkStr = "[$wmImgIdx:0]".(implode(",", $prepArr))."[$wmImg];";
		}
		else {
			$wmImg = "$wmImgIdx:0";
		}
		$marginsCrop = null;
		$marginsOver = null;
		if(isset($watermarkData->margins)) {
			$watermarkData->margins = explode("x",$watermarkData->margins);
			$w = $watermarkData->margins[0];
			if(($centerW=strstr($w,"center"))!=false) $w = (int)str_replace("center", "",$w);
			$w = in_array($w, array(null,0,-1))? 0: $w;
			
			$h = $watermarkData->margins[1];
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

		if(isset($watermarkData->opacity)){
			$watermarkStr.= "[$vidInIdx]crop=$wmWid:$wmHgt:".$marginsCrop.",setsar=$wmWid/".$wmHgt."[cropped];";
			$watermarkStr.= "[cropped][$wmImg]blend=all_mode='overlay':all_opacity=".min(1,$watermarkData->opacity)."[blended];";
			$watermarkStr.= "[$vidInIdx][blended]overlay=$marginsOver";
		}
		else {
			$watermarkStr.= "[$vidInIdx][$wmImg]overlay=$marginsOver";
		}
		
		return $watermarkStr;
	}

	/**
	 *
	 * @param string $cmdStr
	 */
	protected static function adjustH264Level($targetVid, array &$cmdValsArr)
	{
		$H264LevelMacroBlocks = array(
				10=>99,
				11=>192,
				12=>396,
				13=>396,
				20=>396,
				21=>792,
				22=>1620,
				30=>1620,
				31=>3600,
				32=>5120,
				40=>8192,
				41=>8192,
				42=>8704,
				50=>22080,
				51=>36864,
				52=>36864
		);
		
			/*
			 * Adjust only existing 'level' param, don't add a new one
			 */
		$key=array_search('-level', $cmdValsArr);
		if($key==false){
			return;
		}
		
			/*
			 * WID and HGT required to calc target macro blocks,
			 * if npt set ==> leave
			 */
		if(!(isset($targetVid->_width) && isset($targetVid->_height))){
			return;
		}
			/*
			 * Calc target macro blocks
			 */
		$targetMacroBlocks = round($targetVid->_width/16)*round($targetVid->_height/16);
		
		foreach ($H264LevelMacroBlocks as $level=>$mBlks){
			if($mBlks>$targetMacroBlocks){
				if($cmdValsArr[$key+1]<10)
					$currentLevel = round($cmdValsArr[$key+1]*10);
				else
					$currentLevel = $cmdValsArr[$key+1];
				if($level>$currentLevel){
					$cmdValsArr[$key+1] = $level;
				}
				break;
			}
		}
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
	
