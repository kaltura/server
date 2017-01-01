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

		KalturaLog::debug("asdf - 1 with [$cuePointId]");
		$metadata = $this->getAdCuePointMetadata($cuePointId);

		if (!$metadata)
			return;
		$key = $metadata->getSyncKey(Metadata::FILE_SYNC_METADATA_DATA);
		$xml = kFileSyncUtils::file_get_contents($key, true, false);
		$this->setMembers($xml);
	}

	private function setMembers($xml)
	{
		$xmlObj = simplexml_load_string($xml);
		$this->width = $xmlObj->width;
		$this->height = $xmlObj->height;
		$this->x = $xmlObj->x;
		$this->y = $xmlObj->y;
		$this->startTime = $xmlObj->startTime;
		$this->duration = $xmlObj->duration;

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