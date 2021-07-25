<?php
/**
* @package plugins.Vendor
* @subpackage zoom
*/
class kZoomFlowManager implements kObjectChangedEventConsumer
{
	
	const MAP_NAME = 'vendor';
	const CONFIGURATION_PARAM_NAME = 'Zoom';
	
	/**
	 * @inheritDoc
	 */
	public function objectChanged(BaseObject $object, array $modifiedColumns)
	{
		/* @var $object CaptionAsset */
		if($object->getSource() === CaptionSource::ZOOM)
		{
			$zoomConfiguration = kConf::get(self::CONFIGURATION_PARAM_NAME, self::MAP_NAME);
			$object->setAccuracy($zoomConfiguration['ZoomTranscriptionAccuracy']);
		}
		return true;
	}
	
	/**
	 * @param BaseObject $object
	 * @param array $modifiedColumns
	 * @return bool
	 */
	public function shouldConsumeChangedEvent (BaseObject $object, array $modifiedColumns)
	{
		// TODO: Implement shouldConsumeChangedEvent() method.
	}
}