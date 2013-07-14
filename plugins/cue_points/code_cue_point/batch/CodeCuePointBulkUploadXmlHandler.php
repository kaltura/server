<?php
/**
 * Handles code cue point ingestion from XML bulk upload
 * @package plugins.codeCuePoint
 * @subpackage batch
 */
class CodeCuePointBulkUploadXmlHandler extends CuePointBulkUploadXmlHandler
{
	/**
	 * @var CodeCuePointBulkUploadXmlHandler
	 */
	protected static $instance;
	
	/**
	 * @return CodeCuePointBulkUploadXmlHandler
	 */
	public static function get()
	{
		if(!self::$instance)
			self::$instance = new CodeCuePointBulkUploadXmlHandler();
			
		return self::$instance;
	}
	
	/* (non-PHPdoc)
	 * @see CuePointBulkUploadXmlHandler::getNewInstance()
	 */
	protected function getNewInstance()
	{
		return new KalturaCodeCuePoint();
	}
	
	/* (non-PHPdoc)
	 * @see CuePointBulkUploadXmlHandler::parseCuePoint()
	 */
	protected function parseCuePoint(SimpleXMLElement $scene)
	{
		if($scene->getName() != 'scene-code-cue-point')
			return null;
			
		$cuePoint = parent::parseCuePoint($scene);
		if(!($cuePoint instanceof KalturaCodeCuePoint))
			return null;
		
		if(isset($scene->sceneEndTime))
			$cuePoint->endTime = kXml::timeToInteger($scene->sceneEndTime);
		if(isset($scene->code))
			$cuePoint->code = "$scene->code";
		if(isset($scene->description))
			$cuePoint->description = "$scene->description";
			
		return $cuePoint;
	}
}
