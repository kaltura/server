<?php
/**
* @package plugins.Vendor
* @subpackage zoom
*/

class kZoomFlowManager implements kObjectCreatedEventConsumer
{
	const MAP_NAME = 'vendor';
	const CONFIGURATION_PARAM_NAME = 'Zoom';
	
	
	/**
	 * @param BaseObject $object
	 * @return bool
	 */
	public function objectCreated (BaseObject $object)
	{
		/* @var $captionAsset $object CaptionAsset */
		/* @var $object CaptionAsset */
		$zoomConfiguration = kConf::get(self::CONFIGURATION_PARAM_NAME, self::MAP_NAME);
		$captionAsset = assetPeer::retrieveById($object->getId());
		$captionAsset->setAccuracy($zoomConfiguration['ZoomTranscriptionAccuracy']);
		$captionAsset->save();
		
		return true;
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