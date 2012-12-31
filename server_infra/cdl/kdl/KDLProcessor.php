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
				$this->validateProfileTarget($targetList);
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