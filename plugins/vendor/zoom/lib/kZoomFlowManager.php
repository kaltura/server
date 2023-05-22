<?php
/**
 * @package plugins.Vendor
 * @subpackage zoom
 */

class kZoomFlowManager implements kObjectCreatedEventConsumer
{
	const ZOOM_PARAM = 'Zoom';
	
	
	/**
	 * @param BaseObject $object
	 * @return bool
	 */
	public function objectCreated (BaseObject $object)
	{
		/* @var $object CaptionAsset */
		$zoomConfiguration = kConf::get(self::ZOOM_PARAM, kConfMapNames::VENDOR);
		if ($zoomConfiguration)
		{
			$object->setAccuracy($zoomConfiguration['ZoomTranscriptionAccuracy']);
			$object->save();
			
			return true;
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * @param BaseObject $object
	 * @return bool
	 */
	public function shouldConsumeCreatedEvent (BaseObject $object)
	{
		if($object instanceof CaptionAsset && $object->getSource() == CaptionSource::ZOOM)
		{
			return true;
		}
	}
}
