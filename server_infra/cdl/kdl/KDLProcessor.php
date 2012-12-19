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
				$this->validateProfileTarget($this->_srcDataSet, $targetList);
//print_r($this->ProceessFlavorsForCollection($targetList));
			}
/*			else{
				$flavor = $profile->_flavors[0];
				$target = $flavor->GenerateTarget($this->_srcDataSet);
				$targetList[] = $target;
			}
*/
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
		private function validateProfileTarget($source, array &$targetList)
		{
				/* 
				 * Evaluate the 'adjusted' source height, to use as a for best matching flavor.
			 	 */
			$srcHgt = 0;
			if(isset($source->_video) && isset($source->_video->_height)){
				$srcHgt = $source->_video->_height;
				$srcHgt = $srcHgt - ($srcHgt%16);
			}
			
//			$largestCompliantIdx  = null; 	// index of the largest compliant flavor
			$matchSourceHeightIdx = null;	// index of the smallest flavor that matches the source height
			$prev=null;
			foreach ($targetList as $key => $target){

					/*
					 * If the video height is set, then look for the largest compliant flavor 
					 * and for the smallest to match the source height
					 */
				if(isset($target->_video) && isset($target->_video->_height)) {
/*					if((!isset($largestCompliantIdx)||($target->_video->_height>$targetList[$largestCompliantIdx]->_video->_height))
					&&  !$target->IsNonComply()){
						$largestCompliantIdx = $key;
					}*/
					if(!isset($matchSourceHeightIdx)||($targetList[$matchSourceHeightIdx]->_video->_height<$srcHgt)){
						$matchSourceHeightIdx = $key;
					}
				}
				
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

				/*
				 * If samllest-source-height-matching is found and it is 'non-compliant' (therefore it willnot be generated),
				 * set 'forceTranscode' flag for the 'matchSourceHeightIdx' flavor.
				 * If the smallest flavor is non-comply as well - 'force' it too - otherwise only the 'matchSourceHeightIdx' 
				 * will be produced
				 */
			if(isset($matchSourceHeightIdx) && $targetList[$matchSourceHeightIdx]->IsNonComply()) {
				$targetList[$matchSourceHeightIdx]->_flags = $targetList[$matchSourceHeightIdx]->_flags | (KDLFlavor::ForceTranscodingFlagBit);
				if($targetList[0]->IsNonComply()) {
					$targetList[0]->_flags = $targetList[0]->_flags | (KDLFlavor::ForceTranscodingFlagBit);
				}
			}
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