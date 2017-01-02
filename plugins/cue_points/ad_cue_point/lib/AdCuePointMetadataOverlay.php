<?php

/**
 * Created by IntelliJ IDEA.
 * User: David.Winder
 * Date: 1/1/2017
 * Time: 4:56 PM
 */
class AdCuePointMetadataOverlay
{
	
	private $width = null;
	private $height = null;
	private $x = null;
	private $y = null;
	private $startTime = null;
	private $duration = null;
	
	
	function __construct($cuePointId)
	{
		$this->setGeneralMembers($cuePointId);
		$metadata = $this->getAdCuePointMetadata($cuePointId);
		if (!$metadata)
		{
			KalturaLog::info("No metadata on cuePoint [$cuePointId]");
			return;
		}
		$key = $metadata->getSyncKey(Metadata::FILE_SYNC_METADATA_DATA);
		$xml = kFileSyncUtils::file_get_contents($key, true, false);
		$this->setVisualMembers($xml);
	}

	private function setVisualMembers($xml)
	{
		$xmlObj = simplexml_load_string($xml);
		$this->width = $xmlObj->width ? $xmlObj->width:0;
		$this->height = $xmlObj->height? $xmlObj->width:0;
		$this->x = $xmlObj->x ? $xmlObj->x:0;
		$this->y = $xmlObj->y ? $xmlObj->y:0;
	}

	private function setGeneralMembers($cuePointId)
	{
		$cuePoint = CuePointPeer::retrieveByPK($cuePointId);
		$this->startTime = $cuePoint->getStartTime() ? $cuePoint->getStartTime():0;
		$this->duration = $cuePoint->getDuration() ? $cuePoint->getDuration():0;
	}
	
	private function getAdCuePointMetadata($cuePointId) {
		$c = new Criteria();
		$objectType = AdCuePointMetadataPlugin::getMetadataObjectTypeCoreValue(AdCuePointMetadataObjectType::AD_CUE_POINT);
		//for code only
		//$objectType = CodeCuePointMetadataPlugin::getMetadataObjectTypeCoreValue(CodeCuePointMetadataObjectType::CODE_CUE_POINT);
		$c->add(MetadataPeer::OBJECT_TYPE, $objectType);
		$c->add(MetadataPeer::OBJECT_ID, $cuePointId);
		return MetadataPeer::doSelectOne($c);
	}

	public function getWidth() {
		return $this->width;
	}
	public function getHeight() {
		return $this->height;

	}
	public function getX() {
		return $this->x;
	}
	public function getY() {
		return $this->y;
	}
	public function getStartTime() {
		return $this->startTime;
	}
	public function getDuration() {
		return $this->duration;
	}
	
	
	
}