<?php
/**
 * @package    Core
 * @subpackage system
 * @deprecated
 */
require_once ( __DIR__ . "/kalturaSystemAction.class.php" );

/**
 * @package    Core
 * @subpackage system
 * @deprecated
 */
class flavorParamsAction extends kalturaSystemAction
{
	public function execute()
	{
		$this->forceSystemAuthentication();
		$this->pid = $this->getRequestParameter("pid", 0);
		if (!is_null($this->getRequestParameter("advanced")))
		{
			$this->getResponse()->setCookie('flavor-params-advanced', $this->getRequestParameter("advanced"));
			$this->advanced = (int)$this->getRequestParameter("advanced");
		}
		else
		{
			$this->advanced = (int)$this->getRequest()->getCookie('flavor-params-advanced');
		}
		
		myDbHelper::$use_alternative_con = null;
		$this->editFlavorParam = null;
//		if ($this->getRequestParameter("id"))
		{
			$this->editFlavorParam = assetParamsPeer::retrieveByPK($this->getRequestParameter("id"));
			
			if ($this->getRequestParameter("clone"))
			{
				$newFalvorParams = $this->editFlavorParam->copy();
				$newFalvorParams->setSourceAssetParamsIds($this->editFlavorParam->getSourceAssetParamsIds());
				$newFalvorParams->setChunkedEncodeMode($this->editFlavorParam->getChunkedEncodeMode());
				$newFalvorParams->setAspectRatioProcessingMode($this->editFlavorParam->getAspectRatioProcessingMode());
				$newFalvorParams->setForceFrameToMultiplication16($this->editFlavorParam->getForceFrameToMultiplication16());
				$newFalvorParams->setIsGopInSec($this->editFlavorParam->getIsGopInSec());
				$newFalvorParams->setIsAvoidVideoShrinkFramesizeToSource($this->editFlavorParam->getIsAvoidVideoShrinkFramesizeToSource());
				$newFalvorParams->setIsAvoidVideoShrinkBitrateToSource($this->editFlavorParam->getIsAvoidVideoShrinkBitrateToSource());
				$newFalvorParams->setIsVideoFrameRateForLowBrAppleHls($this->editFlavorParam->getIsVideoFrameRateForLowBrAppleHls());
				$newFalvorParams->setIsAvoidForcedKeyFrames($this->editFlavorParam->getIsAvoidForcedKeyFrames());
				$newFalvorParams->setMultiStream($this->editFlavorParam->getMultiStream());
				$newFalvorParams->setAnamorphicPixels($this->editFlavorParam->getAnamorphicPixels());
				$newFalvorParams->setMaxFrameRate($this->editFlavorParam->getMaxFrameRate());
				$newFalvorParams->setWatermarkData($this->editFlavorParam->getWatermarkData());
				$newFalvorParams->setSubtitlesData($this->editFlavorParam->getSubtitlesData());
				$newFalvorParams->setIsDefault(false);
				$newFalvorParams->setPartnerId(-1);
				$newFalvorParams->save();
				$this->redirect("system/flavorParams?pid=".$this->pid."&id=".$newFalvorParams->getId());
			}
			
			if ($this->getRequestParameter("delete"))
			{
				if ($this->advanced || $this->editFlavorParam->getPartnerId() != 0)
				{
					$this->editFlavorParam->setDeletedAt(time());
					$this->editFlavorParam->save();
				}
				$this->redirect("system/flavorParams?pid=".$this->pid);
			}
			
			if ($this->getRequest()->getMethod() == sfRequest::POST)
			{
				if ($this->advanced || $this->editFlavorParam->getPartnerId() != 0)
				{
					$partnerId = $this->getRequestParameter("partner-id");
					if ($this->advanced)
					{
						$this->editFlavorParam->setPartnerId($partnerId);
					}
					else
					{
						if ($partnerId != 0)
							$this->editFlavorParam->setPartnerId($partnerId);
					}
					
					if ($this->advanced >= 1)
					{
						$this->editFlavorParam->setName($this->getRequestParameter("name"));
						$this->editFlavorParam->setSystemName($this->getRequestParameter("systemName"));
						$this->editFlavorParam->setDescription($this->getRequestParameter("description"));
						$this->editFlavorParam->setIsDefault($this->getRequestParameter("is-default", false));
						$this->editFlavorParam->setReadyBehavior($this->getRequestParameter("ready-behavior"));
						$this->editFlavorParam->setTags($this->getRequestParameter("tags"));
						$this->editFlavorParam->setSourceAssetParamsIds($this->getRequestParameter("sourceAssetParamsIds"));
						$this->editFlavorParam->setFormat($this->getRequestParameter("format"));
						$this->editFlavorParam->setTwoPass($this->getRequestParameter("two-pass", false));
						$this->editFlavorParam->setChunkedEncodeMode($this->getRequestParameter("chunkedEncodeMode",0));
						$this->editFlavorParam->setRotate($this->getRequestParameter("rotate", false));
						$this->editFlavorParam->setAspectRatioProcessingMode($this->getRequestParameter("aspectRatioProcessingMode",0));
						$this->editFlavorParam->setIsGopInSec($this->getRequestParameter("isGopInSec",0));
						$this->editFlavorParam->setForceFrameToMultiplication16($this->getRequestParameter("forceFrameToMultiplication16")?"1":"0");
						$this->editFlavorParam->setIsAvoidVideoShrinkFramesizeToSource($this->getRequestParameter("isAvoidVideoShrinkFramesizeToSource",0));
						$this->editFlavorParam->setIsAvoidVideoShrinkBitrateToSource($this->getRequestParameter("isAvoidVideoShrinkBitrateToSource",0));
						$this->editFlavorParam->setIsVideoFrameRateForLowBrAppleHls($this->getRequestParameter("isVideoFrameRateForLowBrAppleHls",0));
						$this->editFlavorParam->setIsAvoidForcedKeyFrames($this->getRequestParameter("isAvoidForcedKeyFrames",0));
						$this->editFlavorParam->setMultiStream($this->getRequestParameter("multiStream",0));
						$this->editFlavorParam->setAnamorphicPixels($this->getRequestParameter("anamorphicPixels",0));
						$this->editFlavorParam->setWidth($this->getRequestParameter("width"));
						$this->editFlavorParam->setHeight($this->getRequestParameter("height"));
						$this->editFlavorParam->setVideoCodec($this->getRequestParameter("video-codec"));
						$this->editFlavorParam->setVideoBitrate($this->getRequestParameter("video-bitrate"));
						$this->editFlavorParam->setWatermarkData($this->getRequestParameter("watermarkData",0));
						$this->editFlavorParam->setSubtitlesData($this->getRequestParameter("subtitlesData",0));
						$this->editFlavorParam->setFrameRate($this->getRequestParameter("frame-rate"));
						$this->editFlavorParam->setMaxFrameRate($this->getRequestParameter("max-frame-rate"));
						$this->editFlavorParam->setGopSize($this->getRequestParameter("gop-size"));
						$this->editFlavorParam->setAudioCodec($this->getRequestParameter("audio-codec"));
						$this->editFlavorParam->setAudioBitrate($this->getRequestParameter("audio-bitrate"));
						$this->editFlavorParam->setAudioChannels($this->getRequestParameter("audio-channels"));
						$this->editFlavorParam->setAudioSampleRate($this->getRequestParameter("audio-sample-rate"));
						$this->editFlavorParam->setAudioResolution($this->getRequestParameter("audio-resolution"));
						$this->editFlavorParam->setConversionEngines($this->getRequestParameter("conversion-engines"));
						$this->editFlavorParam->setConversionEnginesExtraParams($this->getRequestParameter("conversion-engines-extra-params"));
						$this->editFlavorParam->setOperators($this->getRequestParameter("operators"));
						$this->editFlavorParam->setEngineVersion($this->getRequestParameter("engine-version"));
						$this->editFlavorParam->setType($this->getRequestParameter("type"));
					}
					
					$this->editFlavorParam->save();
				}
				$this->redirect("system/flavorParams?pid=".$this->editFlavorParam->getPartnerId());
			}
		}
			
		$c = new Criteria();
    	$c->add(assetParamsPeer::PARTNER_ID, array(0, intval($this->pid)), Criteria::IN);
		$this->flavorParams = assetParamsPeer::doSelect($c);
		
		$this->formats = self::getEnumValues("flavorParams", "CONTAINER_FORMAT");
		$this->videoCodecs = self::getEnumValues("flavorParams", "VIDEO_CODEC");
		$this->audioCodecs = self::getEnumValues("flavorParams", "AUDIO_CODEC");
		$this->readyBehaviors = self::getEnumValues("flavorParamsConversionProfile", "READY_BEHAVIOR");
		$this->creationModes = self::getEnumValues("flavorParams", "CREATION_MODE");
	}
	
	private function getEnumValues($peer, $prefix)
	{
		$reflectionClass = new ReflectionClass($peer);
		$allConsts = $reflectionClass->getConstants();
		$consts = array();
		foreach($allConsts as $key => $value)
		{
			if (strpos($key, $prefix) === 0)
			{
				$consts[str_replace($prefix.'_', '', $key)] = $value;
			}
		}
		return $consts;
	}
	
	static function getEnumValue($peer, $prefix, $value)
	{
		$reflectionClass = new ReflectionClass($peer);
		$allConsts = $reflectionClass->getConstants();
		foreach($allConsts as $key => $enumVal)
		{
			if ($enumVal == $value)
				return str_replace($prefix.'_', '', $key);
		}
		return '';
	}
}
?>
