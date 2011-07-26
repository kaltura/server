<?php
/**
 * Handles annotation ingestion from XML bulk upload
 * @package plugins.annotation
 * @subpackage batch
 */
class AnnotationBulkUploadXmlHandler extends CuePointBulkUploadXmlHandler
{
	/**
	 * @var AnnotationBulkUploadXmlHandler
	 */
	protected static $instance;
	
	/**
	 * @return AnnotationBulkUploadXmlHandler
	 */
	public static function get()
	{
		if(!self::$instance)
			self::$instance = new AnnotationBulkUploadXmlHandler(false);
			
		return self::$instance;
	}
	
	/* (non-PHPdoc)
	 * @see CuePointBulkUploadXmlHandler::getNewInstance()
	 */
	protected function getNewInstance()
	{
		return new KalturaAnnotation();
	}
	
	/* (non-PHPdoc)
	 * @see CuePointBulkUploadXmlHandler::parseCuePoint()
	 */
	protected function parseCuePoint(SimpleXMLElement $scene)
	{
		if($scene->getName() != 'scene-annotation')
			return null;
			
		$cuePoint = parent::parseCuePoint($scene);
		if(!($cuePoint instanceof KalturaAnnotation))
			return null;
		
		if(isset($scene->sceneEndTime))
			$cuePoint->endTime = kXml::timeToInteger($scene->sceneEndTime);
		if(isset($scene->sceneText))
			$cuePoint->text = $scene->sceneText;
			
		if(isset($scene->parentId))
			$cuePoint->parentId = $scene->parentId;
		elseif(isset($scene->parent))
			$cuePoint->parentId = $this->getCuePointId($scene->parent);
			
		return $cuePoint;
	}
	
	/**
	 * @param string $cuePointSystemName
	 * @return string
	 */
	private function getCuePointId($systemName)
	{
		$filter = new KalturaAnnotationFilter();
		$filter->entryIdEqual = $this->entryId;
		$filter->systemNameEqual = $systemName;
		
		$pager = new KalturaFilterPager();
		$pager->pageSize = 1;
		
		try
		{
			$cuePointListResponce = $this->cuePointPlugin->cuePoint->listAction($filter, $pager);
		}
		catch(Exception $e)
		{
			return null;
		}
		
		if($cuePointListResponce->totalCount && $cuePointListResponce->objects[0] instanceof KalturaAnnotation)
			return $cuePointListResponce->objects[0]->id;
			
		return null;
	}
}
