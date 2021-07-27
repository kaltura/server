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
		/* @var $captionAsset $object CaptionAsset */
		/* @var $object CaptionAsset */
		$zoomConfiguration = kConf::get(self::CONFIGURATION_PARAM_NAME, self::MAP_NAME);
		$captionAsset = CaptionAssetItemPeer::retrieveByAssetId($object->getId());
		$captionAsset->setAccuracy($zoomConfiguration['ZoomTranscriptionAccuracy']);
		$captionAsset->save();
		
		return true;
	}
	
	/**
	 * @param BaseObject $object
	 * @param array $modifiedColumns
	 * @return bool
	 */
	public function shouldConsumeChangedEvent (BaseObject $object, array $modifiedColumns)
	{
		if($object instanceof CaptionAsset && $object->getSource() === CaptionSource::ZOOM)
		{
			return true;
		}
	}
}