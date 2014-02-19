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
			 * For resample-filter case - 
			 * 'async 2' causes aud-br distortion ==> set to 'async 1'
			 */
		//if(isset($target->_audio->_useResampleFilter) && $target->_audio->_useResampleFilter==true)
		{
			$cmdValsArr = explode(' ', $cmdStr);
			$key=array_search("-async", $cmdValsArr);
			if($key!==false) {
				if($cmdValsArr[$key+1]>1)
					$cmdValsArr[$key+1]=1;
			}
			
			$cmdStr = implode(" ", $cmdValsArr);
		}
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
		
		if(isset($target->_audio->_useResampleFilter) && $target->_audio->_useResampleFilter==true){
			$cmdValsArr = explode(' ', $cmdStr);
			
			$key=array_search("-ar", $cmdValsArr);
			if($key!==false) {
				$cmdValsArr[$key] = "-af";
				$cmdValsArr[$key+1]="aresample=".$cmdValsArr[$key+1];
			}
			
			$cmdStr = implode(" ", $cmdValsArr);
		}
			
		return $cmdStr;
	}
	
	/* ---------------------------
	 * generateVideoParams
	 */
	protected function generateVideoParams(KDLFlavor $design, KDLFlavor $target)
	{
		if(!isset($target->_video)) {
			return " -vn";
		}
	
		$vid = $target->_video;
		if($vid->_id==KDLVideoTarget::VP9) {
			$cmdStr = " -c:v libvpx-vp9";
			return $cmdStr;
		}
		return parent::generateVideoParams($design, $target);
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
	
}
	