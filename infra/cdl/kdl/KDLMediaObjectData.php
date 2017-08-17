<?php

//include_once("KDLCommon.php");

	/* ---------------------------
	 * KDLBaseMediaData
	 */
	class KDLBaseMediaData {
		/* ---------------------
		 * Data
		 */
		public $_id=null,
			   $_format=null,
			   $_duration=null,
			   $_bitRate=null;
		public $_params=array();
			   

		/* ------------------------------
		 * Validate
		 */
		public function Validate(array &$errors, array &$warnings, $section=null) {
			self::sanityCheck($errors, $warnings, $section);
			if(count($errors)>0)
				return false;
			else
				return true;
		}

		/* ------------------------------
		 * GetIdOrFormat
		 */
		public function GetIdOrFormat()
		{
			if($this->_id && is_numeric($this->_id)==false)
				return $this->_id;
			else if($this->_format)
				return $this->_format;
			else
				return null;
		}
		
		/* ------------------------------
		 * GetIdOrFormatStatic
		 */
		public function GetIdOrFormatStatic($id, $format)
		{
			if($id && is_numeric($id)==false)
				return $id;
			else if($format)
				return $format;
			else
				return null;
		}
		
		/* ---------------------------
		 * CheckAndFixFormat
		 */
		public function CheckAndFixFormat(){
			if($this->_id==null || is_numeric($this->_id)){
				if($this->_format)
					$this->_id = $this->_format;
			}
		}

		/* ---------------------------
		 * ToString
		 */
		public function ToString(){
			$rvStr=null;
			$str=$this->GetIdOrFormat();
			if($str)
				$rvStr = "f:".$str;
			
			if($this->_duration)
				$rvStr=$rvStr.","."d:".round($this->_duration/1000,2);
				
			$str=$this->_bitRate;
			if($this->_bitRate)
				$rvStr=$rvStr.","."br:".round($this->_bitRate,2);

			return $rvStr;
		}
		
		public function GetSectionEnum() { return KDLConstants::ContainerIndex; }
		
		/* ------------------------------
		 * IsDataSet
		 */
		public function IsDataSet(){
			if($this->_id)
				return true;
			if($this->_format)
				return true;
			if($this->_duration)
				return true;
			if($this->_bitRate)
				return true;
			return false;
		}
		
		/* ------------------------------
		 * IsFormatOf
		 */
		public function IsFormatOf($formats){
			if(in_array($this->_format, $formats)
			|| in_array($this->_id, $formats)){
				return true;
			}
			else {
				return false;
			}
		}
		
		/* ------------------------------
		 * sanityCheck
		 */
		protected function sanityCheck(array &$errors, array &$warnings, $section=null) {
			if($section==null)
				$section=KDLConstants::ContainerIndex;
			if($this->_duration<KDLSanityLimits::MinDuration 
			|| $this->_duration>KDLSanityLimits::MaxDuration) // Up to 10 hours
				$warnings[$section][] = KDLWarnings::ToString(KDLWarnings::SanityInvalidDuration, $this->_duration);

			if($this->_bitRate<KDLSanityLimits::MinBitrate
			|| $this->_bitRate>KDLSanityLimits::MaxBitrate)
				$warnings[$section][] = KDLWarnings::ToString(KDLWarnings::SanityInvalidBitrate, $this->_bitRate);
		}
	}

	/* ---------------------------
	 * KDLContainerData
	 */
	class KDLContainerData extends KDLBaseMediaData {

		/* ---------------------
		 * Data
		 */
		public $_fileSize=0;
		public $_fileName=0;
		public $_isFastStart=1;
		
		public function GetSectionEnum() { return KDLConstants::ContainerIndex; }
		
		/* ------------------------------
		 * Validate
		 */
		public function Validate(array &$errors, array &$warnings, $section=null) {
			parent::Validate($errors, $warnings, KDLConstants::ContainerIndex);
			self::sanityCheck($errors, $warnings);
			if(count($errors)>0)
				return false;
			else
				return true;
		}
		
		/* ---------------------------
		 * ToString
		 */
		public function ToString(){
			$rvStr=parent::ToString();
			if($this->_fileSize)
				$rvStr=$rvStr.","."sz:".$this->_fileSize;
			
			if($rvStr)
				$rvStr = "CON(".$rvStr.")";
			return $rvStr;
		}
		
		/* ------------------------------
		 * IsDataSet
		 */
		public function IsDataSet(){
			if(parent::IsDataSet())
				return true;
			if($this->_fileSize)
				return true;
			return false;
		}
		
		/* ------------------------------
		 * sanityCheck
		 */
		protected function sanityCheck(array &$errors, array &$warnings, $section=null) {
			if($this->_fileSize<KDLSanityLimits::MinFileSize 
			|| $this->_fileSize>KDLSanityLimits::MaxFileSize) // Up to 10 hours
				// "Invalid file size (" . $this->_fileSize . "kb)";
			$errors[KDLConstants::ContainerIndex][] = KDLErrors::ToString(KDLErrors::SanityInvalidFileSize, $this->_fileSize);
		}
	}
	
	/* ---------------------------
	 * VideoData
	 */
	class KDLVideoData extends KDLBaseMediaData {

		/* ---------------------
		 * Data
		 */
		public $_width, 
			   $_height,
			   $_frameRate,
			   $_dar,
			   $_gop, //=KDLConstants::DefaultGOP;
			   $_rotation,
			   $_scanType=0; // 0:progressive, 1:interlaced
		public $_arProcessingMode= 0;/*
									 *  0: fix mode (original)
									 *  1: preserve ar (relevant when both wid and hgt are set)
									 *  2: letterbox mode
									 *  3: force 16/9 for everything that is not 16:9
									 *  4: force '-aspect' option
									 *  5: force perciese source AR
									 */
		public $_forceMult16 = 1;
		public $_cbr=0;		// Constant bit rate, when set - -b 50M -minrate 50M -maxrate 50M -bt 5M -bufsize 10M 
		public $_bt=0;		// bitrate tolerance
		public $_isGopInSec=0;// if set the value of gop is in seconds, vs. frames
												// execute flavor to source adaptations - 
		public $_isShrinkFramesizeToSource=1;	// - frame size
		public $_isShrinkBitrateToSource=1;		// - frame rate
		public $_isFrameRateForLowBrAppleHls=0; // for default fr (keep source fr),adjust for Apple HLS recommendations. Relevant only for <200 kbps;
		public $_anamorphic=0;					// null,0,1 - regular/square pixs, otherwise - anamorphic
		public $_forcedKeyFramesMode=1;			// ForcedKF not needed for mezz files, 0:no forced KF, 1:use GOP value (default), 2: follow source KF's
		public $_isCropIMX = false;				// For IMX sources, apply cropping of the top 32 pixs
		public $_maxFrameRate=0;
		public $_watermarkData;
		public $_subtitlesData = null;
		public $_complexityValue = null;
		public $_contentAwareness = null;
		
		public function GetSectionEnum() { return KDLConstants::VideoIndex; }
		
		/* ------------------------------
		 * Validate
		 */
		public function Validate(array &$errors, array &$warnings, $section=null) {
			parent::Validate($errors, $warnings, KDLConstants::VideoIndex);
			self::sanityCheck($errors, $warnings);
			if(count($errors)>0)
				return false;
			else
				return true;
		}

		/* ---------------------------
		 * GetPAR 
		 */
		public function GetPAR()
		{
			return $this->_width/$this->_height;
		}
		
		/* ---------------------------
		 * ToString
		 */
		public function ToString(){
			$rvStr=parent::ToString();
			$rvStr=$rvStr.",".$this->_width."x".$this->_height;
			
			if($this->_dar)
				$rvStr=$rvStr.","."dar:".round($this->_dar,2);
			
			if($this->_frameRate)
				$rvStr=$rvStr.","."fr:".$this->_frameRate;
			
			if($this->_gop)
				$rvStr=$rvStr.","."g:".$this->_gop;

			if($this->_rotation)
				$rvStr=$rvStr.","."rt:".$this->_rotation;

			if($this->_scanType)
				$rvStr=$rvStr.","."di:".$this->_scanType;

				if($rvStr)
				$rvStr = "VID(".$rvStr.")";
			return $rvStr;
		}

		/* ------------------------------
		 * IsDataSet
		 */
		public function IsDataSet(){
			if(parent::IsDataSet())
				return true;
			if($this->_width)
				return true;
			if($this->_height)
				return true;
			if($this->_frameRate)
				return true;
			if($this->_dar)
				return true;
			return false;
		}
		
		/* ------------------------------
		 * sanityCheck
		 */
		protected function sanityCheck(array &$errors, array &$warnings, $section=null) {
			if($this->_width==0){
				$this->_width = 480;
				$warnings[KDLConstants::VideoIndex][] = KDLWarnings::ToString(KDLWarnings::ZeroedFrameDim, $this->_width);
			}
			if($this->_height==0){
				$this->_height = 320;
				$warnings[KDLConstants::VideoIndex][] = KDLWarnings::ToString(KDLWarnings::ZeroedFrameDim, $this->_height);
			}
			
			if($this->_width<KDLSanityLimits::MinDimension 
			|| $this->_width>KDLSanityLimits::MaxDimension
			|| $this->_height<KDLSanityLimits::MinDimension 
			|| $this->_height>KDLSanityLimits::MaxDimension)
					// "Invalid width (" . $this->_width . "px)";
				$errors[KDLConstants::VideoIndex][] = KDLErrors::ToString(KDLErrors::SanityInvalidFrameDim, ($this->_width."x".$this->_height));

			if($this->_frameRate<KDLSanityLimits::MinFramerate 
			|| $this->_frameRate>KDLSanityLimits::MaxFramerate) 
				$warnings[KDLConstants::VideoIndex][] = KDLWarnings::ToString(KDLWarnings::SanityInvalidFarmerate, $this->_frameRate);

			if($this->_dar<KDLSanityLimits::MinDAR 
			|| $this->_dar>KDLSanityLimits::MaxDAR) 
				$warnings[KDLConstants::VideoIndex][] = KDLWarnings::ToString(KDLWarnings::SanityInvalidDAR, $this->_dar);
		}
	}

	/* ---------------------------
	 * AudioData
	 */
	class KDLAudioData extends KDLBaseMediaData {

		/* ---------------------
		 * Data
		 */
		public	$_channels, 
			$_sampleRate,
			$_resolution,
			$_aac_adtstoasc_filter;

		public function GetSectionEnum() { return KDLConstants::AudioIndex; }
		
		/* ------------------------------
		 * Validate
		 */
		public function Validate(array &$errors, array &$warnings, $section=null) {
			parent::Validate($errors, $warnings, KDLConstants::AudioIndex);
			self::sanityCheck($errors, $warnings);
			
			if(count($errors)>0)
				return false;
			else
				return true;
		}
		
		/* ---------------------------
		 * ToString
		 */
		public function ToString(){
			$rvStr=parent::ToString();

			if($this->_channels)
				$rvStr=$rvStr.","."ch:".$this->_channels;
			if($this->_sampleRate)
				$rvStr=$rvStr.","."sa:".$this->_sampleRate;
			if($this->_resolution)
				$rvStr=$rvStr.","."re:".$this->_resolution;
				
			if($rvStr)
				$rvStr = "AUD(".$rvStr.")";
			return $rvStr;
		}
		
		/* ------------------------------
		 * IsDataSet
		 */
		public function IsDataSet(){
			if(parent::IsDataSet())
				return true;
			if($this->_sampleRate)
				return true;
			if($this->_resolution)
				return true;
			if($this->_channels)
				return true;
			return false;
		}
		
		/* ------------------------------
		 * sanityCheck
		 */
		public function sanityCheck(array &$errors, array &$warnings, $section=null) {
			;
		}
	}
	
	
	
	/* ---------------------------
	 * SwfData
	 */
	class KDLSwfData extends KDLBaseMediaData {

		/* ---------------------
		 * Data
		 */
		public	$_flashVersion,
				$_zoom,
				$_zlib,
				$_jpegQuality,
				$_sameWindow,
				$_insertStop,
				$_useShapes,
				$_storeFonts,
				$_flatten,
				$_poly2Bitmap;

		public function GetSectionEnum() { return KDLConstants::SwfIndex; }
		
		/* ------------------------------
		 * Validate
		 */
		public function Validate(array &$errors, array &$warnings, $section=null) {
			parent::Validate($errors, $warnings, KDLConstants::SwfIndex);
			self::sanityCheck($errors, $warnings);
			
			if(count($errors)>0)
				return false;
			else
				return true;
		}
		
		/* ---------------------------
		 * ToString
		 */
		public function ToString(){
			$rvStr=parent::ToString();

			if($this->_flashVersion)
				$rvStr=$rvStr.","."flashVersion:".$this->_flashVersion;
			if($this->_zoom)
				$rvStr=$rvStr.","."zoom:".$this->_zoom;
			if($this->_zlib)
				$rvStr=$rvStr.","."zlib:".$this->_zlib;
			if($this->_jpegQuality)
				$rvStr=$rvStr.","."jpegQuality:".$this->_jpegQuality;
			if($this->_sameWindow)
				$rvStr=$rvStr.","."sameWindow:".$this->_sameWindow;
			if($this->_insertStop)
				$rvStr=$rvStr.","."insertStop:".$this->_insertStop;
			if($this->_useShapes)
				$rvStr=$rvStr.","."useShapes:".$this->_useShapes;
			if($this->_storeFonts)
				$rvStr=$rvStr.","."storeFonts:".$this->_storeFonts;
			if($this->_flatten)
				$rvStr=$rvStr.","."flatten:".$this->_flatten;
			if($this->_poly2Bitmap)
				$rvStr=$rvStr.","."poly2Bitmap:".$this->_poly2Bitmap;
			if($rvStr)
				$rvStr = "SWF(".$rvStr.")";
			return $rvStr;
		}
		
		/* ------------------------------
		 * IsDataSet
		 */
		public function IsDataSet(){
			if(parent::IsDataSet())
				return true;
			if($this->_flashVersion)
				return true;
			if($this->_zoom)
				return true;
			if($this->_zlib)
				return true;
			if($this->_jpegQuality)
				return true;
			if($this->_sameWindow)
				return true;
			if($this->_insertStop)
				return true;
			if($this->_useShapes)
				return true;
			if($this->_storeFonts)
				return true;
			if($this->_flatten)
				return true;
			if($this->_poly2Bitmap)
				return true;
			return false;
		}
		
		/* ------------------------------
		 * sanityCheck
		 */
		public function sanityCheck(array &$errors, array &$warnings, $section=null) {
			;
		}
	}
	
	
	
	/* ---------------------------
	 * PdfData
	 */
	class KDLPdfData extends KDLBaseMediaData {

		/* ---------------------
		 * Data
		 */
		public $_resolution, 
			   $_paperHeight,
			   $_paperWidth,
			   $_readonly;
			   

		public function GetSectionEnum() { return KDLConstants::PdfIndex; }
		
		/* ------------------------------
		 * Validate
		 */
		public function Validate(array &$errors, array &$warnings, $section=null) {
			parent::Validate($errors, $warnings, KDLConstants::PdfIndex);
			self::sanityCheck($errors, $warnings);
			
			if(count($errors)>0)
				return false;
			else
				return true;
		}
		
		/* ---------------------------
		 * ToString
		 */
		public function ToString(){
			$rvStr=parent::ToString();

			if($this->_resolution)
				$rvStr=$rvStr.","."resolution:".$this->_resolution;
			if($this->_paperHeight)
				$rvStr=$rvStr.","."paperHeight:".$this->_paperHeight;
			if($this->_paperWidth)
				$rvStr=$rvStr.","."paperWidth:".$this->_paperWidth;
			if($this->_readonly)
				$rvStr=$rvStr.","."readonly:".$this->_readonly;
				
			if($rvStr)
				$rvStr = "PDF(".$rvStr.")";
			return $rvStr;
		}
		
		/* ------------------------------
		 * IsDataSet
		 */
		public function IsDataSet(){
			if(parent::IsDataSet())
				return true;
			if($this->_resolution)
				return true;
			if($this->_paperHeight)
				return true;
			if($this->_paperWidth)
				return true;
			if($this->_readonly)
				return true;
			return false;
		}
		
		/* ------------------------------
		 * sanityCheck
		 */
		public function sanityCheck(array &$errors, array &$warnings, $section=null) {
			;
		}
	}
	
	/* ---------------------------
	 * ImageData
	 */
	class KDLImageData extends KDLBaseMediaData {

		/* ---------------------
		 * Data
		 */
		public $_densityWidth,
			   $_densityHeight,
			   $_sizeWidth,
			   $_sizeHeight,
			   $_depth;
			   

		public function GetSectionEnum() { return KDLConstants::ImageIndex; }
		
		/* ------------------------------
		 * Validate
		 */
		public function Validate(array &$errors, array &$warnings, $section=null) {
			parent::Validate($errors, $warnings, KDLConstants::ImageIndex);
			self::sanityCheck($errors, $warnings);
			
			if(count($errors)>0)
				return false;
			else
				return true;
		}
		
		/* ---------------------------
		 * ToString
		 */
		public function ToString(){
			$rvStr=parent::ToString();

			if($this->_densityWidth)
				$rvStr=$rvStr.","."densityWidth:".$this->_densityWidth;
			if($this->_densityHeight)
				$rvStr=$rvStr.","."densityHeight:".$this->_densityHeight;
			if($this->_sizeWidth)
				$rvStr=$rvStr.","."sizeWidth:".$this->_sizeWidth;
			if($this->_sizeHeight)
				$rvStr=$rvStr.","."sizeHeight:".$this->_sizeHeight;
			if($this->_depth)
				$rvStr=$rvStr.","."depth:".$this->_depth;
			if($rvStr)
				$rvStr = "IMAGE(".$rvStr.")";
			return $rvStr;
		}
		
		/* ------------------------------
		 * IsDataSet
		 */
		public function IsDataSet(){
			if(parent::IsDataSet())
				return true;
			if($this->_densityWidth)
				return true;
			if($this->_densityHeight)
				return true;
			if($this->_sizeWidth)
				return true;
			if($this->_sizeHeight)
				return true;
			if($this->_depth)
				return true;
			return false;
		}
		
		/* ------------------------------
		 * sanityCheck
		 */
		public function sanityCheck(array &$errors, array &$warnings, $section=null) {
			;
		}
	}



?>
