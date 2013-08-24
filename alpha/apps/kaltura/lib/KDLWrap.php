<?php
//include_once("KDLMediaInfoLoader.php");
//include_once('KDLProcessor.php');
//include_once 'KDLUtils.php';

	/* ===========================
	 * KDLWrap
	 */
class KDLWrap
{
	public 	$_targetList = array();
	public	$_errors = array();
	public	$_warnings = array();
	public  $_rv=true;

	static $TranscodersCdl2Kdl = array(
		conversionEngineType::KALTURA_COM=>KDLTranscoders::KALTURA,
		conversionEngineType::ON2=>KDLTranscoders::ON2,
		conversionEngineType::FFMPEG=>KDLTranscoders::FFMPEG,
		conversionEngineType::MENCODER=>KDLTranscoders::MENCODER,
		conversionEngineType::ENCODING_COM=>KDLTranscoders::ENCODING_COM,
		conversionEngineType::FFMPEG_AUX=>KDLTranscoders::FFMPEG_AUX,
		conversionEngineType::FFMPEG_VP8=>KDLTranscoders::FFMPEG_VP8,
		conversionEngineType::EXPRESSION_ENCODER3=>KDLTranscoders::EE3,
			
		"quickTimeTools.QuickTimeTools"=>KDLTranscoders::QUICK_TIME_PLAYER_TOOLS,
	);
	
	/* ------------------------------
	 * function CDLGenerateTargetFlavors
	 */
	public static function CDLGenerateTargetFlavors($cdlMediaInfo=null, $cdlFlavorList)
	{
		$kdlWrap = new KDLWrap();
		if(!isset($cdlMediaInfo) || is_array($cdlMediaInfo)) {
			return $kdlWrap->generateTargetFlavors(null, $cdlFlavorList);
		}
		else if(get_class($cdlMediaInfo)=='mediaInfo') {
			return $kdlWrap->generateTargetFlavors($cdlMediaInfo, $cdlFlavorList);
		}
		else {
			throw new Exception("Bad argument (".get_class($cdlMediaInfo)."), should be mediaInfo class");
		}
	}
	
	/* ------------------------------
	 * function CDLGenerateTargetFlavorsCmdLinesOnly
	 */
	public static function CDLGenerateTargetFlavorsCmdLinesOnly($fileSizeKb, $cdlFlavorList)
	{
		$kdlWrap = new KDLWrap();
		if($fileSizeKb<KDLSanityLimits::MinFileSize) {
			$kdlWrap->_rv = false;
			$kdlWrap->_errors[KDLConstants::ContainerIndex][] = KDLErrors::ToString(KDLErrors::SanityInvalidFileSize, $fileSizeKb);
			return $kdlWrap;
		}
		return $kdlWrap->generateTargetFlavors(null, $cdlFlavorList);
	}
	
	/* ------------------------------
	 * function GenerateIntermediateSource
	 */
	public static function GenerateIntermediateSource(mediaInfo $cdlMediaInfo, $cdlFlavorList=null)
	{
		$mediaSet = new KDLMediaDataSet();
		self::ConvertMediainfoCdl2Mediadataset($cdlMediaInfo, $mediaSet);
		
		KalturaLog::log( "...S-->".$mediaSet->ToString());

		$profile = null;
		if(isset($cdlFlavorList)) {
			$profile = new KDLProfile();
			foreach($cdlFlavorList as $cdlFlavor) {
				$kdlFlavor = self::ConvertFlavorCdl2Kdl($cdlFlavor);
				$profile->_flavors[] = $kdlFlavor;
				KalturaLog::log( "...F-->".$kdlFlavor->ToString());
			}
		}
		
		$dlPrc = new KDLProcessor();
		
		$interSrc = $dlPrc->GenerateIntermediateSource($mediaSet, $profile);
		if(!isset($interSrc))
			return null;
		
		return self::ConvertFlavorKdl2Cdl($interSrc);
	}
	
	/* ------------------------------
	 * function generateTargetFlavors
	 */
	private function generateTargetFlavors(mediaInfo $cdlMediaInfo=null, $cdlFlavorList)
	{
		$mediaSet = new KDLMediaDataSet();
		if($cdlMediaInfo!=null) {
			self::ConvertMediainfoCdl2Mediadataset($cdlMediaInfo, $mediaSet);
		}
		KalturaLog::log( "...S-->".$mediaSet->ToString());
		
		$profile = new KDLProfile();
		foreach($cdlFlavorList as $cdlFlavor) {
			$kdlFlavor = self::ConvertFlavorCdl2Kdl($cdlFlavor);
			if ($kdlFlavor->_errors)
			{
				$this->_rv = false;
				return $this;
			}
			$profile->_flavors[] = $kdlFlavor;
			KalturaLog::log( "...F-->".$kdlFlavor->ToString());
		}
				
		$trgList = array();
		{
			$dlPrc = new KDLProcessor();

			$dlPrc->Generate($mediaSet, $profile, $trgList);
			$this->_errors   = $this->_errors   + $dlPrc->get_errors();
			$this->_warnings = $this->_warnings + $dlPrc->get_warnings();
			if(count($this->_errors)>0)
				$this->_rv = false;
			else
				$this->_rv = true;
		}
			/*
			 * For 'passthrough' quick&dirty
			 
		if(isset($mediaSet->_container) && $mediaSet->_container->_id=="arf")
			$isArf = true;
		else
			$isArf = false;
			*/
		foreach ($trgList as $trg){
			KalturaLog::log("...T-->".$trg->ToString());
			$cdlFlvrOut = self::ConvertFlavorKdl2Cdl($trg);
			
			/*
			 * 'passthrough' temporal, quick&dirty implementation to support imitation of 
			 * 'auto-inter-src' for webex/arf.
			 
			if($isArf==false && isset($trg->_transcoders)
			&& $trg->_transcoders[0]->_id=="webexNbrplayer.WebexNbrplayer"){
				$cdlFlvrOut->_passthrough=true;
			}*/
			$this->_targetList[] = $cdlFlvrOut;
		}
		return $this;
	}

	/* ------------------------------
	 * function CDLValidateProduct
	 */
	public static function CDLValidateProduct(mediaInfo $cdlSourceMediaInfo=null, flavorParamsOutput $cdlTarget, mediaInfo $cdlProductMediaInfo)
	{
		$kdlProduct = new KDLFlavor();
		KDLWrap::ConvertMediainfoCdl2Mediadataset($cdlProductMediaInfo, $kdlProduct);
		$kdlTarget = KDLWrap::ConvertFlavorCdl2Kdl($cdlTarget);
		$kdlSource = new KDLFlavor();
		// Do not run product validation when the source is undefined
		// in most cases - ForceCommand case
		if($cdlSourceMediaInfo){
			KDLWrap::ConvertMediainfoCdl2Mediadataset($cdlSourceMediaInfo, $kdlSource);
			$kdlTarget->ValidateProduct($kdlSource, $kdlProduct);
		}
		$product = KDLWrap::ConvertFlavorKdl2Cdl($kdlProduct);
		return $product;
	}

	/* ------------------------------
	 * function CDLProceessFlavorsForCollection
	 */
	public static function CDLProceessFlavorsForCollection($cdlFlavorList)
	{

		$kdlFlavorList = array();
		foreach($cdlFlavorList as $cdlFlavor) {
			$kdlFlavor = KDLWrap::ConvertFlavorCdl2Kdl($cdlFlavor);
			$kdlFlavorList[]=$kdlFlavor;
		}
		
		$xml=KDLProcessor::ProceessFlavorsForCollection($kdlFlavorList);
		KalturaLog::log(__METHOD__."-->".$xml."<--");
		foreach ($kdlFlavorList as $kdlFlavor){
			$kdlFlavor->_cdlObject->setVideoBitrate($kdlFlavor->_video->_bitRate);
		}
		return $xml;
	}

	/* ------------------------------
	 * function CDLMediaInfo2Tags
	 */
	public static function CDLMediaInfo2Tags(mediaInfo $cdlMediaInfo, $tagList) 
	{
		KalturaLog::log("==>\n");
		$mediaSet = new KDLMediaDataSet();
		self::ConvertMediainfoCdl2Mediadataset($cdlMediaInfo, $mediaSet);
		KalturaLog::log( "...S-->".$mediaSet->ToString());
		$tagsOut = array();
		$tagsOut = $mediaSet->ToTags($tagList);
		return $tagsOut;
	}
	
	/* ------------------------------
	 * function CDLIsFLV
	 */
	public static function CDLIsFLV(mediaInfo $cdlMediaInfo) 
	{
		KalturaLog::log("==>\n");
		$tagList[] = "flv";
		$mediaSet = new KDLMediaDataSet();
		self::ConvertMediainfoCdl2Mediadataset($cdlMediaInfo, $mediaSet);
		KalturaLog::log("...S-->".$mediaSet->ToString());
		$tagsOut = array();
		$tagsOut = $mediaSet->ToTags($tagList);
		if(count($tagsOut)==1) {
			KalturaLog::log("... an FLV file");
			return true;
		}
		else {
			KalturaLog::log("... NOT an FLV file");
			return false;
		}
	}
	
	/* ------------------------------
	 * function ConvertFlavorKdl2Cdl
	 */
	public static function ConvertFlavorKdl2Cdl(KDLFlavor $target){
		$flavor = new flavorParamsOutputWrap();

		$flavor->setFlavorParamsId($target->_id);
		$flavor->setName($target->_name);
		$flavor->setType($target->_type);
		$flavor->setTags($target->_tags);
		
		if($target->_cdlObject)
		{
			$flavor->setReadyBehavior($target->_cdlObject->getReadyBehavior());
			$flavor->setSourceRemoteStorageProfileId($target->_cdlObject->getSourceRemoteStorageProfileId());
			$flavor->setRemoteStorageProfileIds($target->_cdlObject->getRemoteStorageProfileIds());
			$flavor->setMediaParserType($target->_cdlObject->getMediaParserType());
			$flavor->setSourceAssetParamsIds($target->_cdlObject->getSourceAssetParamsIds());
		}
		
		if($target->IsRedundant()) {
			$flavor->_isRedundant = true;
		}
		else {
			$flavor->_isRedundant = false;
		}
		
		if($target->IsNonComply()) {
			$flavor->_isNonComply = true;
		}
		else {
			$flavor->_isNonComply = false;
		}
		if($target->_clipStart)
			$flavor->setClipOffset($target->_clipStart);
		if($target->_clipDur)
			$flavor->setClipDuration($target->_clipDur);
/*
		if(isset($target->_multiStream))
		{
			$toJson = json_encode($target->_multiStream);
			$flavor->setMultiStream($toJson);
		}
*/
		$flavor->_errors   = $flavor->_errors + $target->_errors;
		$flavor->_warnings = $flavor->_warnings + $target->_warnings;
		
		if($target->_container)
			$flavor->setFormat($target->_container->GetIdOrFormat());
		
		if($target->_video) {
			//echo "\n target->_video - "; print_r($target->_video); echo "\n";
			$flavor->setVideoCodec($target->_video->GetIdOrFormat());
			$flavor->setVideoBitrate($target->_video->_bitRate);
			$flavor->setWidth($target->_video->_width);
			$flavor->setHeight($target->_video->_height);
			$flavor->setFrameRate($target->_video->_frameRate);
			$flavor->setGopSize($target->_video->_gop);
			if($target->_video->_arProcessingMode)
				$flavor->setAspectRatioProcessingMode($target->_video->_arProcessingMode);
			if($target->_video->_forceMult16)
				$flavor->setForceFrameToMultiplication16($target->_video->_forceMult16);
		}

		if($target->_audio) {
			$flavor->setAudioCodec($target->_audio->GetIdOrFormat());
			$flavor->setAudioBitrate($target->_audio->_bitRate);
			$flavor->setAudioChannels($target->_audio->_channels);
			$flavor->setAudioSampleRate($target->_audio->_sampleRate);
		}
		
		if($target->_pdf)
			$flavor->putInCustomData('readonly',$target->_pdf->_readonly);
		
		$cdlOprSets = KDLWrap::convertOperatorsKdl2Cdl($target->_transcoders);
		if($target->_engineVersion==1) {
			KalturaLog::log("\noperators==>\n".print_r($cdlOprSets,true));
			$flavor->setOperators($cdlOprSets->getSerialized());
			$flavor->setEngineVersion(1);
		}
		else {
			$flavor->setEngineVersion(0);
			$convEnginesAssociated = null;
			$commandLines = array();
			foreach($target->_transcoders as $key => $transObj) {
				$extra = $transObj->_extra;
	
					/* -------------------------
					 * Translate KDL transcoders enums to CDL
					 */
				$str = null;
				$cdlTrnsId=array_search($transObj->_id,self::$TranscodersCdl2Kdl);
				if($cdlTrnsId!==false){
					$str = $cdlTrnsId;
					$commandLines[$cdlTrnsId]=$transObj->_cmd;
				}
				
					// Add qt-faststart processing for mp4 targets (relevant to pre-opertors mode) 
				if($flavor->getFormat()=="mp4" && ($cdlTrnsId==conversionEngineType::FFMPEG || $cdlTrnsId==conversionEngineType::FFMPEG_AUX || $cdlTrnsId==conversionEngineType::MENCODER)){
					$fsAddonStr = kConvertJobData::CONVERSION_MILTI_COMMAND_LINE_SEPERATOR.kConvertJobData::CONVERSION_FAST_START_SIGN;
					$commandLines[$cdlTrnsId].=$fsAddonStr;
				}
				
				if($convEnginesAssociated!==null) {
					$convEnginesAssociated = $convEnginesAssociated.",".$str;
				}
				else {
					$convEnginesAssociated = $str;
				}					
	//echo "transcoder-->".$key." flag:".$flag." str:".$trnsStr."<br>\n";
				
			}
			$flavor->setCommandLines($commandLines);
			$flavor->setConversionEngines($convEnginesAssociated);
		}
		$flavor->setFileExt($target->EvaluateFileExt());
		$flavor->_errors = $flavor->_errors + $target->_errors;
		//echo "target errs "; print_r($target->_errors);
		//echo "flavor errs "; print_r($flavor->_errors);
		$flavor->_warnings = $flavor->_warnings + $target->_warnings;
		//echo "target wrns "; print_r($target->_warnings);
		//echo "flavor wrns "; print_r($flavor->_warnings);
		
		//echo "flavor "; print_r($flavor);
		
		//KalturaLog::log(__METHOD__."\nflavorOutputParams==>\n".print_r($flavor,true));
		return $flavor;
	}
	
	/* ------------------------------
	 * function ConvertFlavorCdl2Kdl
	 */
	public static function ConvertFlavorCdl2Kdl($cdlFlavor)
	{
		$kdlFlavor = new KDLFlavor();
		
		$kdlFlavor->_name = $cdlFlavor->getName();
		$kdlFlavor->_id = $cdlFlavor->getId();
		$kdlFlavor->_type = $cdlFlavor->getType();
		$kdlFlavor->_tags = $cdlFlavor->getTags();
		if($cdlFlavor instanceof flavorParams || $cdlFlavor instanceof flavorParamsOutput)
		{ 
			$kdlFlavor->_clipStart = $cdlFlavor->getClipOffset();
			$kdlFlavor->_clipDur = $cdlFlavor->getClipDuration();
/*
			$multiStream = $cdlFlavor->getMultiStream();
			if(isset($multiStream)) {
				$fromJson = json_decode($multiStream);
				$kdlFlavor->_multiStream = isset($fromJson)? $fromJson: null;
			}
*/
		}
		
		$kdlFlavor->_cdlObject = $cdlFlavor;
			/* 
			 * Media container initialization
			 */	
		{
			$kdlFlavor->_container = new KDLContainerData();
			$kdlFlavor->_container->_id=$cdlFlavor->getFormat();
	//		$kdlFlavor->_container->_duration=$api->getContainerDuration();
	//		$kdlFlavor->_container->_bitRate=$api->getContainerBitRate();
	//		$kdlFlavor->_container->_fileSize=$api->getFileSize();
			if($kdlFlavor->_container->IsDataSet()==false)
				$kdlFlavor->_container = null;
		}
			/* 
			 * Video stream initialization
			 */	
		{
			$kdlFlavor->_video = new KDLVideoData();
			$kdlFlavor->_video->_id = $cdlFlavor->getVideoCodec();
	//		$kdlFlavor->_video->_format = $api->getVideoFormat();
	//		$kdlFlavor->_video->_duration = $api->getVideoDuration();
			$kdlFlavor->_video->_bitRate = $cdlFlavor->getVideoBitRate();
			$kdlFlavor->_video->_width = $cdlFlavor->getWidth();
			$kdlFlavor->_video->_height = $cdlFlavor->getHeight();
			$kdlFlavor->_video->_frameRate = $cdlFlavor->getFrameRate();
			$kdlFlavor->_video->_gop = $cdlFlavor->getGopSize();
			$kdlFlavor->_isTwoPass = $cdlFlavor->getTwoPass();
			$kdlFlavor->_video->_arProcessingMode = $cdlFlavor->getAspectRatioProcessingMode();
			$kdlFlavor->_video->_forceMult16 = $cdlFlavor->getForceFrameToMultiplication16();
			if($cdlFlavor instanceof flavorParams) {
				$kdlFlavor->_video->_cbr = $cdlFlavor->getVideoConstantBitrate();
				$kdlFlavor->_video->_bt = $cdlFlavor->getVideoBitrateTolerance();
				$kdlFlavor->_video->_isGopInSec = $cdlFlavor->getIsGopInSec();
				$kdlFlavor->_video->_isShrinkFramesizeToSource = !$cdlFlavor->getIsAvoidVideoShrinkFramesizeToSource();
				$kdlFlavor->_video->_isShrinkBitrateToSource   = !$cdlFlavor->getIsAvoidVideoShrinkBitrateToSource();
				$kdlFlavor->_video->_isFrameRateForLowBrAppleHls = $cdlFlavor->getIsVideoFrameRateForLowBrAppleHls();
				$kdlFlavor->_video->_anamorphic = $cdlFlavor->getAnamorphicPixels();
				$kdlFlavor->_video->_maxFrameRate = $cdlFlavor->getMaxFrameRate();
				$kdlFlavor->_video->_isForcedKeyFrames = !$cdlFlavor->getIsAvoidForcedKeyFrames();
			}
			//		$flavor->_video->_dar = $api->getVideoDar();
			if($kdlFlavor->_video->IsDataSet()==false)
				$kdlFlavor->_video = null;
		}
		
			/* 
			 * Audio stream initialization
			 */	
		{
			$kdlFlavor->_audio = new KDLAudioData();
			$kdlFlavor->_audio->_id = $cdlFlavor->getAudioCodec();
	//		$flavor->_audio->_format = $cdlFlavor->getAudioFormat();
	//		$flavor->_audio->_duration = $cdlFlavor->getAudioDuration();
			$kdlFlavor->_audio->_bitRate = $cdlFlavor->getAudioBitRate();
			$kdlFlavor->_audio->_channels = $cdlFlavor->getAudioChannels();
			$kdlFlavor->_audio->_sampleRate = $cdlFlavor->getAudioSampleRate();
			$kdlFlavor->_audio->_resolution = $cdlFlavor->getAudioResolution();
			if($kdlFlavor->_audio->IsDataSet()==false)
				$kdlFlavor->_audio = null;
		}
		$operators = $cdlFlavor->getOperators();
		$transObjArr = array();
		//KalturaLog::log(__METHOD__."\nCDL Flavor==>\n".print_r($cdlFlavor,true));
		if(!empty($operators) || $cdlFlavor->getEngineVersion()==1) {
			$transObjArr = KDLWrap::convertOperatorsCdl2Kdl($operators);
			$kdlFlavor->_engineVersion = 1;
		}
		else {
			$kdlFlavor->_engineVersion = 0;
			$trnsStr = $cdlFlavor->getConversionEngines();
			$extraStr = $cdlFlavor->getConversionEnginesExtraParams();
			$transObjArr=KDLUtils::parseTranscoderList($trnsStr, $extraStr);
			if($cdlFlavor instanceof flavorParamsOutputWrap || $cdlFlavor instanceof flavorParamsOutput) {
				$cmdLines = $cdlFlavor->getCommandLines();
				foreach($transObjArr as $transObj){
					$transObj->_cmd = $cmdLines[$transObj->_id];
				}
			}
			KalturaLog::log("\ntranscoders==>\n".print_r($transObjArr,true));
		}

		KDLUtils::RecursiveScan($transObjArr, "transcoderSetFuncWrap", self::$TranscodersCdl2Kdl, "");
		$kdlFlavor->_transcoders = $transObjArr;
		
		if($cdlFlavor instanceof flavorParamsOutputWrap) {
			if($cdlFlavor->_isRedundant) {
				$kdlFlavor->_flags = $kdlFlavor->_flags | KDLFlavor::RedundantFlagBit;
			}
			if($cdlFlavor->_isNonComply) {
				$kdlFlavor->_flags = $kdlFlavor->_flags | KDLFlavor::BitrateNonComplyFlagBit;
			}
			$kdlFlavor->_errors = $kdlFlavor->_errors + $cdlFlavor->_errors;
			$kdlFlavor->_warnings = $kdlFlavor->_warnings + $cdlFlavor->_warnings;
		}
		
		if($cdlFlavor instanceof SwfFlavorParams || $cdlFlavor instanceof SwfFlavorParamsOutput) {
			$kdlFlavor->_swf = new KDLSwfData();
			$kdlFlavor->_swf->_flashVersion = $cdlFlavor->getFlashVersion();
			$kdlFlavor->_swf->_zoom         = $cdlFlavor->getZoom();
			$kdlFlavor->_swf->_zlib         = $cdlFlavor->getZlib();
			$kdlFlavor->_swf->_jpegQuality  = $cdlFlavor->getJpegQuality();
			$kdlFlavor->_swf->_sameWindow   = $cdlFlavor->getSameWindow();
			$kdlFlavor->_swf->_insertStop   = $cdlFlavor->getInsertStop();
			$kdlFlavor->_swf->_useShapes    = $cdlFlavor->getUseShapes();
			$kdlFlavor->_swf->_storeFonts   = $cdlFlavor->getStoreFonts();
			$kdlFlavor->_swf->_flatten      = $cdlFlavor->getFlatten();
			$kdlFlavor->_swf->_poly2Bitmap	= $cdlFlavor->getPoly2bitmap();
		}
		
		if($cdlFlavor instanceof PdfFlavorParams || $cdlFlavor instanceof PdfFlavorParamsOutput) {
			$kdlFlavor->_pdf = new KDLPdfData();
			$kdlFlavor->_pdf->_resolution  = $cdlFlavor->getResolution();
			$kdlFlavor->_pdf->_paperHeight = $cdlFlavor->getPaperHeight();
			$kdlFlavor->_pdf->_paperWidth  = $cdlFlavor->getPaperWidth();
			$kdlFlavor->_pdf->_readonly  = $cdlFlavor->getReadonly();
		}
		if($cdlFlavor instanceof ImageFlavorParams || $cdlFlavor instanceof ImageFlavorParamsOutput) {
			$kdlFlavor->_image = new KDLImageData();
			$kdlFlavor->_image->_densityWidth = $cdlFlavor->getDensityWidth();
			$kdlFlavor->_image->_densityHeight = $cdlFlavor->getDensityHeight();
			$kdlFlavor->_image->_sizeWidth = $cdlFlavor->getSizeWidth();
			$kdlFlavor->_image->_sizeHeight = $cdlFlavor->getSizeHeight();
			$kdlFlavor->_image->_depth = $cdlFlavor->getDepth();
			$kdlFlavor->_image->_format = $cdlFlavor->getFormat();
		}
		
		
		//KalturaLog::log(__METHOD__."\nKDL Flavor==>\n".print_r($kdlFlavor,true));
		if(is_null($kdlFlavor->_container))
		{
			KalturaLog::log("No Container Found On Flavor Convert Will Fail");
			$kdlFlavor->_errors[KDLConstants::ContainerIndex][] = KDLErrors::ToString(KDLErrors::InvalidFlavorParamConfiguration);
		}
		return $kdlFlavor;
	}
	
	/* ------------------------------
	 * function ConvertMediainfoCdl2Mediadataset
	 */
	public static function ConvertMediainfoCdl2Mediadataset(mediaInfo $cdlMediaInfo, KDLMediaDataSet &$medSet)
	{
		$medSet->_container = new KDLContainerData();
/*
		$multiStream = $cdlMediaInfo->getMultiStream();
		if(isset($multiStream)) {
			$fromJson = json_decode($multiStream);
			$medSet->_multiStream = isset($fromJson)? $fromJson: null;
		}
*/
		$medSet->_container->_id=$cdlMediaInfo->getContainerId();
		$medSet->_container->_format=$cdlMediaInfo->getContainerFormat();
		$medSet->_container->_duration=$cdlMediaInfo->getContainerDuration();
		$medSet->_container->_bitRate=$cdlMediaInfo->getContainerBitRate();
		$medSet->_container->_fileSize=$cdlMediaInfo->getFileSize();
//		$medSet->_container->_isFastStart=$cdlMediaInfo->getIsFastStart();
		if($medSet->_container->IsDataSet()==false)
			$medSet->_container = null;

		$medSet->_video = new KDLVideoData();
		$medSet->_video->_id = $cdlMediaInfo->getVideoCodecId();
		$medSet->_video->_format = $cdlMediaInfo->getVideoFormat();
		$medSet->_video->_duration = $cdlMediaInfo->getVideoDuration();
		$medSet->_video->_bitRate = $cdlMediaInfo->getVideoBitRate();
		$medSet->_video->_width = $cdlMediaInfo->getVideoWidth();
		$medSet->_video->_height = $cdlMediaInfo->getVideoHeight();
		$medSet->_video->_frameRate = $cdlMediaInfo->getVideoFrameRate();
		$medSet->_video->_dar = $cdlMediaInfo->getVideoDar();
		$medSet->_video->_rotation = $cdlMediaInfo->getVideoRotation();
		$medSet->_video->_scanType = $cdlMediaInfo->getScanType();
/*		{
				$medLoader = new KDLMediaInfoLoader($cdlMediaInfo->getRawData());
				$md = new KDLMediadataset();
				$medLoader->Load($md);
				if($md->_video)
					$medSet->_video->_scanType = $md->_video->_scanType;
		}
*/
		if($medSet->_video->IsDataSet()==false)
			$medSet->_video = null;

		$medSet->_audio = new KDLAudioData();
		$medSet->_audio->_id = $cdlMediaInfo->getAudioCodecId();
		$medSet->_audio->_format = $cdlMediaInfo->getAudioFormat();
		$medSet->_audio->_duration = $cdlMediaInfo->getAudioDuration();
		$medSet->_audio->_bitRate = $cdlMediaInfo->getAudioBitRate();
		$medSet->_audio->_channels = $cdlMediaInfo->getAudioChannels();
		$medSet->_audio->_sampleRate = $cdlMediaInfo->getAudioSamplingRate();
		$medSet->_audio->_resolution = $cdlMediaInfo->getAudioResolution();
		if($medSet->_audio->IsDataSet()==false)
			$medSet->_audio = null;

		return $medSet;
	}

	/* ------------------------------
	 * function ConvertMediainfoCdl2Mediadataset
	 */
	public static function ConvertMediainfoCdl2FlavorAsset(mediaInfo $cdlMediaInfo, flavorAsset &$fla)
	{
		KalturaLog::log("==>");
		KalturaLog::log("\nCDL mediaInfo==>\n".print_r($cdlMediaInfo,true));
	  	$medSet = new KDLMediaDataSet();
		self::ConvertMediainfoCdl2Mediadataset($cdlMediaInfo, $medSet);
		KalturaLog::log("\nKDL mediaDataSet==>\n".print_r($medSet,true));

		$contBr = 0;
		if(isset($medSet->_container)){
			$fla->setContainerFormat($medSet->_container->GetIdOrFormat());
			$contBr = $medSet->_container->_bitRate;
		}
  		$fla->setSize($cdlMediaInfo->getFileSize());

		$vidBr = 0;
		if(isset($medSet->_video)){
			$fla->setWidth($medSet->_video->_width);
  			$fla->setHeight($medSet->_video->_height);
  			$fla->setFrameRate($medSet->_video->_frameRate);
			$vidBr = $medSet->_video->_bitRate;
			$fla->setVideoCodecId($medSet->_video->GetIdOrFormat());
		}
		$audBr = 0;
		if(isset($medSet->_audio)){
			$audBr = $medSet->_audio->_bitRate;
		}
		/*
		 * Evaluate the asset br.
		 * Prevously it was taken from video, if t was available.
		 */
		$assetBr = max($contBr,$vidBr+$audBr);
		$fla->setBitrate($assetBr);

		KalturaLog::log("\nCDL fl.Asset==>\n".print_r($fla,true));
		return $fla;
	}

	/* ------------------------------
	 * function convertOperatorsCdl2Kdl
	 */
	public static function convertOperatorsCdl2Kdl($operators)
	{
		KalturaLog::log("\ncdlOperators==>\n".print_r($operators,true));
		$transObjArr = array();
		$oprSets = new kOperatorSets();
		//		$operators = stripslashes($operators);
		//KalturaLog::log(__METHOD__."\ncdlOperators(stripslsh)==>\n".print_r($operators,true));
		$oprSets->setSerialized($operators);
		KalturaLog::log("\noperatorSets==>\n".print_r($oprSets,true));
		foreach ($oprSets->getSets() as $oprSet) {
			if(count($oprSet)==1) {
				$opr = $oprSet[0];
				KalturaLog::log("\n1==>\n".print_r($oprSet,true));
				$kdlOpr = new KDLOperationParams($opr);
				$transObjArr[] = $kdlOpr;
			}
			else {
				$auxArr = array();
				foreach ($oprSet as $opr) {
					KalturaLog::log("\n2==>\n".print_r($oprSet,true));
					$kdlOpr = new KDLOperationParams($opr);
					$auxArr[] = $kdlOpr;
				}
				$transObjArr[] = $auxArr;
			}
		}
		return $transObjArr;
	}

	/* ------------------------------
	 * function convertOperatorKdl2Cdl
	 */
	public static function convertOperatorKdl2Cdl($kdlOperator, $id=null)
	{
		$opr = new kOperator();
		if(!$id || $id===false)
			$opr->id = $kdlOperator->_id;
		else
			$opr->id = $id;
		
		$opr->extra = $kdlOperator->_extra;
		$opr->command = $kdlOperator->_cmd;
		$opr->config = $kdlOperator->_cfg;
		$opr->params = $kdlOperator->_params;
		$opr->isOptional = $kdlOperator->_isOptional;
		return $opr;
	}
	
	/* ------------------------------
	 * function convertOperatorsKdl2Cdl
	 */
	public static function convertOperatorsKdl2Cdl($kdlOperators)
	{
	$cdlOprSets = new kOperatorSets();
		foreach($kdlOperators as $transObj) {
			$auxArr = array();
			if(is_array($transObj)) {
				foreach($transObj as $tr) {
					$key=array_search($tr->_id,self::$TranscodersCdl2Kdl);
//					$opr = new kOperator();
//					if($key===false)
//						$opr->id = $tr->_id;
//					else
//						$opr->id = $key;
//					$opr->extra = $tr->_extra;
//					$opr->command = $tr->_cmd;
//					$opr->config = $tr->_cfg;
//					$auxArr[] = $opr;
					$auxArr[] = KDLWrap::convertOperatorKdl2Cdl($tr, $key);
				}
			}
			else {
				$key=array_search($transObj->_id,self::$TranscodersCdl2Kdl);
//				$opr = new kOperator();
//				if($key===false)
//					$opr->id = $transObj->_id;
//				else
//					$opr->id = $key;
//				$opr->extra = $transObj->_extra;
//				$opr->command = $transObj->_cmd;
//				$opr->config = $transObj->_cfg;
//				$auxArr[] = $opr;
				$auxArr[] = KDLWrap::convertOperatorKdl2Cdl($transObj, $key);
			}
			$cdlOprSets->addSet($auxArr);
		}
		return $cdlOprSets;
	}
}

	/* ===========================
	 * flavorParamsOutputWrap
	 */
class flavorParamsOutputWrap extends flavorParamsOutput {

	/* ---------------------
	 * Data
	 */
	public  $_isRedundant=false;
	public 	$_isNonComply=false;
	public 	$_force=false;
	public	$_create_anyway=false;
	public	$_passthrough = false;		// true: skip execution of this engine,use the source for output.
	
	public  $_errors=array(),
			$_warnings=array();

	/* ------------------------------
	 * IsValid
	 */
	public function IsValid()
	{
		return (count($this->_errors)==0);
	}
		
}

		/* ---------------------------
		 * transcoderSetFuncWrap
		 */
function transcoderSetFuncWrap($oprObj, $transDictionary, $param2)
{
	$trId = KDLUtils::trima($oprObj->_id);
	if(!is_null($transDictionary) && array_key_exists($trId, $transDictionary)){
		$oprObj->_id = $transDictionary[$trId];
	}

//	$oprObj->_engine = KDLWrap::GetEngineObject($oprObj->_id);
	$id = $oprObj->_id;
	KalturaLog::log(":operators id=$id :");
	$engine=null;
	if(isset($oprObj->_className) && class_exists($oprObj->_className)){
		try {
			$engine = new $oprObj->_className($id);
		}
		catch(Exception $e){
			$engine=null;
		}
	}
	
	if(isset($engine)) {
		KalturaLog::log(__METHOD__.": the engine was successfully overloaded with $oprObj->_className");
	}
	else {
		switch($id){
		case KDLTranscoders::KALTURA:
		case KDLTranscoders::ON2:
		case KDLTranscoders::FFMPEG:
		case KDLTranscoders::MENCODER:
		case KDLTranscoders::ENCODING_COM:
		case KDLTranscoders::FFMPEG_AUX:
		case KDLTranscoders::FFMPEG_VP8:
		case KDLTranscoders::EE3:
			$engine = new KDLOperatorWrapper($id);
			break;
		case KDLTranscoders::QUICK_TIME_PLAYER_TOOLS:
			$engine = KalturaPluginManager::loadObject('KDLOperatorBase', "quickTimeTools.QuickTimeTools");
			break;
		default:
//		KalturaLog::log("in default :operators id=$id :");
			$engine = KalturaPluginManager::loadObject('KDLOperatorBase', $id);
			break;
		}
	}

	if(is_null($engine)) {
		KalturaLog::log(__METHOD__.":ERROR - plugin manager returned with null");
	}
	else {
		$oprObj->_engine = $engine;
		KalturaLog::log(__METHOD__."Engine object from plugin mgr==>\n".print_r($oprObj->_engine,true));
	}
	
	return;
}

?>