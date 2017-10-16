<?php

	/* ===========================
	 * KDLProcessor
	 */
	class KDLProcessor  {

		/* ---------------------
		 * Data
		 */
		private	$_srcDataSet=null;

		/* ----------------------
		 * Cont/Dtor
		 */
		public function __construct() {
			$this->_srcDataSet = new KDLMediaDataSet();
		}
		public function __destruct() {
			unset($this);
		}

		/* ----------------------
		 * Getters/Setters
		 */
		/**
		 * @return the $_warnings
		 */
		public function get_warnings() {
			return $this->_srcDataSet->_warnings;
		}

		/**	 
		 * $return the $_srcDataSet
		 */
		public function get_srcDataSet() {
			return $this->_srcDataSet;
		}

		/* ------------------------------
		 * function Generate
		 */
		public function Generate(KDLMediaDataSet $mediaSet, KDLProfile $profile, array &$targetList)
		{
			if($mediaSet!=null && $mediaSet->IsDataSet()){
				$rv=$this->Initialize($mediaSet);

				if($rv==false) {
					/*
					 * fix #9599 - handles rm files that fails to extract media info, but still playable by real player -
					 * simulate video and audio elements, although no source mediainfo is provided
					 */
					if($this->_srcDataSet->_container && $this->_srcDataSet->_container->IsFormatOf(array("realmedia"))){
						$rmSrc = $this->_srcDataSet;
						$rmSrc->_errors=array();
						$rmSrc->_video = new KDLVideoData;
						$rmSrc->_video->_id = $rmSrc->_video->_format = "realvideo";
						$rmSrc->_audio = new KDLAudioData;
						$rmSrc->_audio->_id = $rmSrc->_audio->_format = "realaudio";
						$rmSrc->_warnings[KDLConstants::ContainerIndex][] = // "Product bitrate too low - ".$prdAud->_bitRate."kbps, required - ".$trgAud->_bitRate."kbps.";
							KDLWarnings::ToString(KDLWarnings::RealMediaMissingContent);
KalturaLog::log("An invalid source RealMedia file thatfails to provide valid mediaInfodata. Set up a flavor with 'default' params.");
					}
					/*
					 * ARF (Webex) sources don't have proper mediaInfo, therefore turn on the Force flag to carry on with conversion processing
					 */
					else if(isset($mediaSet->_container) && $mediaSet->_container->_format=="arf"){
						foreach($profile->_flavors as $fl)
							$fl->_flags=$fl->_flags|KDLFlavor::ForceCommandLineFlagBit;
						$mediaSet->_errors = array();
KalturaLog::log("ARF (Webex) sources don't have proper mediaInfo, therefore turn on the Force flag to carry on with conversion processing.");
					}
					else {
						return false;
					}
				}
			}
			if($profile==null)
				return true;

			$this->GenerateTargetFlavors($profile, $targetList);
			if(count($this->_srcDataSet->_errors)>0){
				return false;
			}
			return true;
		}

		/* ------------------------------
		 * Initialize
		 */
		public function Initialize(KDLMediaDataSet $mediaInfoObj) {
			$this->_srcDataSet = $mediaInfoObj;
			if($this->_srcDataSet->Initialize()==false)
				return false;
			else
				return true;
		}

	/**
	 * @return the $_errors
	 */
	public function get_errors() {
		return $this->_srcDataSet->_errors;
	}

		
		/* ------------------------------
		 * GenerateTargetFlavors
		 */
		public function GenerateTargetFlavors(KDLProfile $profile, array &$targetList)
		{
//			if($this->_srcDataSet->_video) 
			{
				foreach ($profile->_flavors as $flavor){
					$target = $flavor->GenerateTarget($this->_srcDataSet);
					if(isset($target))
						$targetList[] = $target;
				}
				$this->validateProfileTarget($targetList);
			}
		}
		
		/* ------------------------------
		 * GenerateIntermediateSource
		 */
		public function GenerateIntermediateSource(KDLMediaDataSet $mediaSet, KDLProfile $profile=null)
		{
			/*
			 * Check minimal source validity, else get out
			 */
			if($mediaSet==null || !$mediaSet->IsDataSet()){
				return null;
			}
			/*
			 * Source is invalid if Initialize() fails, unless it is an ARF
			 */
			if(!((isset($mediaSet->_container) && $mediaSet->_container->_format=="arf")
			|| $mediaSet->Initialize())){
				return null;			
			}
							
			$interSrcProfile = null;
			$forceAudioStream = false;
			/*
			 * For ARF ==> webex plugin 
			 */
			if(isset($mediaSet->_container) && $mediaSet->_container->IsFormatOf(array("arf"))) {
				$interSrcProfile = $this->setProfileWithIntermediateSource(KDLContainerTarget::WMV,
						KDLVideoTarget::WVC1A, 4000, 1080,
						KDLAudioTarget::WMA, 128, 0,
						1, "webexNbrplayer.WebexNbrplayer");
					/*
					 * Following creates 3 retries for Webex conversions.
					 * Required for the sake of 'garbled audio' issue.
					 */
				$interSrcFlavor = $interSrcProfile->_flavors[0];
				$interSrcFlavor->_transcoders[] = $interSrcFlavor->_transcoders[0];
				$interSrcFlavor->_transcoders[] = $interSrcFlavor->_transcoders[0];
			}
			
			/*
			 * For GotoMeeting ==> EE plugin 
			 */
				 		/*
				 		 * FFmpeg 2.1 and higher handles G2M4
				 		 */
			else if(isset($mediaSet->_video) && $mediaSet->_video->IsFormatOf(array("gotomeeting","g2m3","gotomeeting3"/*,"g2m4","gotomeeting3"*/))) {
				$interSrcProfile = $this->setProfileWithIntermediateSource(KDLContainerTarget::WMV,
						KDLVideoTarget::WVC1A, 4000, 1080,
						KDLAudioTarget::WMA, 128, 0,
						1, "expressionEncoder.ExpressionEncoder");
			}
			/*
			 * For MAC native (icod, qt/wmv/wma ==> MAC plugin 
			 */
			else if(isset($mediaSet->_video) 
				 && (
				 		/*
				 		 * FFmpeg 2.1 and higher handles ICOD
				 		 */
				 	//$mediaSet->_video->IsFormatOf(array("icod","intermediate codec"))||
					($mediaSet->_container->IsFormatOf(array("qt","mov")) 
					   && $mediaSet->_video->IsFormatOf(array("wmv","wmv2","wmv3","wvc1","vc1","vc-1")) 
					   && $mediaSet->_audio->IsFormatOf(array("wma","wma2","wma3","windows media audio","windows media audio 10 professional"))
					  ) 
				    )
				   )
				{
					$interSrcProfile = $this->setProfileWithIntermediateSource(KDLContainerTarget::MP4, 
						KDLVideoTarget::H264H, 4000, 1080, 
						KDLAudioTarget::AAC, 128, 0, 
						1, "quickTimeTools.QuickTimeTools");
			}
			/*
			 * For "red/green strip" on On2 ==> ffmpeg intermedite reconversion 
			 */
			else if(isset($mediaSet->_video) && $mediaSet->_video->IsFormatOf(array("xdvd","xdva","xdvb","xdvc","xdve","xdvf","xdv4","hdv2"))) {
				foreach($profile->_flavors as $flvr){
					foreach ($flvr->_transcoders as $trans) {
						if($trans->_id==KDLTranscoders::ON2){
							$interSrcProfile = $this->setProfileWithIntermediateSource(KDLContainerTarget::MP4, 
								KDLVideoTarget::H264H, 4000, 1080, 
								KDLAudioTarget::AAC, 128, 0, 
								0, KDLTranscoders::FFMPEG);
							break;
						}
					}
					if(isset($interSrcProfile)){
						break;
					}
				}
			}
			/*
			 * Add silent audio track to the video in case on of the flavors is with widevine tag since widevine does not support files with no audio track 
			 */
			else if(isset($mediaSet->_video) && !isset($mediaSet->_audio)) {
	            foreach($profile->_flavors as $flvr) {
	            	if(preg_match('/widevine/', strtolower($flvr->_tags), $matches)) {
	                	$interSrcProfile = $this->setProfileWithIntermediateSource(KDLContainerTarget::MP4, 
								KDLVideoTarget::H264H, 4000, 1080, 
								KDLAudioTarget::AAC, 128, 0, 
								0, KDLTranscoders::FFMPEG);
						$forceAudioStream = true;
						break;
					}
				}
			}
			
			/*
			 * Progressive Segmented WVC1 
			 */
			else if(isset($mediaSet->_video) && $mediaSet->_video->IsFormatOf(array("wvc1","wmv3")) 
				 && isset($mediaSet->_contentStreams) && isset($mediaSet->_contentStreams->video) && count($mediaSet->_contentStreams->video)>0 
				 && isset($mediaSet->_contentStreams->video[0]->progressiveSegmented) && $mediaSet->_contentStreams->video[0]->progressiveSegmented==true) {
				foreach($profile->_flavors as $flvr){
					foreach ($flvr->_transcoders as $trans) {
						if($trans->_id==KDLTranscoders::FFMPEG){
							$interSrcProfile = $this->setProfileWithIntermediateSource(KDLContainerTarget::MP4, 
								KDLVideoTarget::H264H, 4000, 1080, 
								KDLAudioTarget::AAC, 128, 0, 
								0, KDLTranscoders::MENCODER);
							break;
						}
					}
					if(isset($interSrcProfile)){
						break;
					}
				}
			}
			
			/*
			else if($mediaSet->_video->IsFormatOf(array("tscc","tsc2"))) {
				$interSrcProfile = $this->setProfileWithIntermediateSource(KDLContainerTarget::MP4, 
						KDLVideoTarget::H264H, 4000, 1080, 
						KDLAudioTarget::AAC, 128, 0, 
						0, KDLTranscoders::FFMPEG);
			}
			*/
			
			/*
			 * If no "inter-src" cases ==> get out
			 */
			if(!isset($interSrcProfile))
				return null;

KalturaLog::log("Automatic Intermediate Source will be generated");
			$targetList = array();
			$this->Generate($mediaSet, $interSrcProfile, $targetList);
			if(count($targetList)==0)
				return null;
			if(!isset($targetList[0]->_video->_width)){
				$targetList[0]->_video->_width = 0;
			}
			//Add silent track to video
			if($forceAudioStream){
				//When duration is set on the source we will use it instead of the -shortest to avoid large difference between video and audio difference
				if($this->_srcDataSet->_video->_duration)
					$useToAddSilence = "-t " . $this->_srcDataSet->_video->_duration/1000;
				else 
					$useToAddSilence = "-shortest";
				$cmd = $targetList[0]->_transcoders[0]->_cmd;
				$cmd = str_replace("__inFileName__", "__inFileName__ -ar 44100 -ac 2 -f s16le -i /dev/zero " . $useToAddSilence, $cmd);
				$cmd = str_replace("-an", "-b:a 64k", $cmd);
				$targetList[0]->_transcoders[0]->_cmd = $cmd;
			}
			return $targetList[0];
		}
		
		/* ------------------------------
		 * ProceessFlavorsForCollection
		 */
		public static function ProceessFlavorsForCollection($flavorList)
		{
			$ee3obj = new KDLExpressionEncoder3(KDLTranscoders::EE3);
			return $ee3obj->GenerateSmoothStreamingPresetFile($flavorList);
		}
		
		/* ------------------------------
		 * ValidateProductFlavors
		 */
		public function ValidateProductFlavors(KDLMediaDataSet $source, array $targetList, array $productList)
		{
		$rv = true;
			foreach ($targetList as $trg) {
				if($trg->IsRedundant())
					continue;
				$prd = $trg->IsInArray($productList);
				if($prd==null)
					$this->_srcDataSet->_errors[KDLConstants::ContainerIndex][] = "Missing flavor (".$trg->_id.")";
				
				if($trg->ValidateProduct($source, $prd)==false)
					$rv = false;
			}
			return rv;
		}

		/* ------------------------------
		 * validateProfileTarget
		 */
		private function validateProfileTarget(array &$targetList)
		{
			$prev=null;
			foreach ($targetList as $key => $target){
				
					/*
					 * Redundency checking 
					 */
				if($prev==null){
					$prev=$target;
					continue;
				}
				
				if($target->ProcessRedundancy($prev)==false){
					$prev=$target;
				}
			}
		}

		/* ------------------------------
		 * setProfileWithIntermediateSource
		*/
		private function setProfileWithIntermediateSource($contId, $vidId, $vidBr, $vidHeight, $audId, $audBr, $audSr, $engVer, $engine)
		{
			$interSrcFlavor = new KDLFlavor();
			$interSrcFlavor->_name = "Automatic Intermediate Source";
			$interSrcFlavor->_id = 0;
			$interSrcFlavor->_container = new KDLContainerData();
			$interSrcFlavor->_container->_id = $contId;
			$vid = new KDLVideoData();
				$vid->_id = $vidId;
				$vid->_bitRate = $vidBr;
				$vid->_height = $vidHeight;
			$interSrcFlavor->_video = $vid;
			$aud = new KDLAudioData();
				$aud->_id = $audId;
				$aud->_bitRate = $audBr;
				$aud->_sampleRate = $audSr;
			$interSrcFlavor->_audio = $aud;
			$interSrcFlavor->_engineVersion = $engVer;
			
			$opr = new KDLOperationParams(); 
			$opr->Set($engine);
			if($interSrcFlavor->_engineVersion==1) {
				$opr->_engine = KalturaPluginManager::loadObject('KDLOperatorBase', $opr->_id);
			}
			else {
				$opr->_engine = new KDLOperatorWrapper($opr->_id);
			}
			if($opr->_engine==null)
				return null;
			$interSrcFlavor->_transcoders[] = $opr;
			$interSrcProfile = new KDLProfile();
			$interSrcProfile->_flavors[] = $interSrcFlavor;
			
			return $interSrcProfile;
		}
		
	}

	/* ===========================
	 * KDLProfile
	 */
	class KDLProfile {

		/* ---------------------
		 * Data
		 */
		public $_flavors = array();
		
		/* ----------------------
		 * Cont/Dtor
		 */
		public function __construct() {
			;
		}
		public function __destruct() {
			unset($this);
		}

		/* ---------------------------
		 * ToString
		 */
		public function ToString(){
		$rvStr = null;
		$i=0;
			foreach ($this->_flavors as $flavor){
				$str = $flavor->ToString();
				if($str){
					$rvStr=$rvStr.$i."=>".$str."<br>\n";
				}
				$i++;
			}
			return $rvStr;
		}
	}
	
?>
