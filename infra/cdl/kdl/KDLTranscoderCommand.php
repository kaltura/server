<?php

	/* ===========================
	 * KDLOperatorWrapper
	 */
class KDLOperatorWrapper extends KDLOperatorBase {
    public function __construct($id, $name=null, $sourceBlacklist=null, $targetBlacklist=null) {
    	$srcBlacklist = $sourceBlacklist;
		if(is_null($sourceBlacklist) && array_key_exists($id, KDLConstants::$TranscodersSourceBlackList)) {
			$srcBlacklist = KDLConstants::$TranscodersSourceBlackList[$id];
		}
		$trgBlacklist = $targetBlacklist;
		if(is_null($targetBlacklist) && array_key_exists($id, KDLConstants::$TranscodersTargetBlackList)) {
			$trgBlacklist = KDLConstants::$TranscodersTargetBlackList[$id];
		}
    	parent::__construct($id,$name,$srcBlacklist,$trgBlacklist);
    }

	public function GenerateCommandLine(KDLFlavor $predesign, KDLFlavor $target, $extra=null)
	{
//		$cmdLineGenerator = $target->SetTranscoderCmdLineGenerator($predesign);
		$cmdLineGenerator = new KDLTranscoderCommand($predesign, $target);
		$params = new KDLOperationParams();
		$params->Set($this->_id, $extra);
		if(isset($predesign->_video))
			return $cmdLineGenerator->Generate($params, $predesign->_video->_bitRate);
		else 
			return $cmdLineGenerator->Generate($params, 0);
	}

    /* ---------------------------
	 * CheckConstraints
	 */
	public function CheckConstraints(KDLMediaDataSet $source, KDLFlavor $target, array &$errors=null, array &$warnings=null)
	{
//No need for 'global' check, each engine can check for itself
//		if(parent::CheckConstraints($source, $target, $errors, $warnings)==true)
//			return true;

		if($this->_id==KDLTranscoders::FFMPEG_AUX) {
			$transcoder = new KDLOperatorFfmpeg2_2($this->_id);
			if($transcoder->CheckConstraints($source, $target, $errors, $warnings)==true)
				return true;
		}
			
		if($this->_id==KDLTranscoders::FFMPEG) {
			$transcoder = new KDLOperatorFfmpeg2_7_2($this->_id);
			if($transcoder->CheckConstraints($source, $target, $errors, $warnings)==true)
				return true;
		}
	
		
		if($this->_id==KDLTranscoders::MENCODER) {
			$transcoder = new KDLOperatorMencoder($this->_id);
			if($transcoder->CheckConstraints($source, $target, $errors, $warnings)==true)
				return true;
		}
	
		
		if($this->_id==KDLTranscoders::ON2) {
			$transcoder = new KDLOperatorOn2($this->_id);
			if($transcoder->CheckConstraints($source, $target, $errors, $warnings)==true)
				return true;
		}
	
		/*
		 * Remove encoding.com - it is no longer supported
		 */
		if($this->_id==KDLTranscoders::ENCODING_COM){
			$warnings[KDLConstants::ContainerIndex][] =
				KDLWarnings::ToString(KDLWarnings::TranscoderLimitation, $this->_id)."(unsupported transcoder)";
			return true;
		}
		
		/*
		 * Prevent invalid copy attempts, that might erronously end up with 'false-positive' result
		 */
		if((isset($target->_video) && $target->_video->_id==KDLVideoTarget::COPY)
		|| (isset($target->_audio) && $target->_audio->_id==KDLAudioTarget::COPY)){
			if($target->_container->_id==KDLContainerTarget::FLV){
				$rvArr=$source->ToTags(array("web"));
				if(count($rvArr)==0){
					$errStr = "Copy to Target format:FLV, Source:".$source->ToString();
					$target->_errors[KDLConstants::ContainerIndex][] = 
						KDLErrors::ToString(KDLErrors::InvalidRequest, $errStr);
					return true;
				}
			}
		}
		
		return false;	
	}
}


	/* ===========================
	 * KDLTranscoderCommand
	 */
class KDLTranscoderCommand {
	
	private $_design;
	private $_target;
			
	public function __construct(KDLFlavor $design, KDLFlavor $target)
	{
		$this->_design = $design;
		$this->_target = $target;
	}	
	
	/* ---------------------------
	 * Generate
	 */
	public function Generate(KDLOperationParams $transParams, $maxVidRate)
	{
		$cmd=null;
		switch($transParams->_id){
			case KDLTranscoders::KALTURA:
				$cmd=$transParams->_id;
				break;
			case KDLTranscoders::ON2:
				$cmd=$this->CLI_Encode($transParams->_extra);;
				break;
			case KDLTranscoders::FFMPEG:
			case KDLTranscoders::FFMPEG_VP8:
				$cmd=$this->FFMpeg($transParams->_extra);
				break;
			case KDLTranscoders::MENCODER:
				$cmd=$this->Mencoder($transParams->_extra);
				break;
			case KDLTranscoders::ENCODING_COM:
				$cmd=$transParams->_id;
				break;
			case KDLTranscoders::FFMPEG_AUX:
				$cmd=$this->FFMpeg_aux($transParams->_extra);
				break;
			case KDLTranscoders::EE3:
				$cmd=$this->EE3($transParams->_extra);
				break;
		}
		return $cmd;
	}
	
	/* ---------------------------
	 * FFMpeg
	 */
	public function FFMpeg($extra=null)
	{
		$transcoder = new KDLOperatorFfmpeg2_7_2(KDLTranscoders::FFMPEG); 
		return $transcoder->GenerateCommandLine($this->_design,  $this->_target,$extra);
	}

	/* ---------------------------
	 * Mencoder
	 */
	public function Mencoder($extra=null)
	{
		$transcoder = new KDLOperatorMencoder(KDLTranscoders::MENCODER); 
		return $transcoder->GenerateCommandLine($this->_design,  $this->_target,$extra);
	}

	/* ---------------------------
	 * CLI_Encode
	 */
	public function CLI_Encode($extra=null)
	{
		$transcoder = new KDLOperatorOn2(KDLTranscoders::ON2); 
		return $transcoder->GenerateCommandLine($this->_design,  $this->_target,$extra);
	}
	
	/* ---------------------------
	 * Encoding_com
	 */
	public function Encoding_com($extra=null)
	{
		return $this->CLI_Encode($extra);
	}

	/* ---------------------------
	 * FFMpeg_aux
	 */
	public function FFMpeg_aux($extra=null)
	{/**/
		$transcoder = new KDLOperatorFfmpeg2_2(KDLTranscoders::FFMPEG_AUX); 
		return $transcoder->GenerateCommandLine($this->_design,  $this->_target,$extra);
	}

	/* ---------------------------
	 * EE3
	 */
	public function EE3($extra=null)
	{
		$ee3 = new KDLExpressionEncoder3(KDLTranscoders::EE3);
		return $ee3->GeneratePresetFile($this->_target);
	}

}

?>
