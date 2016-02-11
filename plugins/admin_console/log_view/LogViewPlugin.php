<?php
/**
 * Enable log view for admin-console entry investigation page
 * @package plugins.logView
 */
class LogViewPlugin extends KalturaPlugin implements IKalturaApplicationPartialView, IKalturaAdminConsolePages
{
	const PLUGIN_NAME = 'logView';

	/* (non-PHPdoc)
	 * @see IKalturaPlugin::getPluginName()
	 */
	public static function getPluginName()
	{
		return self::PLUGIN_NAME;
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaApplicationPartialView::getApplicationPartialViews()
	 */
	public static function getApplicationPartialViews($controller, $action)
	{
		if($controller == 'batch' && $action == 'entryInvestigation')
		{
			return array(
				new Kaltura_View_Helper_EntryInvestigateLogView(),
			);
		}
		
		return array();
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaAdminConsolePages::getApplicationPages()
	 */
	public static function getApplicationPages()
	{
		$KalturaInternalTools = array(
			new KalturaLogViewAction(),
			new KalturaObjectInvestigateLogAction(),
		);
		return $KalturaInternalTools;
	}
}
