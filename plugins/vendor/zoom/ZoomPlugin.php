<?php
/**
 * @package plugins.Zoom
 */
class ZoomPlugin extends KalturaPlugin implements IKalturaEventConsumers
{
	const PLUGIN_NAME                         = 'Zoom';
	const EVENT_ZOOM_FLOW_MANAGER = 'kZoomFlowManager';
	
	public static function getEventConsumers()
	{
		return array(
			self::EVENT_ZOOM_FLOW_MANAGER
		);
	}
	
	/**
	 * @return string
	 */
	public static function getPluginName ()
	{
		return self::PLUGIN_NAME;
	}
}