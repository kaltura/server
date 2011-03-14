<?php
include_once("KDLMediaInfoLoader.php");
include_once('KDLProcessor.php');
include_once 'KDLUtils.php';

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
//		"expressionEncoder.ExpressionEncoder"=>KDLTranscoders::EXPRESSION_ENCODER,
		"quickTimeTools.QuickTimeTools"=>KDLTranscoders::QUICK_TIME_PLAYER_TOOLS,
//		"fastStart.FastStart"=>KDLTranscoders::QT_FASTSTART,
//		"avidemux.Avidemux"=>KDLTranscoders::AVIDEMUX,
//		conversionEngineType::PDF2SWF=>KDLTranscoders::PDF2SWF,
//		conversionEngineType::PDF_CREATOR=>KDLTranscoders::PDF_CREATOR,
	);
	
	/* ------------------------------
	 * function CDLGenerateTargetFlavors
	 */
	public static function CDLGenerateTargetFlavors(mediaInfo $cdlMediaInfo=null, $cdlFlavorList)
	{
		$kdlWrap = new KDLWrap();
		return $kdlWrap->generateTargetFlavors($cdlMediaInfo, $cdlFlavorList);
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
		
		foreach ($trgList as $trg){
			KalturaLog::log("...T-->".$trg->ToString());
			$this->_targetList[] = self::ConvertFlavorKdl2Cdl($trg);
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
		if($cdlSourceMediaInfo)
			KDLWrap::ConvertMediainfoCdl2Mediadataset($cdlSourceMediaInfo, $kdlSource);
		
		$kdlTarget->ValidateProduct($kdlSource, $kdlProduct);

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
		return $xml;
	}

	/* ------------------------------
	 * function CDLMediaInfo2Tags
	 */
	public static function CDLMediaInfo2Tags(mediaInfo $cdlMediaInfo, $tagList) 
	{
KalturaLog::log(__METHOD__."==>\n");
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
KalturaLog::log(__METHOD__."==>\n");
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
		$flavor->setType($target->_type);
		$flavor->setReadyBehavior($target->_ready_behavior);
		$flavor->setTags($target->_tags);
		
		$flavor->setName($target->_name);
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
KalturaLog::log(__METHOD__."\noperators==>\n".print_r($cdlOprSets,true));
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
		$kdlFlavor->_ready_behavior = $cdlFlavor->getReadyBehavior();
		$kdlFlavor->_tags = $cdlFlavor->getTags();
		if($cdlFlavor instanceof flavorParams)
		{ 
			$kdlFlavor->_clipStart = $cdlFlavor->getClipOffset();
			$kdlFlavor->_clipDur = $cdlFlavor->getClipDuration();
		}
		
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
KalturaLog::log(__METHOD__."\ntranscoders==>\n".print_r($transObjArr,true));
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
			$kdlFlavor->_swf = new KDLSWFData();
			$kdlFlavor->_swf->_flashVersion = $cdlFlavor->getFlashVersion();
			$kdlFlavor->_swf->_zoom         = $cdlFlavor->getZoom();
			$kdlFlavor->_swf->_zlib         = $cdlFlavor->getZlib();
			$kdlFlavor->_swf->_jpegQuality  = $cdlFlavor->getJpegQuality();
			$kdlFlavor->_swf->_sameWindow   = $cdlFlavor->getSameWindow();
			$kdlFlavor->_swf->_insertStop   = $cdlFlavor->getInsertStop();
			$kdlFlavor->_swf->_useShapes    = $cdlFlavor->getUseShapes();
			$kdlFlavor->_swf->_storeFonts   = $cdlFlavor->getStoreFonts();
			$kdlFlavor->_swf->_flatten      = $cdlFlavor->getFlatten();
		}
		
		if($cdlFlavor instanceof PdfFlavorParams || $cdlFlavor instanceof PdfFlavorParamsOutput) {
			$kdlFlavor->_pdf = new KDLPDFData();
			$kdlFlavor->_pdf->_resolution  = $cdlFlavor->getResolution();
			$kdlFlavor->_pdf->_paperHeight = $cdlFlavor->getPaperHeight();
			$kdlFlavor->_pdf->_paperWidth  = $cdlFlavor->getPaperWidth();
			$kdlFlavor->_pdf->_readonly  = $cdlFlavor->getReadonly();
		}
		
//KalturaLog::log(__METHOD__."\nKDL Flavor==>\n".print_r($kdlFlavor,true));
		return $kdlFlavor;
	}
	
	/* ------------------------------
	 * function ConvertMediainfoCdl2Mediadataset
	 */
	public static function ConvertMediainfoCdl2Mediadataset(mediaInfo $cdlMediaInfo, KDLMediaDataSet &$medSet)
	{
//		KalturaLog::log(__METHOD__."->".$cdlMediaInfo->getRawData());
		$medSet->_container = new KDLContainerData();
		$medSet->_streamsCollectionStr = $cdlMediaInfo->getMultiStreamInfo();
		
		$medSet->_container->_id=$cdlMediaInfo->getContainerId();
		$medSet->_container->_format=$cdlMediaInfo->getContainerFormat();
		$medSet->_container->_duration=$cdlMediaInfo->getContainerDuration();
		$medSet->_container->_bitRate=$cdlMediaInfo->getContainerBitRate();
		$medSet->_container->_fileSize=$cdlMediaInfo->getFileSize();
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
KalturaLog::log(__METHOD__."==>");
KalturaLog::log("\nCDL mediaInfo==>\n".print_r($cdlMediaInfo,true));
/*
$flavorAsset->setWidth($mediaInfoDb->getVideoWidth());
$flavorAsset->setHeight($mediaInfoDb->getVideoHeight());
$flavorAsset->setFrameRate($mediaInfoDb->getVideoFrameRate());
$flavorAsset->setBitrate($mediaInfoDb->getContainerBitRate());
$flavorAsset->setSize($mediaInfoDb->getFileSize());
$flavorAsset->setContainerFormat($mediaInfoDb->getContainerFormat());
$flavorAsset->setVideoCodecId($mediaInfoDb->getVideoCodecId());
*/
  	$medSet = new KDLMediaDataSet();
	self::ConvertMediainfoCdl2Mediadataset($cdlMediaInfo, $medSet);
KalturaLog::log("\nKDL mediaDataSet==>\n".print_r($medSet,true));
	
//	$fla = new flavorAsset();
		if(!is_null($medSet->_container)){
			$fla->setContainerFormat($medSet->_container->GetIdOrFormat());
		}
  		$fla->setSize($cdlMediaInfo->getFileSize());

  	$vidBr = 0;
		if($medSet->_video){
			$fla->setWidth($medSet->_video->_width);
  			$fla->setHeight($medSet->_video->_height);
  			$fla->setFrameRate($medSet->_video->_frameRate);
			$vidBr = $medSet->_video->_bitRate;
			$fla->setVideoCodecId($medSet->_video->GetIdOrFormat());
		}
		if($vidBr==0)
			$fla->setBitrate($medSet->_container->_bitRate);
		else
			$fla->setBitrate($vidBr);

KalturaLog::log("\nCDL fl.Asset==>\n".print_r($fla,true));
		return $fla;
	}

	/* ------------------------------
	 * function convertOperatorsCdl2Kdl
	 */
	public static function convertOperatorsCdl2Kdl($operators)
	{
KalturaLog::log(__METHOD__."\ncdlOperators==>\n".print_r($operators,true));
		$transObjArr = array();
$oprSets = new kOperatorSets();
//		$operators = stripslashes($operators);
//KalturaLog::log(__METHOD__."\ncdlOperators(stripslsh)==>\n".print_r($operators,true));
		$oprSets->setSerialized($operators);
KalturaLog::log(__METHOD__."\noperatorSets==>\n".print_r($oprSets,true));
		foreach ($oprSets->getSets() as $oprSet) {
			if(count($oprSet)==1) {
				$opr = $oprSet[0];
KalturaLog::log(__METHOD__."\n1==>\n".print_r($oprSet,true));
				$kdlOpr = new KDLOperationParams($opr->id, $opr->extra, $opr->command, $opr->config);
				$transObjArr[] = $kdlOpr;
			}
			else {
				$auxArr = array();
				foreach ($oprSet as $opr) {
KalturaLog::log(__METHOD__."\n2==>\n".print_r($oprSet,true));
					$kdlOpr = new KDLOperationParams($opr->id, $opr->extra, $opr->command, $opr->config);
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
KalturaLog::log(__METHOD__.":operators id=$id :");
	switch($id){
		case KDLTranscoders::KALTURA:
		case KDLTranscoders::ON2:
		case KDLTranscoders::FFMPEG:
		case KDLTranscoders::MENCODER:
		case KDLTranscoders::ENCODING_COM:
		case KDLTranscoders::FFMPEG_AUX:
		case KDLTranscoders::FFMPEG_VP8:
		case KDLTranscoders::EE3:
			$oprObj->_engine = new KDLOperatorWrapper($id);
			return;
		case KDLTranscoders::QUICK_TIME_PLAYER_TOOLS:
			$oprObj->_engine = KalturaPluginManager::loadObject('KDLOperatorBase', "quickTimeTools.QuickTimeTools");
			break;
		default:
//		KalturaLog::log("in default :operators id=$id :");
			$oprObj->_engine = KalturaPluginManager::loadObject('KDLOperatorBase', $id);
			break;
	}
/*
	if($id==KDLTranscoders::QUICK_TIME_PLAYER_TOOLS) {
		$oprObj->_engine = KalturaPluginManager::loadObject('KDLOperatorBase', "quickTimeTools.QuickTimeTools");
	}
	else if($id=="fastStart.FastStart") {
		$oprObj->_engine = KalturaPluginManager::loadObject('KDLOperatorBase', $id);
	}
//	else if($id==KDLTranscoders::EXPRESSION_ENCODER) {
//		$oprObj->_engine = KalturaPluginManager::loadObject('KDLOperatorBase', "expressionEncoder.ExpressionEncoder");
//	}
//	else if($id==KDLTranscoders::QT_FASTSTART) {
//		$oprObj->_engine = KalturaPluginManager::loadObject('KDLOperatorBase', "fastStart.FastStart");
//	}
//	else if($id==KDLTranscoders::AVIDEMUX) {
//		$oprObj->_engine = KalturaPluginManager::loadObject(KalturaPluginManager::OBJECT_TYPE_KDL_ENGINE, "avidemux.Avidemux");
//	}
//	else if($id==KDLTranscoders::PDF_CREATOR) {
//		$oprObj->_engine = KalturaPluginManager::loadObject('KDLOperatorBase', conversionEngineType::PDF_CREATOR);
//	}
//	else if($id==KDLTranscoders::PDF2SWF) {
//		$oprObj->_engine = KalturaPluginManager::loadObject('KDLOperatorBase', conversionEngineType::PDF2SWF);
//	}
	else {
		$oprObj->_engine = new KDLOperatorWrapper($id);
		return;
	}
*/
	if(is_null($oprObj->_engine)) {
		KalturaLog::log(__METHOD__.":ERROR - plugin manager returned with null");
	}
	else {
		KalturaLog::log(__METHOD__."Engine object from plugin mgr==>\n".print_r($oprObj->_engine,true));
	}
	return;
}

?>